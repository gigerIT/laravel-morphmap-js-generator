<?php
namespace gigerIT\LaravelMorphMapJsGenerator;

use Illuminate\Support\ServiceProvider;

class MorphMapJsGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\GenerateMorphMapJsCommand::class,
            ]);
        }
    }
}