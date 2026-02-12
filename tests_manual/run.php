<?php

// Include Bootstrap
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/UserTestManual.php';

// Simple Test Runner
echo "Starting Manual Tests (No Vendor)...\n\n";

$test = new UserTestManual();

try {
    // Setup
    $test->setUp();

    // Run Tests
    $test->testFindAll();
    $test->setUp();
    $test->testFindById();
    $test->setUp();
    $test->testWhere();
    $test->setUp();
    $test->testChainedWhere();
    $test->setUp();
    $test->testChainedUpdate();
    $test->setUp();
    $test->testChainedDelete();
    $test->setUp();
    $test->testJoin();
    $test->setUp();
    $test->testRawQuery();

    echo "\n--- View Tests ---\n";
    require_once __DIR__ . '/ViewTestManual.php';
    $viewTest = new ViewTestManual();

    $viewTest->setUp();
    $viewTest->testRenderSimple();
    $viewTest->setUp();
    $viewTest->testRenderWithData();
    $viewTest->setUp();
    $viewTest->testRenderLayout();

    echo "\nAll Manual Tests Passed! \u{1F680}\n";

} catch (Exception $e) {
    echo "FAIL\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
