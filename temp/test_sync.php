<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$contacts = \App\Models\Contact::get();
foreach ($contacts as $c) {
    echo $c->id . ' - central_id: ' . var_export($c->central_id, true) . PHP_EOL;
}
