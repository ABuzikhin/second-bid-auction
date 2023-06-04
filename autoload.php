<?php

declare(strict_types=1);

$classmap = [
    'App'   => __DIR__.'/src/',
    'Tests' => __DIR__.'/tests/',
];

spl_autoload_register(function(string $classname) use ($classmap) {
    $parts = \explode('\\', $classname);

    $namespace = \array_shift($parts);
    $classfile = \array_pop($parts) . '.php';

    if (!isset($classmap[$namespace])) {

        return;
    }

    $path = \implode(DIRECTORY_SEPARATOR, $parts);
    $file = $classmap[$namespace] . $path . DIRECTORY_SEPARATOR . $classfile;

    if (!\file_exists($file) && !\class_exists($classname)) {

        return;
    }

    require_once $file;
});

