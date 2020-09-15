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

use HyperfExt\Enum\Exceptions\InvalidEnumMemberException;

abstract class FlaggedEnum extends Enum
{
    const None = 0;

    /**
     * Construct a FlaggedEnum instance.
     *
     * @param \HyperfExt\Enum\Enum[]|int[] $flags
     */
    public function __construct($flags)
    {
        $this->key = null;
        $this->description = null;

        if (is_array($flags)) {
            $this->setFlags($flags);
        } else {
            try {
                parent::__construct($flags);
            } catch (InvalidEnumMemberException $exception) {
                $this->value = $flags;
            }
        }
    }

    /**
     * Return a FlaggedEnum instance with defined flags.
     *
     * @param \HyperfExt\Enum\Enum[]|int[] $flags
     *
     * @throws \HyperfExt\Enum\Exceptions\InvalidEnumMemberException
     */
    public static function flags($flags): self
    {
        return static::fromValue($flags);
    }

    /**
     * Set the flags for the enum to the given array of flags.
     *
     * @param \HyperfExt\Enum\Enum[]|int[] $flags
     */
    public function setFlags(array $flags): self
    {
        $this->value = array_reduce($flags, function ($carry, $flag) {
            return $carry | static::fromValue($flag)->value;
        }, 0);

        return $this;
    }

    /**
     * Add the given flag to the enum.
     *
     * @param \HyperfExt\Enum\Enum|int $flag
     */
    public function addFlag($flag): self
    {
        $this->value |= static::fromValue($flag)->value;

        return $this;
    }

    /**
     * Add the given flags to the enum.
     *
     * @param \HyperfExt\Enum\Enum[]|int[] $flags
     */
    public function addFlags(array $flags): self
    {
        array_map(function ($flag) {
            $this->addFlag($flag);
        }, $flags);

        return $this;
    }

    /**
     * Remove the given flag from the enum.
     *
     * @param \HyperfExt\Enum\Enum|int $flag
     */
    public function removeFlag($flag): self
    {
        $this->value &= ~static::fromValue($flag)->value;

        return $this;
    }

    /**
     * Remove the given flags from the enum.
     *
     * @param \HyperfExt\Enum\Enum[]|int[] $flags
     */
    public function removeFlags(array $flags): self
    {
        array_map(function ($flag) {
            $this->removeFlag($flag);
        }, $flags);

        return $this;
    }

    /**
     * Check if the enum has the specified flag.
     *
     * @param \HyperfExt\Enum\Enum|int $flag
     */
    public function hasFlag($flag): bool
    {
        $flagValue = static::fromValue($flag)->value;

        if ($flagValue === 0) {
            return false;
        }

        return ($flagValue & $this->value) === $flagValue;
    }

    /**
     * Check if the enum has all of the specified flags.
     *
     * @param \HyperfExt\Enum\Enum[]|int[] $flags
     */
    public function hasFlags(array $flags): bool
    {
        foreach ($flags as $flag) {
            if (! static::hasFlag($flag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the enum does not have the specified flag.
     *
     * @param \HyperfExt\Enum\Enum|int $flag
     */
    public function notHasFlag($flag): bool
    {
        return ! $this->hasFlag($flag);
    }

    /**
     * Check if the enum doesn't have any of the specified flags.
     *
     * @param \HyperfExt\Enum\Enum[]|int[] $flags
     */
    public function notHasFlags(array $flags): bool
    {
        foreach ($flags as $flag) {
            if (! static::notHasFlag($flag)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the flags as an array of instances.
     *
     * @return \HyperfExt\Enum\Enum[]
     */
    public function getFlags(): array
    {
        $members = static::getInstances();
        $flags = [];

        foreach ($members as $member) {
            if ($this->hasFlag($member)) {
                $flags[] = $member;
            }
        }

        return $flags;
    }

    /**
     * Check if there are multiple flags set on the enum.
     */
    public function hasMultipleFlags(): bool
    {
        return ($this->value & ($this->value - 1)) !== 0;
    }

    /**
     * Get the bitmask for the enum.
     */
    public function getBitmask(): int
    {
        return (int) decbin($this->value);
    }
}
