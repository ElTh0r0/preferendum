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
- **Customizable colors**: All the interface colors can easily be set to your preference (system-wide, that is, not for individual polls)
- Optional:
  - **Poll admin links** to restrict poll management to author
  - Receive email after new entry and/or comment
  - Collect user contact information with a poll entry
  - Poll **administration interface** for managing all the polls on the server
  - Simple user administration for admin interface

![screenshot](https://user-images.githubusercontent.com/26674558/121807573-faaea200-cc54-11eb-838f-4d31639b0915.png)

## Requirements
- PHP 7.4 or higher (including intl and mbstring extension)
- One of the following databases:
  - MySql (5.6 or higher)
  - MariaDB (5.6 or higher)
  - PostgreSQL (9.4 or higher)
  - Microsoft SQL Server (2012 or higher)
  - SQLite 3

## Installation
- Create an empty MySql database on your server, note down the DB host address, user name and password
- Install option #1:
  - Download the latest PREFERendum release archive `preferendum_vX.X.X.zip` from [releases](https://github.com/ElTh0r0/preferendum/releases)
  - Extract the contents of the archive into a new directory (e.g. `preferendum` on your computer)
  - Take care of proper folder permissions, see [CakePHP permissions](https://book.cakephp.org/4/en/installation.html#permissions)
  - Copy `config/app_local.example.php` and rename it to `config/app_local.php`.
- Install option #2:
  - Download **source code** from latest release and run `composer install --no-dev`. The install command will download all needed dependencies.
- In `config/app_local.php` change the following entries:
  - Datasources\Default: Set host, username, password, database according to your environment.
  - Security\Salt: Replace \_\_SALT\_\_ with an arbitrary string (will be done automatically, if composer install method is used).
- In `config/preferendum_features.php` configure the features as needed.
- Upload the `preferendum` directory to your web server (root-directory or somewhere else)
- Access `<your installation folder>/installdb` through your browser (e.g. `yourdomain.com/preferendum/installdb`)
-  Delete `src/Controller/InstalldbController.php` from your server
-  Enjoy!

## Configuration and customization
### Features configuration
You can turn on/off and configure all the available features (like admin interface) in `config/preferendum_features.php`.
### Texts and labels
By default English and German translations are included and one can switch between the available languages in `config/preferendum_features.php`. PREFERendum uses **gettext** for translation and based on the English strings in `resources/locales/default.pot` one can create further translations, which have to be put into a separate sub folder (using ISO code as folder name).

#### Help translating
New translations and corrections are highly welcome! You can either fork the source code from GitHub, make your changes and create a pull request or you can participate on Transifex: https://www.transifex.com/elth0r0/preferendum/

### Colors
At the top of the CSS stylesheet (`css/preferendum.css`), you'll find a list of [CSS custom properties](https://developer.mozilla.org/en-US/docs/Web/CSS/--*) you can change to customize colors and some other layout/design related things.

## Administration
### Poll administration interface (off by default)
If you want to use the optional admin interface (to view and delete any polls on your server via a web interface under `/admin`) you have to enable this in `config/preferendum_features.php`!

Default credentials: admin/admin

If you are enabling this feature, please change the password after the installation!

### Automatic deletion of inactive polls (needs cronjob!)
You can set up a certain number of days in the `config/preferendum_features.php` to mark the maximum age of an **inactive** poll (*inactive* as in: no new answers and comments). PREFERendum comes with a cleanup function that you can set up to be executed periodically via a cronjob, e.g.

`0 0 1 * * /usr/bin/php /var/www/html/preferendum/polls/cleanup`

The cleanup script will then delete all inactive polls that became too old.

## Contribution
This is my first project using [CakePHP](https://cakephp.org), so there might exist many code sections, which could be implemented much more elegant with the build-in CakePHP features. I'm open for any optimization! Same for suggestions for additional features or if you are running into problems setting up PREFERendum, **write an issue**! Additional translations are highly welcome as well or if you feel like improving the code of this app, send a (well documented) **pull request**! If you just like PREFERendum as it is, let me know by donating this repo a star.

If you want to work on the code, clone the repositiory and run `composer install`. This will download CakePHP library and dependencies.

## Attribution of used third-party software/media
PREFERendum makes use of the following software/media and says **Thank you!** to:

- [/bkis/sprudel](https://github.com/bkis/sprudel)
- [CakePHP](https://cakephp.org)
- [/fengyuanchen/datepicker](https://github.com/fengyuanchen/datepicker)
- [/jquery/jquery](https://github.com/jquery/jquery)
- [/zenorocha/clipboard.js](https://github.com/zenorocha/clipboard.js)
- [Icons from iconmonstr.com](http://www.iconmonstr.com)
