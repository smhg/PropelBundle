<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\ArgumentResolver;

use Propel\Bundle\PropelBundle\Attribute\MapModel;
use Propel\Bundle\PropelBundle\Util\PropelInflector;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\PropelQuery;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Resolves controller arguments type-hinted with a model class.
 *
 * The object is looked up by primary key when a route parameter matches it,
 * otherwise by all other route parameters that match a column. Use the
 * {@see MapModel} attribute to configure the lookup.
 *
 * @author Jérémie Augustin <jeremie.augustin@pixel-cookers.com>
 */
final class PropelValueResolver implements ValueResolverInterface
{
    /**
     * @return iterable<ActiveRecordInterface|null>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $class = $argument->getType();
        if (null === $class || !is_subclass_of($class, ActiveRecordInterface::class)) {
            return [];
        }

        $options = $this->getOptions($request, $argument);
        if (null === $options) {
            return [];
        }

        $query = $this->createQuery($class, $options);

        if (null !== $options->queryMethod && method_exists($query, $options->queryMethod)) {
            $object = $query->{$options->queryMethod}($request->attributes);
        } else {
            $object = $this->find($query, $request, $argument, $options);
            if (false === $object) {
                return [];
            }
        }

        if (null === $object && !$argument->isNullable()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        return [$object];
    }

    /**
     * Returns null when the argument does not opt in, through #[MapModel] or propel_converter route defaults.
     */
    private function getOptions(Request $request, ArgumentMetadata $argument): ?MapModel
    {
        $attribute = $argument->getAttributesOfType(MapModel::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;
        if ($attribute instanceof MapModel) {
            return $attribute;
        }

        // Options can also be passed as route defaults
        $options = $request->attributes->all('propel_converter')[$argument->getName()] ?? null;
        if (null === $options) {
            return null;
        }

        return new MapModel(
            $options['mapping'] ?? null,
            $options['exclude'] ?? [],
            $options['with'] ?? [],
            $options['query_method'] ?? null,
        );
    }

    /**
     * Returns false when the request holds nothing to look the object up by.
     *
     */
    private function find(ModelCriteria $query, Request $request, ArgumentMetadata $argument, MapModel $options): ActiveRecordInterface|null|false
    {
        $pkColumns = $query->getTableMap()->getPrimaryKeys();
        $pkColumn = 1 === count($pkColumns) ? reset($pkColumns) : null;
        $pk = $pkColumn ? strtolower($pkColumn->getName()) : null;

        $exclude = [];
        if (null !== $options->mapping) {
            $filters = [];
            foreach ($options->mapping as $routeParam => $column) {
                if (!$request->attributes->has($routeParam)) {
                    continue;
                }

                if ($pk === $column) {
                    $pk = $routeParam;
                } else {
                    $filters[$column] = $request->attributes->get($routeParam);
                }
            }
        } else {
            $exclude = $options->exclude;
            $filters = $request->attributes->all();
        }

        unset($filters[$argument->getName()]);

        $populates = [] !== $options->with;

        // Find by primary key
        if ($pkColumn && !in_array($pk, $exclude, true) && $request->attributes->has($pk)) {
            $value = $request->attributes->get($pk);

            return $populates ? $query->filterBy($pkColumn->getPhpName(), $value)->find()->getFirst() : $query->findPk($value);
        }

        // Find by the other parameters
        $hasCriteria = false;
        foreach ($filters as $column => $value) {
            if (in_array($column, $exclude, true)) {
                continue;
            }

            try {
                $query->{'filterBy'.PropelInflector::camelize($column)}($value);
                $hasCriteria = true;
            } catch (\Exception) {
                // not a column of this model
            }
        }

        if (!$hasCriteria) {
            return false;
        }

        return $populates ? $query->find()->getFirst() : $query->findOne();
    }

    private function createQuery(string $class, MapModel $options): ModelCriteria
    {
        $query = PropelQuery::from($class);

        foreach ((array) $options->with as $with) {
            if (is_array($with)) {
                if (2 !== count($with)) {
                    throw new \InvalidArgumentException(sprintf('Invalid "with" option "%s": use a relation name (e.g. "Book") or a relation name and join type (e.g. ["Book", "left join"]).', var_export($with, true)));
                }

                $query->joinWith($with[0], $this->getJoinType($with[1]));
            } else {
                $query->joinWith($with, Criteria::INNER_JOIN);
            }
        }

        return $query;
    }

    private function getJoinType(string $type): string
    {
        return match (trim(str_replace(['_', 'JOIN'], '', strtoupper($type)))) {
            'LEFT' => Criteria::LEFT_JOIN,
            'RIGHT' => Criteria::RIGHT_JOIN,
            'INNER' => Criteria::INNER_JOIN,
            default => throw new \InvalidArgumentException(sprintf('Invalid join type "%s": use "left", "right" or "inner".', $type)),
        };
    }
}
