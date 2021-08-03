<?php

declare(strict_types=1);
/**
 *
 * @Copyright chongqing JiuWan Technology Co., Ltd
 *
 */

namespace App\Concrete\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\ConstantsCollector;
use Hyperf\Constants\Exception\ConstantsException;
use Hyperf\Utils\Str;
use ReflectionClass;
use RuntimeException;

/**
 * @method static string|null getMessage($code)
 * @method static string|null getDescription($code)
 */
abstract class Enum extends AbstractConstants
{
    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed|string
     * @throws
     */
    final public static function __callStatic($name, $arguments)
    {
        if (!Str::startsWith($name, 'get')) {
            throw new ConstantsException('The function is not defined!');
        }

        if (!isset($arguments) || count($arguments) === 0) {
            throw new ConstantsException('The Code is required');
        }

        $code = $arguments[0];
        $name = strtolower(substr($name, 3));
        $class = get_called_class();

        $message = ConstantsCollector::getValue($class, $code, $name);

        array_shift($arguments);

//        $result = self::translate($message, $arguments);
//        // If the result of translate doesn't exist, the result is equal with message, so we will skip it.
//        if ($result && $result !== $message) {
//            return $result;
//        }

        $count = count($arguments);
        if ($count > 0) {
            return sprintf($message, ...(array) $arguments[0]);
        }

        return $message;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public static function has($name): bool
    {
        return array_key_exists($name, static::all());
    }

    /**
     * @throws
     */
    public static function all(): array
    {
        static $cache = [];
        $class = static::class;
        if (!isset($cache[$class])) {
            $reflect = new ReflectionClass($class);
            $cache[$class] = $reflect->getConstants();
        }
        return $cache[$class];
    }

    /**
     * @param $value
     */
    public static function exist($value, ?bool $strict = false): bool
    {
        return in_array($value, static::all(), $strict);
    }

    public static function snakeAll(): array
    {
        $data = [];
        foreach (static::all() as $code => $value) {
            $data[Str::snake($code)] = $value;
        }
        return $data;
    }

    public static function map(callable $call): array
    {
        return array_map($call, static::all());
    }
    /**
     * @param $element
     *
     * @return int
     * @throws
     */
    public static function guess($element): int
    {
        if (is_numeric($element)) {
            self::getName((int) $element);
            return (int) $element;
        }
        return self::getCode($element);
    }

    /**
     * @throws
     */
    public static function getName(int $code): string
    {
        foreach (static::all() as $name => $v) {
            if ($code === $v) {
                return $name;
            }
        }
        throw new RuntimeException(sprintf('enum %s code %s not found',
            static::class, $code));
    }

    /**
     * @param string $name
     *
     * @return int
     * @throws RuntimeException
     */
    public static function getCode(string $name): int
    {
        $name = Str::studly($name);
        $all = static::all();
        if (isset($all[$name])) {
            return $all[$name];
        }
        throw new RuntimeException(sprintf(
            'enum %s name %s not found',
            static::class,
            $name
        ));
    }
}
