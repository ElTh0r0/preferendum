# Changelog

## [0.8.1] (2025-08-03)

- Bug fixes:
  - Updating the expiry date
  - Changing an existing entry with limited choices
- Fix many HTML warnings
- Update logo
- Configurable default theme
- Fixing CakePHP related deprecations
- Update to latest CakePHP 5.2.x
- Align with latest CakePHP app skeleton
- phpcs code corrections

## [0.8.0] (2024-11-10)

- Migrate from CakePHP 4.5 to 5.1
- PHP 7 support was dropped; minimum PHP 8.1 required (as well as MySQL 5.7+, MariaDB 10.1+, PostgreSQL 9.6+)
- Fixing CakePHP related deprecations
- phpcs code corrections

## [0.7.1] (2024-08-28)

- Add max. entries to CSV export
- Prevent value manipulation if max. number of entries is used
- Update to latest CakePHP 4.5.x

## [0.7.0] (2024-06-12)

- Add option to restrict number of entries per option (opt_MaxEntriesPerOption)
- Add 'No' to existing entries when adding new choice
- Add tooltip to choice cells
- Increase max choice length to 50
- Update to latest CakePHP 4.5.x

## [0.6.0] (2024-02-23)

- Option to toggle between light/dark theme (toggleTheme)
- Option to send personal link by email (opt_SendChangeEntryLink)
- Option to send password reset for back-end users (sendBackendUserPwReset)
- Refactor back-end user management
- Small UI fixes

## [0.5.0] (2023-12-29)

- Adding user roles and user management
- Let users change their own entry through personal link
- Protect poll with a password
- Allow poll creation for admin/polladmin users only
- Globally enable admin links and globally disable comments
- Enable comments per poll
- Lock poll automatically on expiry date
- Create anonymous poll
- Change existing choice and choices order
- Search for poll name and user name in admin interface
- Download poll result as CSV
- Include expired polls in cronjob cleanup and optionally trigger cleanup manually
- Extended admin interface with icons for poll options
- Send email with poll links to poll admin
- Add PostgreSQL support
- Fundamental DB table changes with proper table constraints, heavy controller reconstructions/refactoring
- Many fixes: Fixing pagination, prevent field manipulations, PHP deprecations, CakePHP deprecations, minor bug fixes
- Bump clipboard.js, Datepicker, jquery version
- Update to latest CakePHP 4.5.x

## [0.4.0] (2022-05-01)

- Add option to hide poll results for the users
- Add icons for Admin page
- Update CakePHP to v4.3

## [0.3.0] (2020-10-04)

- Rebranded release as "PREFERendum" (formerly known as "Sprudel-ng").
- Same functionality / features as v0.2.0

## [0.2.0] (2020-09-19)

- Option to receive email after new entry and/or comment.

## [0.1.0] (2020-09-10)

- Initial pre-release


[0.8.1]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.8.1
[0.8.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.8.0
[0.7.1]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.7.1
[0.7.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.7.0
[0.6.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.6.0
[0.5.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.5.0
[0.4.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.4.0
[0.3.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.3.0
[0.2.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.2.0
[0.1.0]: https://github.com/ElTh0r0/preferendum/releases/tag/v0.1.0
