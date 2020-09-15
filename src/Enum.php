<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/enum.
 *
 * @link     https://github.com/hyperf-ext/enum
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/enum/blob/master/LICENSE
 */
namespace HyperfExt\Enum;

use Hyperf\Contract\Castable;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Str;
use Hyperf\Utils\Traits\Macroable;
use HyperfExt\Enum\Casts\EnumCast;
use HyperfExt\Enum\Contracts\EnumInterface;
use HyperfExt\Enum\Contracts\LocalizedEnum;
use HyperfExt\Enum\Exceptions\InvalidEnumKeyException;
use HyperfExt\Enum\Exceptions\InvalidEnumMemberException;
use JsonSerializable;
use ReflectionClass;

abstract class Enum implements EnumInterface, Castable, Arrayable, JsonSerializable
{
    use Macroable {
        __callStatic as macroCallStatic;
        __call as macroCall;
    }

    /**
     * The value of one of the enum members.
     *
     * @var mixed
     */
    public $value;

    /**
     * The key of one of the enum members.
     *
     * @var mixed
     */
    public $key;

    /**
     * The description of one of the enum members.
     *
     * @var mixed
     */
    public $description;

    /**
     * Constants cache.
     *
     * @var array
     */
    protected static $constCacheArray = [];

    /**
     * Construct an Enum instance.
     *
     * @param mixed $enumValue
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     */
    public function __construct($enumValue)
    {
        if (! static::hasValue($enumValue)) {
            throw new InvalidEnumMemberException($enumValue, $this);
        }

        $this->value = $enumValue;
        $this->key = static::getKey($enumValue);
        $this->description = static::getDescription($enumValue);
    }

    /**
     * Attempt to instantiate an enum by calling the enum key as a static method.
     *
     * This function defers to the macroable __callStatic function if a macro is found using the static method called.
     *
     * @param mixed $parameters
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumKeyException|\HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     * @return mixed
     */
    public static function __callStatic(string $method, $parameters)
    {
        if (static::hasMacro($method)) {
            return static::macroCallStatic($method, $parameters);
        }

        return static::fromKey($method);
    }

    /**
     * Delegate magic method calls to macro's or the static call.
     *
     * While it is not typical to use the magic instantiation dynamically, it may happen
     * incidentally when calling the instantiation in an instance method of itself.
     * Even when using the `static::KEY()` syntax, PHP still interprets this is a call to
     * an instance method when it happens inside of an instance method of the same class.
     *
     * @param mixed $parameters
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumKeyException|\HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     * @return mixed
     */
    public function __call(string $method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return self::__callStatic($method, $parameters);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Make a new instance from an enum value.
     *
     * @param mixed $enumValue
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     * @return static
     */
    public static function fromValue($enumValue): self
    {
        if ($enumValue instanceof static) {
            return $enumValue;
        }

        return new static($enumValue);
    }

    /**
     * Make an enum instance from a given key.
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumKeyException|\HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     * @return static
     */
    public static function fromKey(string $key): self
    {
        if (static::hasKey($key)) {
            $enumValue = static::getValue($key);
            return new static($enumValue);
        }

        throw new InvalidEnumKeyException($key, static::class);
    }

    /**
     * Checks if this instance is equal to the given enum instance or value.
     *
     * @param mixed|static $enumValue
     */
    public function is($enumValue): bool
    {
        if ($enumValue instanceof static) {
            return $this->value === $enumValue->value;
        }

        return $this->value === $enumValue;
    }

    /**
     * Checks if this instance is not equal to the given enum instance or value.
     *
     * @param mixed|static $enumValue
     */
    public function isNot($enumValue): bool
    {
        return ! $this->is($enumValue);
    }

    /**
     * Checks if a matching enum instance or value is in the given array.
     *
     * @param mixed[]|static[] $values
     */
    public function in(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->is($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return instances of all the contained values.
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     * @return static[]
     */
    public static function getInstances(): array
    {
        return array_map(
            function ($constantValue) {
                return new static($constantValue);
            },
            static::getConstants()
        );
    }

    /**
     * Attempt to instantiate a new Enum using the given key or value.
     *
     * @param mixed $enumKeyOrValue
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     * @return null|static
     */
    public static function coerce($enumKeyOrValue): ?self
    {
        if ($enumKeyOrValue === null) {
            return null;
        }

        if (static::hasValue($enumKeyOrValue)) {
            return static::fromValue($enumKeyOrValue);
        }

        if (is_string($enumKeyOrValue) && static::hasKey($enumKeyOrValue)) {
            $enumValue = static::getValue($enumKeyOrValue);
            return new static($enumValue);
        }

        return null;
    }

    /**
     * Get all of the enum keys.
     */
    public static function getKeys(): array
    {
        return array_keys(static::getConstants());
    }

    /**
     * Get all of the enum values.
     */
    public static function getValues(): array
    {
        return array_values(static::getConstants());
    }

    /**
     * Get the key for a single enum value.
     *
     * @param mixed $value
     */
    public static function getKey($value): string
    {
        return array_search($value, static::getConstants(), true);
    }

    /**
     * Get the value for a single enum key.
     *
     * @return mixed
     */
    public static function getValue(string $key)
    {
        return static::getConstants()[$key];
    }

    /**
     * Get the description for an enum value.
     *
     * @param mixed $value
     */
    public static function getDescription($value): string
    {
        return
            static::getLocalizedDescription($value) ??
            static::getFriendlyKeyName(static::getKey($value));
    }

    /**
     * Get a random key from the enum.
     */
    public static function getRandomKey(): string
    {
        $keys = static::getKeys();

        return $keys[array_rand($keys)];
    }

    /**
     * Get a random value from the enum.
     *
     * @return mixed
     */
    public static function getRandomValue()
    {
        $values = static::getValues();

        return $values[array_rand($values)];
    }

    /**
     * Get a random instance of the enum.
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     */
    public static function getRandomInstance(): self
    {
        return new static(static::getRandomValue());
    }

    /**
     * Return the enum as an array.
     *
     * [string $key => mixed $value]
     *
     * @return array
     */
    public static function asArray()
    {
        return static::getConstants();
    }

    /**
     * Get the enum as an array formatted for a select.
     *
     * [mixed $value => string description]
     */
    public static function asSelectArray(): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $key => $value) {
            $selectArray[$value] = static::getDescription($value);
        }

        return $selectArray;
    }

    /**
     * Check that the enum contains a specific key.
     */
    public static function hasKey(string $key): bool
    {
        return in_array($key, static::getKeys(), true);
    }

    /**
     * Check that the enum contains a specific value.
     *
     * @param mixed $value
     * @param bool $strict (Optional, defaults to True)
     */
    public static function hasValue($value, bool $strict = true): bool
    {
        $validValues = static::getValues();

        if ($strict) {
            return in_array($value, $validValues, true);
        }

        return in_array((string) $value, array_map('strval', $validValues), true);
    }

    /**
     * Get the default localization key.
     */
    public static function getLocalizationKey(): string
    {
        return 'enum.' . static::class;
    }

    /**
     * Cast values loaded from the database before constructing an enum from them.
     *
     * You may need to overwrite this when using string values that are returned
     * from a raw database query or a similar data source.
     *
     * @param mixed $value A raw value that may have any native type
     * @return mixed The value cast into the type this enum expects
     */
    public static function parseDatabase($value)
    {
        return $value;
    }

    /**
     * Transform value from the enum instance before it's persisted to the database.
     *
     * You may need to overwrite this when using a database that expects a different
     * type to that used internally in your enum.
     *
     * @param mixed $value A raw value that may have any native type
     * @return mixed The value cast into the type this database expects
     */
    public static function serializeDatabase($value)
    {
        return $value;
    }

    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @return string
     * @return \Hyperf\Contract\CastsAttributes|\Hyperf\Contract\CastsInboundAttributes|string
     */
    public static function castUsing()
    {
        return new EnumCast(static::class);
    }

    /**
     * Transform the enum instance when it's converted to an array.
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'description' => $this->description,
        ];
    }

    /**
     * Transform the enum when it's passed through json_encode.
     */
    public function jsonSerialize()
    {
        return $this->value;
    }

    /**
     * Get all of the constants defined on the class.
     */
    protected static function getConstants(): array
    {
        $calledClass = get_called_class();

        if (! array_key_exists($calledClass, static::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            static::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return static::$constCacheArray[$calledClass];
    }

    /**
     * Get the localized description of a value.
     *
     * This works only if localization is enabled
     * for the enum and if the key exists in the lang file.
     *
     * @param mixed $value
     */
    protected static function getLocalizedDescription($value): ?string
    {
        if (static::isLocalizable()) {
            $localizedStringKey = static::getLocalizationKey() . '.' . $value;

            $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);
            if ($translator->has($localizedStringKey)) {
                return $translator->trans($localizedStringKey);
            }
        }

        return null;
    }

    /**
     * Transform the key name into a friendly, formatted version.
     */
    protected static function getFriendlyKeyName(string $key): string
    {
        if (ctype_upper(str_replace('_', '', $key))) {
            $key = strtolower($key);
        }

        return ucfirst(str_replace('_', ' ', Str::snake($key)));
    }

    /**
     * Check that the enum implements the LocalizedEnum interface.
     */
    protected static function isLocalizable(): bool
    {
        return isset(class_implements(static::class)[LocalizedEnum::class]);
    }
}
