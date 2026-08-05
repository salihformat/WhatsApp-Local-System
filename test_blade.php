<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$compiler = app('blade.compiler');
$compiled = $compiler->compileString(file_get_contents('resources/views/conversations/show.blade.php'));
file_put_contents('compiled_blade_test.php', $compiled);
