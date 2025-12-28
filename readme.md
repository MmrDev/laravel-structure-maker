# LaravelStructureMaker

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

A Laravel 12 package to help you quickly generate structured service classes and other boilerplate code in your projects.

## Installation

Install via Composer:

```bash
composer require mmrdev/laravel-structure-maker
```

Publish stubs and config:

php artisan vendor:publish --tag=structure-maker-stubs
php artisan vendor:publish --tag=structure-maker-config

Usage

After installation, you can generate a service class using the Artisan command:

php artisan make:service UserService
php artisan make:service Admin/UserService


This will create a new service class under app/Services/ with the correct namespace.

Change log

All notable changes to this package will be documented in the changelog
.

Testing

Run tests with:

composer test

Contributing

Contributions are welcome! Please read the contributing guide
 before submitting issues or pull requests.

Security

If you discover any security issues, please contact me via email: mmrdev@example.com
. Do not use the issue tracker for sensitive security reports.

Credits

MmrDev

All contributors

License

This package is licensed under the MIT License. See the LICENSE
 file for details.
