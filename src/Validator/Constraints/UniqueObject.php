<?php

/**
 * This file is part of the PropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Propel\Bundle\PropelBundle\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Constraint for the Unique Object validator
 *
 * @author Maxime AILLOUD <maxime.ailloud@gmail.com>
 * @author Marek Kalnik <marekk@theodo.fr>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class UniqueObject extends Constraint
{
    public string $message = 'A {{ object_class }} object already exists with {{ fields }}';

    /**
     * Used to merge multiple fields in the message
     */
    public string $messageFieldSeparator = ' and ';

    /**
     * @var string[]
     */
    public array $fields;

    /**
     * Used to set the path where the error will be attached, default is the first field.
     */
    public ?string $errorPath = null;

    /**
     * @param string|string[]|array<string, mixed> $fields the field(s), or an array of options (deprecated)
     * @param string[]|null                        $groups
     */
    #[HasNamedArguments]
    public function __construct(
        string|array $fields,
        ?string $message = null,
        ?string $messageFieldSeparator = null,
        ?string $errorPath = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        if (\is_array($fields) && !array_is_list($fields)) {
            trigger_deprecation('perplorm/perpl-bundle', '1.0', 'Passing an array of options to "%s" is deprecated, use named arguments instead.', __CLASS__);

            $options = $fields;
            if ($unknown = array_diff(array_keys($options), ['fields', 'message', 'messageFieldSeparator', 'errorPath', 'groups', 'payload'])) {
                throw new ConstraintDefinitionException(sprintf('The options "%s" do not exist in constraint "%s".', implode('", "', $unknown), __CLASS__));
            }

            $fields = $options['fields'] ?? [];
            $message = $options['message'] ?? $message;
            $messageFieldSeparator = $options['messageFieldSeparator'] ?? $messageFieldSeparator;
            $errorPath = $options['errorPath'] ?? $errorPath;
            $groups = $options['groups'] ?? $groups;
            $payload = $options['payload'] ?? $payload;
        }

        parent::__construct(null, $groups, $payload);

        $this->fields = (array) $fields;
        $this->message = $message ?? $this->message;
        $this->messageFieldSeparator = $messageFieldSeparator ?? $this->messageFieldSeparator;
        $this->errorPath = $errorPath;

        if ([] === $this->fields) {
            throw new ConstraintDefinitionException('At least one field must be specified.');
        }
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
