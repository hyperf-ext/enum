<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum\Traits;

use HyperfExt\Enum\Enum;

/**
 * @property array $enumCasts Map attribute names to enum classes.
 */
trait CastsEnums
{
    /**
     * Get a plain attribute (not a relationship).
     *
     * @param string $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if ($this->hasEnumCast($key)) {
            $value = $this->castToEnum($key, $value);
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($value !== null && $this->hasEnumCast($key)) {
            $enum = $this->enumCasts[$key];

            if ($value instanceof $enum) {
                $this->attributes[$key] = $value->value;
            } else {
                if ($this->hasCast($key)) {
                    $value = $this->castAttribute($key, $value);
                }

                $this->attributes[$key] = $enum::fromValue($value)->value;
            }

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Determine whether an attribute should be cast to a enum.
     *
     * @param string $key
     */
    public function hasEnumCast($key): bool
    {
        // This can happen if this trait is added to the model
        // but no enum casts have been added yet
        if ($this->enumCasts === null) {
            return false;
        }

        return array_key_exists($key, $this->enumCasts);
    }

    /**
     * Casts the given key to an enum instance.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function castToEnum($key, $value): ?Enum
    {
        /** @var \HyperfExt\Enum\Enum $enum */
        $enum = $this->enumCasts[$key];

        if ($value === null || $value instanceof Enum) {
            return $value;
        }
        return $enum::fromValue($value);
    }
}
