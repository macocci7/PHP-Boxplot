<?php

namespace Macocci7\PhpBoxplot\Traits;

trait JudgeTrait
{
    /**
     * judges if all items are integer or not
     * @param   array<mixed>    $items
     */
    public static function isIntAll(array $items): bool
    {
        if (empty($items)) {
            return false;
        }
        foreach ($items as $item) {
            if (!is_int($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * judges if the param is number
     * @param   mixed   $item
     */
    public static function isNumber(mixed $item): bool
    {
        return is_int($item) || is_float($item);
    }

    /**
     * judges if all items are number or not
     */
    public static function isNumbersAll(mixed $items): bool
    {
        if (!is_array($items)) {
            return false;
        }
        if (empty($items)) {
            return false;
        }
        foreach ($items as $item) {
            if (!self::isNumber($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * judges if the param is in '#rrggbb' format or not
     */
    public static function isColorCode(mixed $item): bool
    {
        if (!is_string($item)) {
            return false;
        }
        return preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $item) ? true : false;
    }

    /**
     * judges if all of params are colorcode or not
     * @param   string[]    $colors
     */
    public static function isColorCodeAll(array $colors): bool
    {
        if (empty($colors)) {
            return false;
        }
        foreach ($colors as $color) {
            if (!self::isColorCode($color)) {
                return false;
            }
        }
        return true;
    }

    /**
     * judges if type of $input is valid or not
     */
    public static function isValidType(mixed $input, string $defs): bool
    {
        $r = false;
        foreach (explode('|', $defs) as $def) {
            $r = $r || match ($def) {
                'int' => is_int($input),
                'float' => is_float($input),
                'string' => is_string($input),
                'bool' => is_bool($input),
                'array' => is_array($input),
                'null' => is_null($input),
                'number' => self::isNumber($input),
                'colorCode' => self::isColorCode($input),
                default => false,
            };
        }
        return $r;
    }

    /**
     * judges if the data is valid or not
     */
    public static function isValidData(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }
        if (empty($data)) {
            return false;
        }
        foreach ($data as $values) {
            if (!self::isNumbersAll($values)) {
                return false;
            }
        }
        return true;
    }

    /**
     * judges if the dataset is valid or not
     */
    public static function isValidDataset(mixed $dataset): bool
    {
        if (!is_array($dataset)) {
            return false;
        }
        if (empty($dataset)) {
            return false;
        }
        foreach ($dataset as $data) {
            if (!self::isValidData($data)) {
                return false;
            }
        }
        return true;
    }
}
