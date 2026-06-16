<?php

// Lightweight stubs to help static analyzers (Intelephense) recognize Pest globals
// These are only defined when the real functions do not exist (Pest provides them at runtime).

if (! function_exists('beforeEach')) {
    function beforeEach(callable $callback): void
    {
        // stub for static analysis
    }
}

if (! function_exists('test')) {
    function test(string $description, ?callable $callback = null)
    {
        // stub for static analysis
    }
}

if (! function_exists('it')) {
    function it(string $description, ?callable $callback = null)
    {
        // stub for static analysis
    }
}

if (! function_exists('expect')) {
    function expect($value = null)
    {
        return new class($value) {
            private $value;
            public function __construct($v) { $this->value = $v; }
            public function __call($name, $args) { return $this; }
            public function __get($name) { return $this; }
        };
    }
}

if (! function_exists('pest')) {
    function pest()
    {
        return null; // static analysis only
    }
}
