<?php
/**
 * Copyright Adrian Schubek (c) 2020.
 * https://adriansoftware.de
 */

namespace adrianschubek\Macro;


interface Macroable
{
    static function hasMacro(string $name): bool;

    static function mixin(object $obj, bool $force = false);

    static function macro(string $name, callable $fun);
}