# Project Overview

This Composer package provides a Laravel Artisan command that reads an
application's Eloquent morph map and generates frontend JavaScript or
TypeScript constants, keeping morph type values in sync across backend and
frontend code.

## Repository Structure

- `.github/workflows/` contains the Release Please workflow for pushes to
  `main`.
- `src/` contains the package source under the
  `gigerIT\LaravelMorphMapJsGenerator` PSR-4 namespace.
- `src/Console/` contains the `morphmap:generate-js` Artisan command.
- `README.md` documents installation, usage, generated output, and options.
- `CHANGELOG.md` records Release Please generated package changes.
- `composer.json` defines package metadata, dependencies, autoloading, and
  Laravel package auto-discovery.

## Build & Development Commands

Install the package in a Laravel application:

```bash
composer require gigerit/laravel-morphmap-js-generator --dev
```

Generate JavaScript constants in the consuming Laravel application:

```bash
php artisan morphmap:generate-js
```

Generate TypeScript constants in the consuming Laravel application:

```bash
php artisan morphmap:generate-js --ts
```

Generate into a custom output path:

```bash
php artisan morphmap:generate-js --path=resources/js/constants
```

Recommended consuming-app build hook from the README:

```json
{
  "scripts": {
    "prepare": "php artisan morphmap:generate-js --ts"
  }
}
```

> TODO: No local dependency-install command is declared in `composer.json`.
> TODO: No test command is declared in `composer.json`.
> TODO: No lint command or formatter command is declared in this repository.
> TODO: No type-check command is declared in this repository.
> TODO: No debug command is documented.
> TODO: No local deploy command is documented; releases run through GitHub
> Actions on pushes to `main`.

## Code Style & Conventions

- PHP source uses PSR-4 autoloading from
  `gigerIT\LaravelMorphMapJsGenerator\` to `src/`.
- The package service provider is
  `gigerIT\LaravelMorphMapJsGenerator\MorphMapJsGeneratorServiceProvider`.
- The public Artisan command signature is `morphmap:generate-js`.
- Command options are `--path=` and `--ts`; keep their names stable unless the
  README and release notes are updated at the same time.
- Generated frontend files are named `morphMap.js` or `morphMap.ts`.
- Generated constants use uppercase snake case, derived from the morph key or
  model basename for numeric morph keys.

> TODO: No PHP formatter, linter, editorconfig, or coding-standard config is
> present.
> TODO: No commit message template is present.

## Architecture Notes

```mermaid
flowchart TD
    A[Laravel package auto-discovery] --> B[MorphMapJsGeneratorServiceProvider]
    B --> C[morphmap:generate-js command]
    C --> D[Relation::morphMap()]
    D --> E[Generate MORPH_MAP and MORPH_MAP_MODELS]
    E --> F[base_path output directory]
    F --> G[morphMap.js or morphMap.ts]
```

Laravel discovers the service provider from `composer.json`. During console
execution, the provider registers `GenerateMorphMapJsCommand`. The command
reads the active morph map with `Relation::morphMap()`, fails with exit code
`1` when the map is empty, then writes a generated frontend constants file to
`resources/js` or the `--path` option.

The template contains `MORPH_MAP`, `MORPH_MAP_MODELS`, `getMorphMapModel`, and
`MorphMapValue`. Numeric morph keys keep numeric output values while their
constant names come from the model basename.

## Testing Strategy

> TODO: No test directory, test dependency, PHPUnit or Pest config, or CI test
> workflow is present.

Future tests should cover the command behavior around empty morph maps,
numeric keys, string keys, `--path`, `--ts`, generated filenames, directory
creation, and generated template contents.

## Security & Compliance

- The package is licensed as MIT in `composer.json`.
- Runtime dependencies are limited to PHP `^8.0` and Laravel Framework
  `^8.0|^9.0|^10.0|^11.0|^12.0`.
- `.gitignore` excludes `/vendor` and `/.idea`.
- The GitHub release workflow uses `${{ secrets.GITHUB_TOKEN }}`.
- Security reports are directed to `security@example.com` in the README.

> TODO: The README links to `LICENSE.md`, but no `LICENSE.md` file is present.
> TODO: No dependency scanning, static analysis, or vulnerability audit workflow
> is configured.

## Agent Guardrails

- Follow the gigerIT dependency rule: if a task is blocked by a bug or gap in a
  dependency owned by `gigerit` (`gigerit/*`, `@gigerit/*`, or an author,
  provider, or creator starting with `gigerit`), do not patch around it in the
  consuming project. Stop, report the package, version, blocker,
  expected/actual behavior, repro or code path, and suggested fix. Continue
  only after the dependency is fixed or the user explicitly approves a
  workaround.
- Do not edit generated consuming-app files such as `morphMap.js` or
  `morphMap.ts` as a package fix; change the command template instead.
- Do not modify `.github/workflows/release-please.yml` unless release
  automation is part of the task.
- Do not modify `CHANGELOG.md` by hand for ordinary code changes; it is release
  automation output.
- Do not commit `vendor/` or IDE files.
- Keep public package API changes coordinated across `composer.json`,
  `README.md`, source code, and release notes.
- Avoid destructive git commands such as `git reset --hard` unless the user
  explicitly asks for them.

## Extensibility Hooks

- Laravel package auto-discovery is configured in `composer.json` under
  `extra.laravel.providers`.
- The command can be extended through the `--path` and `--ts` options.
- The generated output location defaults to `resources/js`.
- The command depends on the host application's `Relation::morphMap()`
  configuration.

> TODO: No package config file, publishable config, environment variables, or
> feature flags are present.

## Further Reading

- [README.md](README.md)
- [CHANGELOG.md](CHANGELOG.md)
- [composer.json](composer.json)
- [.github/workflows/release-please.yml](.github/workflows/release-please.yml)

> TODO: No `docs/`, ADRs, or nested `AGENTS.md` files are present.
