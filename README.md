# package-info
Verifying requirements of composer packages of a GitHub organization

## Installation

```bash
composer require arueckauer/package-info
```

## Configuration

Copy `config\autoload\local.php.dist` to `config\autoload\local.php` and place your personal access token in `github_api_token`.

The application is pre-configured with the packages of the Laminas organization. You can adjust the configuration according to your wishes.

### Configuration options

* `github_api_token` Your personal access tokens (see GitHub / Settings / [Developer settings](https://github.com/settings/tokens))
* `organizations` An array of organization names whose repositories will be checked.
* `ignore_repositories` An array of package names, that will be ignored and thus not checked.
* `ignore_branches` An array of branche names, that will be ignored and thus not checked.
* `cache_file_path` Location of the cache path.
* `requirements` An array of requirements with package names as keys and versions as values.
* `development_requirements` An array of development requirements with package names as keys and versions as values.

## Commands

There are three commands available.

### 1. Build cache

To not repeatedly fetch all information from GitHub, package information is cached. To build the cache, execute

```bash
php ./bin/console cache:build
```

### 2. Show information for a single package

```bash
php ./bin/console package-info:get <package-name>
```

### 3. Show information for all packages

```bash
php ./bin/console package-info:list
```
