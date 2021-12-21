# package-info

Verifying requirements of composer packages of a GitHub organization

## Installation

Clone the repository and install dependencies.

```bash
git clone git@github.com:arueckauer/package-info.git
cd package-info
composer i
```

## Configuration

The application comes with sample configurations for various PHP framework organizations. Copy one of the `config\autoload\local-*.dist` files to `config\autoload\local.php` . Or alternatively create your own configuration based on any of the sample configurations.

Place your personal access token in `github_api_token` and provide a `cache_file_path`.

**Configuration options**

- `github_api_token` Your personal access tokens (see GitHub / Settings / [Developer settings](https://github.com/settings/tokens))
- `organizations` An array of organization names whose repositories will be checked.
- `ignore_repositories` An array of package names, that will be ignored and thus not checked.
- `ignore_branches` An array of branch names, that will be ignored and thus not checked.
- `cache_file_path` Location of the cache path.

## Commands

There are three commands available.

**Build cache**

This will be the first step. To not repeatedly fetch all information from GitHub, package information is cached. To build the cache, execute the following command. Depending on the size of the organization, this may take a while.

```bash
php bin/console cache:build
```

**Show information for a single package**

This command shows information such as available heads (branches, releases and pull requests) for a single package.

```bash
php bin/console get <package-name>
```

**Check requirements**

The check command performs the actual check against given requirements.

Examples:

To check which repositories are supporting PHP 8.1, run the following command.

```bash
php bin/console check check -r php:^8.1
```

The results of the previous command include branches and pull requests. To check only releases, run the following command.

```bash
php bin/console check check -r php:^8.1 -t release
```

To include only repositories of a specific organization, run the following command.

```bash
php bin/console check check -r php:^8.1 -vendor mezzio
```

For a complete list of options, execute the following command.

```bash
php bin/console help check
```
