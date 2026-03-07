# Export Project Structure

A Laravel package for exporting project structure files into text files.

## Installation

```bash
composer require makuruwan/export-project-structure
```

## Publish config

```bash
php artisan vendor:publish --tag=export-project-structure-config
```

## Usage

### Export everything into one file:

```bash
php artisan code:export --all
```

### Export everything into separate files:

```bash
php artisan code:export --all-separate
```

### Export only models:

```bash
php artisan code:export --models
```

### Export only controllers:

```bash
php artisan code:export --controllers
```

### Export only views:

```bash
php artisan code:export --views
```

### Export multiple selected sections:

```bash
php artisan code:export --models --controllers --views
```
