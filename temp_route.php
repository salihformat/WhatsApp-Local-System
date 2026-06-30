<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$route = collect(Route::getRoutes())->firstWhere('uri', 'api/webhook/status');
dump($route->gatherMiddleware());
