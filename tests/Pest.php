<?php

use Tests\TestCase;

// Always run Feature and Unit tests
uses(TestCase::class)->in('Feature', 'Unit');

// Only include Browser tests if Playwright is available (installed via npm)
$hasPlaywright = false;
if (function_exists('shell_exec')) {
	$version = trim(@shell_exec('npx playwright --version 2>&1'));
	if ($version !== '') {
		$hasPlaywright = true;
	}
}

if (! $hasPlaywright) {
	$playwrightPath = __DIR__ . '/../node_modules/playwright';
	$playwrightBin = __DIR__ . '/../node_modules/.bin/playwright';
	if (file_exists($playwrightPath) || file_exists($playwrightBin)) {
		$hasPlaywright = true;
	}
}

if ($hasPlaywright) {
	uses(TestCase::class)->in('Browser');
}
