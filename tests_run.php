<?php

declare(strict_types=1);

use Tests\LogicTest;

require_once __DIR__.'/autoload.php';


function main(): void
{
    xdebug_set_filter( XDEBUG_FILTER_CODE_COVERAGE, XDEBUG_PATH_INCLUDE, [ __DIR__ . "/src/" ] );

    /** @var array<string> $xDebugInfo */
    $xDebugInfo = \xdebug_info('mode');

    $coverageModeEnabled = in_array('coverage', $xDebugInfo);
    $testClass           = new LogicTest();

    $coverageModeEnabled && xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

    echo PHP_EOL . PHP_EOL . '-------------- Test Cases Report ------------- ' . PHP_EOL;

    $reflection = new ReflectionClass(LogicTest::class);
    $methods    = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

    foreach ($methods as $method) {
        if (!str_starts_with($method->name, 'test')) {
            continue;
        }

        $testClass->{$method->name}();
    }

    if (!$coverageModeEnabled) {
        exit;
    }

    $data = xdebug_get_code_coverage();
    xdebug_stop_code_coverage();

    $coverageResult = [];
    foreach ($data as $filename => $lines) {
        $fileStat = [
            'codeLines' => 0,
            'covered'   => 0,
        ];

        foreach ($lines as $coverage) {
            //does not have executable code
            if (-2 === $coverage) {
                continue;
            }

            //not executed
            ++$fileStat['codeLines'];

            if (-1 === $coverage) {
                continue;
            }

            ++$fileStat['covered'];
        }

        $coverageResult[$filename] = $fileStat;
    }

    echo PHP_EOL . PHP_EOL . '-------------- Code Coverage Report ------------- ' . PHP_EOL;

    foreach ($coverageResult as $fileName => $fileStat) {
        $coveredLines = $fileStat['covered'];
        $codeLines    = $fileStat['codeLines'];
        echo $fileName . ' => ' .$coveredLines. '/' .$codeLines. ' = ' . number_format(($coveredLines/$codeLines) * 100, 2) . '%' . PHP_EOL;
    }
}

main();