<?php
/**
 * Copyright Adrian Schubek (c) 2020.
 * https://adriansoftware.de
 */

namespace adrianschubek\Macro;

use Closure;
use http\Exception\BadMethodCallException;
use ReflectionClass;
use ReflectionMethod;

trait HasMacros
{
    protected static array $macros = [];

    public static function mixin(object $obj, bool $force = false)
    {
        $methods = (new ReflectionClass($obj))->getMethods(
            ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
        );

        foreach ($methods as $m) {
            if ($force || !static::hasMacro($m->name)) {
                $m->setAccessible(true);
                static::macro($m->name, $m->invoke($obj));
            }
        }
    }

    public static function hasMacro(string $name): bool
    {
        return isset(static::$macros[$name]);
    }

    public static function macro(string $name, callable $fun)
    {
        static::$macros[$name] = $fun;
    }

    public static function __callStatic($name, $arguments)
    {
        if (!static::hasMacro($name)) {
            throw new BadMethodCallException(sprintf("Method %s::%s does not exist.", static::class, $name));
        }

        $macro = static::$macros[$name];

        if ($macro instanceof Closure) {
            return Closure::bind($macro, null, static::class)(...$arguments);
        }

        return ($macro)(...$arguments);
    }

    public function __call($name, $arguments)
    {
        if (!static::hasMacro($name)) {
            throw new BadMethodCallException(sprintf("Method %s::%s does not exist.", static::class, $name));
        }

        $macro = static::$macros[$name];

        if ($macro instanceof Closure) {
            return $macro->bindTo($this, static::class)(...$arguments);
        }

        return $macro(...$arguments);
    }
}