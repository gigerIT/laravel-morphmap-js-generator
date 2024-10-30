<?php

namespace gigerIT\LaravelMorphMapJsGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class GenerateMorphMapJsCommand extends Command
{
    protected $signature = 'morphmap:generate-js
                          {--path= : The path where the JavaScript file should be generated}
                          {--ts : Generate TypeScript file instead of JavaScript}';

    protected $description = 'Generate frontend JavaScript/TypeScript constants from Laravel morph map configuration';

    public function handle()
    {
        // Get the morph map directly from Laravel's Relation class
        $morphMap = Relation::morphMap();

        if (empty($morphMap)) {
            $this->error('No morph map found! Make sure morphMap is configured in your application.');
            return 1;
        }

        $path = $this->option('path') ?? 'resources/js';
        $isTypeScript = $this->option('ts');

        $template = $this->generateTemplate($morphMap, $isTypeScript);

        $fileName = $isTypeScript ? 'morphMap.ts' : 'morphMap.js';
        $fullPath = base_path($path . '/' . $fileName);

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $template);

        $this->info("Frontend morph map constants generated successfully at: {$fullPath}");
        $this->table(
            ['Key', 'Model'],
            collect($morphMap)->map(fn($model, $key) => [$key, $model])->toArray()
        );
    }

    protected function generateTemplate(array $morphMap, bool $isTypeScript): string
    {
        $constants = [];
        $models = [];

        foreach ($morphMap as $key => $modelClass) {
            $constantName = $this->generateConstantName($key, $modelClass);
            $modelName = class_basename($modelClass);

            if (is_numeric($key)) {
                $constants[] = "  {$constantName}: {$key}";
            } else {
                $constants[] = "  {$constantName}: '{$key}'";
            }

            $models[] = "  [MORPH_MAP.{$constantName}]: '{$modelName}'";
        }

        $typeAnnotation = $isTypeScript ? ' as const' : '';

        return <<<EOT
// This file is auto-generated. Do not edit it manually.
// Generated from Laravel's morphMap configuration on: {$this->getCurrentTimestamp()}

/**
 * Enum for Laravel morph map types
 * @enum {string|number}
 */
export const MORPH_MAP = {
{$this->formatArray($constants)}
}{$typeAnnotation};

/**
 * Mapping of morph map values to their corresponding model names
 */
export const MORPH_MAP_MODELS = {
{$this->formatArray($models)}
}{$typeAnnotation};

/**
 * Helper function to get model name from morph map value
 */
export const getMorphMapModel = (morphMap: keyof typeof MORPH_MAP): string => {
  return MORPH_MAP_MODELS[morphMap] || 'Unknown';
};

/**
 * Type for all possible morph map values
 */
export type MorphMapValue = typeof MORPH_MAP[keyof typeof MORPH_MAP];

EOT;
    }

    protected function generateConstantName($key, $modelClass): string
    {
        if (is_numeric($key)) {
            // For numeric keys, use the model name
            return Str::upper(Str::snake(class_basename($modelClass)));
        }

        return Str::upper(Str::snake($key));
    }

    protected function formatArray(array $items): string
    {
        return implode(",\n", $items);
    }

    protected function getCurrentTimestamp(): string
    {
        return now()->format('Y-m-d H:i:s');
    }
}