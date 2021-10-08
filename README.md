# package-info

Verifying requirements of composer packages of a GitHub organization

## Installation

```bash
composer require arueckauer/package-info
```

## Configuration

The application comes with sample configurations for various PHP framework organizations. Copy one of the `config\autoload\local-*.dist` files to `config\autoload\local.php` and place your personal access token in `github_api_token`.

You can adjust the configuration according to your wishes.

### Configuration options

- `github_api_token` Your personal access tokens (see GitHub / Settings / [Developer settings](https://github.com/settings/tokens))
- `organizations` An array of organization names whose repositories will be checked.
- `ignore_repositories` An array of package names, that will be ignored and thus not checked.
- `ignore_branches` An array of branche names, that will be ignored and thus not checked.
- `cache_file_path` Location of the cache path.
- `requirements` An array of requirements with package names as keys and versions as values.
- `development_requirements` An array of development requirements with package names as keys and versions as values.

## Commands

There are three commands available.

### 1. Build cache

To not repeatedly fetch all information from GitHub, package information is cached. To build the cache, execute

```bash
./vendor/bin/package-info cache:build
```

### 2. Show information for a single package

```bash
./vendor/bin/package-info get <package-name>
```

### 3. Show information for all packages

```bash
./vendor/bin/package-info list
```
