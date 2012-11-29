<?php

/**
 * Running PHPUnit tests with generating coverage
 */

define('COVERAGE_DIR', realpath(__DIR__ . '/../../Data/') . DIRECTORY_SEPARATOR . 'Coverage');

$_SERVER['argv'][] = '--coverage-html';
$_SERVER['argv'][] = COVERAGE_DIR;

// run phpunit
require_once __DIR__ . '/phpunit.php';
