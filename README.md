# Laravel Route Docs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mikegarde/laravel-route-docs.svg?style=flat-square)](https://packagist.org/packages/mikegarde/laravel-route-docs)
[![codecov](https://codecov.io/gh/mikegarde/laravel-route-docs/branch/main/graph/badge.svg)](https://codecov.io/gh/mikegarde/laravel-route-docs)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mikegarde/laravel-route-docs/run-tests.yml?branch=main&label=tests)](https://github.com/mikegarde/laravel-route-docs/actions)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](LICENSE)

A Laravel package that uses PHP attributes to document routes, generate readable route listings, and export OpenAPI or Postman definitions.

---

## Features

- Document routes directly using PHP attributes
- Validate route documentation in your CI/CD pipeline
- Includes CLI tooling for discovery and inspection

### TODO:
- Add request parameters
- Add response schemas
- Export route definitions as JSON, OpenAPI, or Postman collections

---

## Installation

```bash
composer require mikegarde/laravel-route-docs --dev
```

## Usage
Annotate your controller methods using custom attributes to describe your API:

```php
use RouteDocs\Attributes\get;

class ItemController
{
    #[get('/items', name: 'items.index')]
    public function index()
    {
        return Item::all();
    }
}
```

Then run:

```bash
php artisan route:docs
```

You’ll get a structured view of your documented routes.

## Validate Route Attributes in CI/CD

You can validate that all routes have correct and complete attribute annotations:

```bash
php artisan route:docs:validate
```

This will return non-zero exit codes on failure, making it CI-friendly.
