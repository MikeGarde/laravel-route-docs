# Development Guide for `laravel-route-docs`

This document outlines the local development workflow for building and testing the `laravel-route-docs` package, 
as well as integrating it into other Laravel projects before publishing.

---

## Project Setup

 - [Docker](https://www.docker.com/)
 - [Taskfile](https://taskfile.dev/)

### Setup & Install Dependencies

```bash
task build
```

---

## Running Tests

### PHPUnit

```bash
task test:unit
```

Or run tests against all versions of PHP:

```bash
task test:all
```

---

## Route Integration Tests with Laravel Kernel

- Tests extend `Tests\TestCase`, which boots a full Laravel app kernel using Testbench.
- Example route definitions are injected using `defineRoutes()`.

---

## Using This Package in Another Laravel Project (During Development)

If you're actively developing this package and want to test it **in another Laravel app** without publishing it yet:

### Option 1: Path Repository (Recommended)

In your consuming Laravel app's `composer.json`:

```json
"repositories": [
  {
    "type": "path",
    "url": "../path-to/laravel-route-docs"
  }
],
"require": {
  "mikegarde/laravel-route-docs": "*"
}
```

Then run:

```bash
composer update mikegarde/laravel-route-docs
```

> Changes in `laravel-route-docs` will be picked up live, no commit/push needed.

---

## Using Inside Docker (Path Setup)

If you're using Docker and the package is **outside** the app folder, mount both volumes in your `docker-compose.yml`:

```yaml
services:
  php:
    volumes:
      - ../your-laravel-app:/var/www/html
      - ../laravel-route-docs:/packages/laravel-route-docs
```

Then in the app's `composer.json`:

```json
"repositories": [
  {
    "type": "path",
    "url": "/packages/laravel-route-docs"
  }
]
```

Run this inside your container:

```bash
composer require mikegarde/laravel-route-docs:* --prefer-source
# And maybe also:
composer dump-autoload
php artisan ide-helper:generate
```

---

## Helpers and Polyfills

To support `base_path()` and `app_path()` in both Laravel and standalone mode:
- Polyfills are provided in `src/helpers.php`.
- Registered in `composer.json` under `autoload.files`.

---

## Useful Commands

- `php artisan route:docs` — show documented routes
- `php artisan route:docs:validate` — validate attribute/route consistency
- `composer test` — run all tests

---

## Notes

- The `examples/Http/Controllers` folder contains real attribute usage for demo and test purposes.

