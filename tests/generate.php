<?php

require __DIR__ . '/../vendor/autoload.php';

use gigerIT\LaravelMorphMapJsGenerator\MorphMapJsGeneratorServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Console\Application as Artisan;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Application;

$app = new Application($argv[1]);
$app->instance('config', new Repository());
$app->register(MorphMapJsGeneratorServiceProvider::class);
$app->boot();
$artisan = new Artisan($app, $app['events'], $app->version());

$cases = [
    'matching' => ['X' => 'App\\Models\\MatchingModel'],
    'values' => ['user' => 'App\\Models\\User', 7 => 'App\\Models\\Client'],
];

foreach ($cases as $name => $map) {
    Relation::morphMap($map, false);
    $exit = $artisan->call('morphmap:generate-js', ['--ts' => true, '--path' => $name]);
    if ($exit !== 0 || !is_file($argv[1] . '/' . $name . '/morphMap.ts')) {
        throw new RuntimeException('Generation failed: ' . $artisan->output());
    }
}

Relation::morphMap($cases['values'], false);
$exit = $artisan->call('morphmap:generate-js', ['--path' => 'values-js']);
if ($exit !== 0 || !is_file($argv[1] . '/values-js/morphMap.js')) {
    throw new RuntimeException('JavaScript generation failed: ' . $artisan->output());
}

Relation::morphMap([], false);
foreach ([false, true] as $isTypeScript) {
    $path = $isTypeScript ? 'empty-ts' : 'empty-js';
    $options = ['--path' => $path];
    if ($isTypeScript) {
        $options['--ts'] = true;
    }

    if ($artisan->call('morphmap:generate-js', $options) !== 1
        || file_exists($argv[1] . '/' . $path)) {
        throw new RuntimeException('An empty morph map must fail without writing output.');
    }
}
