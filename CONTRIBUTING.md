# Contributing

Thank you for considering contributing to Laravel Custom Morph Map! Here's how you can help.

## Development Setup

1. Fork and clone the repository
2. Install dependencies:

```bash
composer install
```

3. Create a branch for your feature or fix:

```bash
git checkout -b feature/my-feature
```

## Running Tests

```bash
composer test
```

## Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) for code style. Please ensure your code follows the Laravel coding style before submitting a pull request:

```bash
# Check style
composer format:check

# Fix style automatically
composer format
```

## Static Analysis

This project uses [PHPStan](https://phpstan.org/) for static analysis:

```bash
composer analyse
```

## Submitting Changes

1. Ensure all tests pass
2. Ensure code style passes (`composer format:check`)
3. Ensure static analysis passes (`composer analyse`)
4. Write a clear commit message describing your changes
5. Submit a pull request

## Reporting Bugs

When reporting bugs, please include:

- PHP and Laravel version
- Steps to reproduce the issue
- Expected vs actual behavior
- Any relevant code snippets or error messages

## Security Vulnerabilities

If you discover a security vulnerability, please send an email to mucahitcucen@gmail.com instead of creating a public issue.
