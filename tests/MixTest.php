<?php

use adrianschubek\Macro\HasMacros;
use adrianschubek\Macro\Macroable;

it("works", function () {
    class Mix
    {
        public function sayBye(): callable
        {
            return function () {
                return "Bye!";
            };
        }
    }

    class Test implements Macroable
    {
        use HasMacros;

        public function sayHello()
        {
            return "Hello";
        }
    }

    Test::mixin(new Mix());

    assertSame("Bye!", (new Test())->sayBye());
});