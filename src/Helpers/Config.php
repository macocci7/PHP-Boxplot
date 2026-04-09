<?php

namespace Macocci7\PhpBoxplot\Helpers;

use Macocci7\PhpBoxplot\Traits\JudgeTrait;
use Nette\Neon\Neon;

/**
 * Config operator.
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Config
{
    use JudgeTrait;

    /**
     * @var mixed[] $conf
     */
    private static mixed $conf = [];

    /**
     * loads config from a file
     */
    public static function load(): void
    {
        $class = self::class();
        $cl = self::className($class);
        $path = __DIR__ . '/../../conf/' . $cl . '.neon';
        self::$conf[$class] = Neon::decodeFile($path);
    }

    /**
     * returns the fully qualified class name of the caller
     */
    public static function class(): string
    {
        return debug_backtrace()[2]['class'];
    }

    /**
     * returns just the class name splitted parent namespace
     */
    public static function className(string $class): string
    {
        $pos = strrpos($class, '\\');
        if ($pos) {
            return substr($class, $pos + 1);
        }
        return $class;
    }

    /**
     * returns config data
     */
    public static function get(?string $key = null): mixed
    {
        // get fully qualified class name of the caller
        $class = self::class();
        if (!self::$conf[$class]) {
            return null;
        }
        if (is_null($key)) {
            return self::$conf[$class];
        }
        $keys = explode('.', $key);
        $conf = self::$conf[$class];
        foreach ($keys as $k) {
            if (!isset($conf[$k])) {
                return null;
            }
            $conf = $conf[$k];
        }
        return $conf;
    }

    /**
     * filters config items
     * @param   string|mixed[]  $configResource
     * @return  mixed[]
     */
    public static function filter(string|array $configResource): array
    {
        $class = self::class();
        if (is_string($configResource)) {
            return self::filterFromFile($configResource, $class);
        }
        return self::filterFromArray($configResource, $class);
    }

    /**
     * returns valid config items from specified file
     * @return  mixed[]
     * @thrown  \Exception
     */
    private static function filterFromFile(string $path, string $class): array
    {
        if (strlen($path) === 0) {
            throw new \Exception("Specify valid filename.");
        }
        if (!is_readable($path)) {
            throw new \Exception("Cannot read file $path.");
        }
        $content = Neon::decodeFile($path);
        return self::filterFromArray($content, $class);
    }

    /**
     * returns valid config items from specified array
     * @param   mixed[] $content
     * @return  mixed[]
     * @thrown  \Exception
     */
    private static function filterFromArray(array $content, string $class): array
    {
        $conf = [];
        $validConfig = self::$conf[$class]['validConfig'];
        foreach ($validConfig as $key => $def) {
            if (array_key_exists($key, $content)) {
                if (self::isValidType($content[$key], $def['type'])) {
                    $conf[$key] = $content[$key];
                } else {
                    $message = $key . " must be type of " . $def['type'] . ".";
                    throw new \Exception($message);
                }
            }
        }
        return $conf;
    }
}
