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
use HyperfExt\Enum\FlaggedEnum;

class EnumValue implements Rule
{
    /**
     * The name of the rule.
     */
    protected $rule = 'enum_value';

    /**
     * @var \HyperfExt\Enum\Enum|string
     */
    protected $enumClass;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * Create a new rule instance.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $enumClass, bool $strict = true)
    {
        $this->enumClass = $enumClass;
        $this->strict = $strict;

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
        $strict = $this->strict ? 'true' : 'false';

        return "{$this->rule}:{$this->enumClass},{$strict}";
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param mixed $value
     */
    public function passes(string $attribute, $value): bool
    {
        if (is_subclass_of($this->enumClass, FlaggedEnum::class) && (is_integer($value) || ctype_digit($value))) {
            // Unset all possible flag values
            foreach ($this->enumClass::getValues() as $enumValue) {
                $value &= ~$enumValue;
            }
            // All bits should be unset
            return $value === 0;
        }
        return $this->enumClass::hasValue($value, $this->strict);
    }

    /**
     * Get the validation error message.
     *
     * @return array|string
     */
    public function message()
    {
        return __('enum.validation.enum_value');
    }
}
