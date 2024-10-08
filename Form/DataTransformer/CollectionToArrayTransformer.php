<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Propel\Bundle\PropelBundle\Form\DataTransformer;

use Propel\Runtime\Collection\ObjectCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * CollectionToArrayTransformer class.
 *
 * @author William Durand <william.durand1@gmail.com>
 * @author Pierre-Yves Lebecq <py.lebecq@gmail.com>
 *
 * @implements DataTransformerInterface<ObjectCollection, array>
 */
class CollectionToArrayTransformer implements DataTransformerInterface
{
    /**
     * @param $collection
     *
     * @return array<mixed>
     */
    public function transform($collection): array
    {
        if (null === $collection) {
            return array();
        }

        if (!$collection instanceof ObjectCollection) {
            throw new TransformationFailedException('Expected a \Propel\Runtime\Collection\ObjectCollection.');
        }

        return $collection->getData();
    }

    /**
     * @param array<mixed>|string|null $array
     *
     * @return ObjectCollection
     */
    public function reverseTransform($array): ObjectCollection
    {
        $collection = new ObjectCollection();

        if ('' === $array || null === $array) {
            return $collection;
        }

        if (!is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $collection->setData($array);

        return $collection;
    }
}
