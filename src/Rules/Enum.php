<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Rules;

use Hyperf\Validation\Contract\Rule;

class Enum implements Rule
{
    /**
     * The name of the rule.
     */
    protected $rule = 'enum';

    /**
     * @var \HyperfExt\Enum\Enum|string
     */
    protected $enumClass;

    /**
     * Create a new rule instance.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $enum)
    {
        $this->enumClass = $enum;

        if (! class_exists($this->enumClass)) {
            throw new \InvalidArgumentException("Cannot validate against the enum, the class {$this->enumClass} doesn't exist.");
        }
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     *
     * @see \Hyperf\Validation\ValidationRuleParser::parseParameters
     */
    public function __toString()
    {
        return "{$this->rule}:{$this->enumClass}";
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed $value
     */
    public function passes(string $attribute, $value): bool
    {
        return $value instanceof $this->enumClass;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string
     */
    public function message()
    {
        return __('enum.validation.enum');
    }
}
