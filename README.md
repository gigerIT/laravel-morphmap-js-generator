# Laravel Morph Map JS Generator

Generate frontend JavaScript/TypeScript constants from your Laravel application's morph map configuration. 
Keep your frontend morph types in sync with your Laravel models automatically.

## Installation

You can install the package via composer:

```bash
composer require gigerit/laravel-morphmap-js-generator --dev
```

The package will automatically register itself with Laravel.

## Usage

After installation, you can generate the JavaScript/TypeScript constants using the artisan command:

```bash
# Generate JavaScript file
php artisan morphmap:generate-js

# Generate TypeScript file
php artisan morphmap:generate-js --ts

# Specify custom output path
php artisan morphmap:generate-js --path=resources/js/constants
```

### Example

If your Laravel application has this morph map configuration:

```php
// App/Providers/AppServiceProvider.php

use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Relation::morphMap([
            'tenant' => Tenant::class,
            'user' => User::class,
            1 => Client::class,
            2 => Contact::class,
        ]);
    }
}
```

With `--ts`, the command generates these TypeScript constants:

```typescript
// morphMap.ts

export const MORPH_MAP = {
  TENANT: 'tenant',
  USER: 'user',
  CLIENT: 1,
  CONTACT: 2
} as const;

export const MORPH_MAP_MODELS = {
  [MORPH_MAP.TENANT]: 'Tenant',
  [MORPH_MAP.USER]: 'User',
  [MORPH_MAP.CLIENT]: 'Client',
  [MORPH_MAP.CONTACT]: 'Contact'
} as const;

export const getMorphMapModel = (morphMap: MorphMapValue): string => {
  return MORPH_MAP_MODELS[morphMap] || 'Unknown';
};

export type MorphMapValue = typeof MORPH_MAP[keyof typeof MORPH_MAP];
```

The helper accepts morph values such as `'user'` or `1`. Constant names such as
`'USER'` are not valid arguments. Untyped callers passing an unknown value retain
the `'Unknown'` runtime fallback.

### Using in Your Frontend

```typescript
import { MORPH_MAP, getMorphMapModel, type MorphMapValue } from './morphMap';

// Use constants
if (type === MORPH_MAP.TENANT) {
  // Handle tenant case
}

// Get model name
const modelName = getMorphMapModel(MORPH_MAP.CLIENT); // Returns 'Client'

// TypeScript support
function handleMorphable(type: MorphMapValue) {
  // Type-safe handling of morph types
}
```

## Features

- 🔄 Automatically syncs with Laravel's morph map configuration
- 📝 Generates JavaScript or TypeScript files
- 🎯 Type-safe with TypeScript support
- 🔍 Includes helper function for model name lookup
- 🎨 Converts numeric keys to readable constant names
- 📦 Zero configuration required
- 💻 Command output shows current mappings

## Command Options

| Option | Description | Default |
|--------|-------------|---------|
| `--ts` | Generate TypeScript instead of JavaScript | `false` |
| `--path` | Custom output path for generated file | `resources/js` |

## Best Practices

1. **Version Control**: Add the generated file to your version control system to track changes.
2. **Build Process**: Include the generation command in your build process to ensure sync:

```json
{
  "scripts": {
    "prepare": "php artisan morphmap:generate-js --ts"
  }
}
```

3. **Type Safety**: Use TypeScript for better type checking and IDE support.

## Development

Install PHP dependencies with `composer install` and the test compiler with
`npm ci`, then run `npm test`. The tests boot the package service provider,
execute `morphmap:generate-js --ts`, compile the generated files under strict
TypeScript settings, and execute the compiled consumers to verify lookups and
the runtime fallback. CI runs this against Laravel 12 and 13.

## Security

If you discover any security related issues, please email security@example.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.