# PREFERendum
## A flexible, self-hosted webapp for scheduling and polls

- Based on [sprudel](https://github.com/bkis/sprudel) and ported to [CakePHP](https://github.com/cakephp/cakephp)
- Clean, intuitive interface
- `yes`/`no`/`maybe` options
- Answer trend **visualization**
- Free entry of arbitrary **answer options or dates** (using the built-in date picker)
- Unique public links for sharing a poll
- One-click **clipboard copy** of the poll URL
- **Comments** section in every poll
- **Mini View** feature (for very big poll tables)
- **Customizable labels**: All the labels and texts can be set to custom strings, so you can even translate the interface to a language of your choice
- **Customizable colors**:
  - All the interface colors can easily be set to your preference (system-wide, that is, not for individual polls)
  - Toggle between light and dark color themes
- Optional:
  - **Poll admin links** to restrict poll management to author
  - Receive email after new entry and/or comment
  - Collect user contact information with a poll entry
  - Poll **administration interface** for managing all the polls on the server
  - Simple user administration for admin interface
  - Restrict poll creation to backend users only
  - Allow users to change their entry through a personal links
  - Make votes only visible for admin
  - Create anonymous votes (no user name stored/shown)
  - Lock poll automatically at a certain date
  - Define maximum number of entries per option
  - Protect poll by a password

![Screenshot](https://github.com/ElTh0r0/preferendum/assets/26674558/8b45b2f3-7587-4784-8e7a-9255364492d1)

## Requirements
- PHP 8.1 or higher (including intl and mbstring extension)
- One of the following databases:
  - MySql (5.7 or higher)
  - MariaDB (10.1 or higher)
  - PostgreSQL (9.6 or higher)

## Installation
- Create an empty MariaDB/MySql/PostgreSQL database on your server, note down the DB host address, user name and password
- Install option #1:
  - Download the latest PREFERendum release archive `preferendum_vX.X.X.zip` from [releases](https://github.com/ElTh0r0/preferendum/releases)
  - Extract the contents of the archive into a new directory (e.g. `preferendum` on your computer)
  - Take care of proper folder permissions, see [CakePHP permissions](https://book.cakephp.org/5/en/installation.html#permissions)
  - Copy `config/app_local.example.php` and rename it to `config/app_local.php`.
- Install option #2:
  - Download **source code** from latest release
  - Download [Composer](https://getcomposer.org/download/)
  - Run `composer install --no-dev`. The install command will download all needed dependencies.
- In `config/app_local.php` change the following entries:
  - Datasources\Default: Set host, driver ('**Mysql**' for MySql/MariaDB or '**Postgres**' for PostgreSQL) username, password, database according to your environment.
  - Security\Salt: Replace \_\_SALT\_\_ with an arbitrary string (will be done automatically, if composer install method is used).
- In `config/preferendum_features.php` configure the features as needed.
- Upload the `preferendum` directory to your web server (root-directory or somewhere else)
- Access `<your installation folder>/Db/install` through your browser (e.g. `yourdomain.com/preferendum/Db/install`)
- Delete `src/Controller/DbController.php` from your server
- Enjoy!

## Update
Starting with **v0.7.0** database update scripts had been added to PREFERendum, so that one can keep the existing database & data. But this feature can be only used, if the **old database is on v0.6.0** (or newer)!

Access `<your installation folder>/Db/update` through your browser (e.g. `yourdomain.com/preferendum/Db/update`) and select from which to which version your PREFERendum database shall be updated.

## Configuration and customization
You can turn on/off and configure all the available features in `config/preferendum_features.php`. For a full list of the options see the [Wiki](https://github.com/ElTh0r0/preferendum/wiki).

### Poll administration interface (off by default)
If you want to use the optional admin interface (to view and delete any polls on your server via a web interface at `yourdomain.com/preferendum/admin`) you have to enable this in `config/preferendum_features.php` ('adminInterface')! See [Admin Interface](https://github.com/ElTh0r0/preferendum/wiki/6-Admin-interface) for more information.

## Contribution
This is my first project using [CakePHP](https://cakephp.org), so there might exist many code sections, which could be implemented much more elegant with the build-in CakePHP features. I'm open for any optimization, just send a (well documented) **pull request**! Same for suggestions for additional features or if you are running into problems setting up PREFERendum, please write an [issue](https://github.com/ElTh0r0/preferendum/issues).

If you want to work on the code, clone the repositiory and run `composer install`. This will download CakePHP library and dependencies.

If you just like PREFERendum as it is, let me know by donating this repo a star.

### Help translating
New translations and corrections are highly welcome! You can either fork the source code from GitHub, make your changes and create a pull request or you can participate on Transifex: https://www.transifex.com/elth0r0/preferendum/

## Attribution of used third-party software/media
PREFERendum makes use of the following software/media and says **Thank you!** to:

- [/bkis/sprudel](https://github.com/bkis/sprudel)
- [CakePHP](https://cakephp.org)
- [/fengyuanchen/datepicker](https://github.com/fengyuanchen/datepicker)
- [/jquery/jquery](https://github.com/jquery/jquery)
- [/zenorocha/clipboard.js](https://github.com/zenorocha/clipboard.js)
- [Icons from iconmonstr.com](http://www.iconmonstr.com)
- [JS light/dark theme toggle](https://whitep4nth3r.com/blog/best-light-dark-mode-theme-toggle-javascript/)
