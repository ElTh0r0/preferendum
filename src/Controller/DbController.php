<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2020-present github.com/ElTh0r0
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.8.0
 */

declare(strict_types=1);

namespace App\Controller;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

class DbController extends AppController
{
    private const DEFAULT_ADMIN_USER = 'admin';
    private const DEFAULT_ADMIN_PW = 'admin';

    public function install(): void
    {
        echo '<!DOCTYPE html><html lang="en" data-theme="light">
        <head><meta charset="utf-8"><title>PREFERendum database setup</title>
        <link rel="stylesheet" href="../css/preferendum.css"></head>
        <body><p>Starting <strong>PREFERendum</strong> database setup...</p>';

        $this->checkEnvironment();
        $this->checkFilesystem();
        Configure::load('app_local');
        $dbdriver = Configure::read('Datasources.default.driver');
        $dbconnection = $this->checkDbConnection($dbdriver);
        if ($this->isAlreadyInstalled($dbconnection, $dbdriver)) {
            echo '<ul><li class="fail"><strong>Attention:</strong> 
            Install script was already executed - stopping execution!</li></ul>';
            die;
        }
        $this->createTables($dbconnection, $dbdriver);
        $dbconnection->getDriver()->disconnect();

        echo '<p class="success"><br>SETUP COMPLETED SUCCESSFULLY!</p>
        <strong>!!! Please delete "src/Controller/DbController.php" !!!</strong>
        </body></html>';

        $this->autoRender = false;
    }

    //------------------------------------------------------------------------

    public function update(?string $version = null): void
    {
        echo '<!DOCTYPE html><html lang="en" data-theme="light">
        <head><meta charset="utf-8"><title>PREFERendum database update</title>';
        if (!isset($version) || empty($version)) {
            echo '<link rel="stylesheet" href="../css/preferendum.css">';
        } else {
            echo '<link rel="stylesheet" href="../../css/preferendum.css">';
        }
        echo '</head><body><p><strong>PREFERendum</strong> database update...</p>';

        Configure::load('app_local');
        $dbdriver = Configure::read('Datasources.default.driver');
        $connection = $this->checkDbConnection($dbdriver);

        if (!isset($version) || empty($version)) {
            $this->checkEnvironment();
            $this->checkFilesystem();
            if (!$this->isAlreadyInstalled($connection, $dbdriver)) {
                echo '<ul><li class="fail"><strong>Attention:</strong> 
                PREFERendum seems not to be installed - stopping execution!</li></ul>';
                die;
            }

            echo '<p><br>Select update:</p><ul>
            <li><u><a href="update/06-07">Version 0.6.x -> 0.7.x</li></u></a>
            </ul>';
        } else {
            if (strcmp('06-07', $version) == 0) {
                $this->update06_07($connection, $dbdriver);
                echo '<p class="success"><br>Update from version 0.6 to 0.7 completed successfully!</p>';
            }
        }

        echo '<strong>Please delete "src/Controller/DbController.php" after the update!</strong>
        </body></html>';

        $this->autoRender = false;
    }

    //------------------------------------------------------------------------

    private function checkEnvironment(): void
    {
        echo '<h4>Environment</h4>
        <ul>';

        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            echo '<li class="success">Your version of PHP is 8.1.0 or higher (detected ' . PHP_VERSION . ').</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Your version of PHP is too low. 
            You need PHP 8.1.0 or higher (detected ' . PHP_VERSION . ').</li>';
            die;
        }

        if (extension_loaded('mbstring')) {
            echo '<li class="success">Your version of PHP has the mbstring extension loaded.</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Your version of PHP does NOT have the 
            mbstring extension loaded.</li>';
            die;
        }

        if (extension_loaded('openssl')) {
            echo '<li class="success">Your version of PHP has the openssl extension loaded.</li>';
        } elseif (extension_loaded('mcrypt')) {
            echo '<li class="success">Your version of PHP has the mcrypt extension loaded.</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Your version of PHP does NOT have the 
            openssl or mcrypt extension loaded.</li>';
            die;
        }

        if (extension_loaded('intl')) {
            echo '<li class="success">Your version of PHP has the intl extension loaded.</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Your version of PHP does NOT have the 
            intl extension loaded.</li>';
            die;
        }

        echo '</ul>';
    }

    //------------------------------------------------------------------------

    private function checkFilesystem(): void
    {
        echo '<h4>Filesystem</h4>';
        echo '<ul>';

        if (is_writable(TMP)) {
            echo '<li class="success">Your tmp directory is writable.</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Your tmp directory is NOT writable.</li>';
            die;
        }

        if (is_writable(LOGS)) {
            echo '<li class="success">Your logs directory is writable.</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Your logs directory is NOT writable.</li>';
            die;
        }

        $settings = Cache::getConfig('_cake_translations_');
        if (!empty($settings)) {
            echo '<li>The <em>' . $settings['className'] . 'Engine</em> is being used for core caching. 
            To change the config edit config/app.php</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Your cache is NOT working. 
            Please check the settings in config/app.php</li>';
            die;
        }

        echo '</ul>';
    }

    //------------------------------------------------------------------------

    private function checkDbConnection(string $dbdriver): object
    {
        echo '<h4>Database</h4>';
        if (
            strcmp(strtolower($dbdriver), 'mysql') != 0 &&
            strcmp(strtolower($dbdriver), 'postgres') != 0
        ) {
            echo '<li class="fail"><strong>Problem:</strong> Invalid SQL database driver selected in 
            "app_local.php!<br>Only "Mysql" (for MySQL and MariaDB) and "Postgres" supported.</li>';
            die;
        }

        echo '<ul>';
        $errorMsg = '';
        try {
            $connection = ConnectionManager::get('default');
            $connection->getDriver()->connect();
            $connected = $connection->getDriver()->isConnected();
        } catch (Exception $connectionError) {
            $connected = false;
            $errorMsg = $connectionError->getMessage();
            if (method_exists($connectionError, 'getAttributes')) {
                $attributes = $connectionError->getAttributes();
                if (isset($errorMsg['message'])) {
                    $errorMsg .= '<br />' . $attributes['message'];
                }
            }
        }

        if ($connected) {
            echo '<li class="success">Database connection successful</li>';
        } else {
            echo '<li class="fail"><strong>Problem:</strong> NOT able to connect to the 
            database.<br />' . $errorMsg . '</li>';
            die;
        }
        echo '</ul>';

        return $connection;
    }

    //------------------------------------------------------------------------

    private function isAlreadyInstalled(object $connection, string $dbdriver): bool
    {
        echo '<ul>';

        $isInstalled = false;
        $DbName = $connection->config()['database'];
        if (strcmp(strtolower($dbdriver), 'mysql') == 0) {
            $table = $connection->execute(
                'SELECT IF( EXISTS(
                    SELECT *
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_SCHEMA = "' . $DbName . '" AND TABLE_NAME = "polls"), 1, 0) as "exists";'
            )->fetchAll('assoc');
        } elseif (strcmp(strtolower($dbdriver), 'postgres') == 0) {
            $table = $connection->execute(
                'SELECT EXISTS (
                    SELECT 1
                    FROM information_schema.tables
                    WHERE table_catalog = \'' . $DbName . '\' AND TABLE_NAME = \'polls\') as exists;'
            )->fetchAll('assoc');
        } else {
            echo '<li class="fail"><strong>Problem:</strong> Invalid DB driver selected!</li>';
            die;
        }

        if ($table[0]['exists']) {
            $isInstalled = true;
        }
        echo '</ul>';

        return $isInstalled;
    }

    //------------------------------------------------------------------------

    private function createTables(object $connection, string $dbdriver): void
    {
        echo '<ul>';

        if (strcmp(strtolower($dbdriver), 'mysql') == 0) {
            $this->createMySqlTables($connection);
        } elseif (strcmp(strtolower($dbdriver), 'postgres') == 0) {
            $this->createPostgresTables($connection);
        }

        echo '<li>Creating default admin user</li>';
        $this->fetchTable('Users')->insertQuery()
            ->insert(['name', 'role', 'password'])
            ->values([
                'name' => self::DEFAULT_ADMIN_USER,
                'role' => 'admin',
                'password' => (new DefaultPasswordHasher())->hash(self::DEFAULT_ADMIN_PW),
            ])->execute();
        echo '<li><ul><li>If Admin Interface is used, please change the default 
        password after first login!</li></ul></li>';

        echo '</ul>';
    }

    //------------------------------------------------------------------------

    private function createMySqlTables(object $connection): void
    {
        echo '<li>Creating "polls" table</li>';
        $connection->execute('CREATE TABLE `polls` (
            `id` varchar(32) PRIMARY KEY,
            `adminid` varchar(32) NOT NULL,
            `title` varchar(256) NOT NULL,
            `details` varchar(512) DEFAULT "",
            `email` varchar(32) DEFAULT "",
            `emailentry` tinyint(1) NOT NULL DEFAULT 0,
            `emailcomment` tinyint(1) NOT NULL DEFAULT 0,
            `userinfo` tinyint(1) NOT NULL DEFAULT 0,
            `editentry` tinyint(1) NOT NULL DEFAULT 0,
            `comment` tinyint(1) NOT NULL DEFAULT 0,
            `hidevotes` tinyint(1) NOT NULL DEFAULT 0,
            `anonymous` tinyint(1) NOT NULL DEFAULT 0,
            `pwprotect` tinyint(1) NOT NULL DEFAULT 0,
            `limitentry` tinyint(1) NOT NULL DEFAULT 0,
            `expiry` DATE DEFAULT NULL,
            `locked` tinyint(1) NOT NULL DEFAULT 0,
            `modified` DATETIME NOT NULL
        );');

        echo '<li>Creating "comments" table</li>';
        $connection->execute('CREATE TABLE `comments` (
            `id` INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `poll_id` varchar(32) NOT NULL,
            `text` varchar(512) NOT NULL,
            `name` varchar(32) NOT NULL,
            `created` DATETIME NOT NULL
        );');

        echo '<li>Creating "choices" table</li>';
        $connection->execute('CREATE TABLE `choices` (
            `id` INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `poll_id` varchar(32) NOT NULL,
            `option` varchar(50) NOT NULL,
            `max_entries` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
            `sort` tinyint(3) UNSIGNED NOT NULL
        );');

        echo '<li>Creating "entries" table</li>';
        $connection->execute('CREATE TABLE `entries` (
            `id` INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `choice_id` INTEGER UNSIGNED NOT NULL,
            `user_id` INTEGER UNSIGNED NOT NULL,
            `value` tinyint(3) UNSIGNED NOT NULL
        );');

        echo '<li>Creating "users" table</li>';
        $connection->execute('CREATE TABLE `users` (
            `id` INTEGER UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(32) NOT NULL,
            `role` varchar(16) DEFAULT "",
            `password` varchar(255) DEFAULT "",
            `info` varchar(255) DEFAULT ""
        );');

        echo '<li>Creating key/index</li>';
        $connection->execute('ALTER TABLE `comments`
            ADD KEY `poll_id` (`poll_id`);');
        $connection->execute('ALTER TABLE `choices`
            ADD KEY `poll_id` (`poll_id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD KEY `choice_id` (`choice_id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD KEY `user_id` (`user_id`);');

        echo '<li>Creating constraints</li>';
        $connection->execute('ALTER TABLE `comments`
            ADD CONSTRAINT `fk_comm_pollid` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`);');
        $connection->execute('ALTER TABLE `choices`
            ADD CONSTRAINT `fk_choi_pollid` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD CONSTRAINT `fk_entr_choiceid` FOREIGN KEY (`choice_id`) REFERENCES `choices` (`id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD CONSTRAINT `fk_entr_userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);');
    }

    //------------------------------------------------------------------------

    private function createPostgresTables(object $connection): void
    {
        echo '<li>Creating "polls" table</li>';
        $connection->execute("CREATE TABLE polls (
            id varchar(32) PRIMARY KEY,
            adminid varchar(32) NOT NULL,
            title varchar(256) NOT NULL,
            details varchar(512) DEFAULT '',
            email varchar(32) DEFAULT '',
            emailentry BOOLEAN NOT NULL DEFAULT false,
            emailcomment BOOLEAN NOT NULL DEFAULT false,
            userinfo BOOLEAN NOT NULL DEFAULT false,
            editentry BOOLEAN NOT NULL DEFAULT false,
            comment BOOLEAN NOT NULL DEFAULT false,
            hidevotes BOOLEAN NOT NULL DEFAULT false,
            anonymous BOOLEAN NOT NULL DEFAULT false,
            pwprotect BOOLEAN NOT NULL DEFAULT false,
            limitentry BOOLEAN NOT NULL DEFAULT false,
            expiry DATE DEFAULT NULL,
            locked BOOLEAN NOT NULL DEFAULT false,
            modified TIMESTAMP NOT NULL
        );");

        echo '<li>Creating "comments" table</li>';
        $connection->execute("CREATE TABLE comments (
            id SERIAL PRIMARY KEY,
            poll_id varchar(32) NOT NULL,
            text varchar(512) NOT NULL,
            name varchar(32) NOT NULL,
            created TIMESTAMP NOT NULL
        );");

        echo '<li>Creating "choices" table</li>';
        $connection->execute("CREATE TABLE choices (
            id SERIAL PRIMARY KEY,
            poll_id varchar(32) NOT NULL,
            option varchar(50) NOT NULL,
            max_entries SMALLINT NOT NULL DEFAULT 0,
            sort SMALLINT NOT NULL
        );");

        echo '<li>Creating "entries" table</li>';
        $connection->execute('CREATE TABLE entries (
            id SERIAL PRIMARY KEY,
            choice_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            value SMALLINT NOT NULL
        );');

        echo '<li>Creating "users" table</li>';
        $connection->execute("CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            name varchar(32) NOT NULL,
            role varchar(16) DEFAULT '',
            password varchar(255) DEFAULT '',
            info varchar(255) DEFAULT ''
        );");

        echo '<li>Creating key/index</li>';
        $connection->execute('CREATE INDEX comm_poll_id on comments (poll_id);');
        $connection->execute('CREATE INDEX choi_poll_id on choices (poll_id);');
        $connection->execute('CREATE INDEX entr_choice_id on entries (choice_id);');
        $connection->execute('CREATE INDEX entr_user_id on entries (user_id);');

        echo '<li>Creating constraints</li>';
        $connection->execute('ALTER TABLE comments
            ADD CONSTRAINT fk_comm_pollid FOREIGN KEY (poll_id) REFERENCES polls (id);');
        $connection->execute('ALTER TABLE choices
            ADD CONSTRAINT fk_choi_pollid FOREIGN KEY (poll_id) REFERENCES polls (id);');
        $connection->execute('ALTER TABLE entries
            ADD CONSTRAINT fk_entr_choiceid FOREIGN KEY (choice_id) REFERENCES choices (id);');
        $connection->execute('ALTER TABLE entries
            ADD CONSTRAINT fk_entr_userid FOREIGN KEY (user_id) REFERENCES users (id);');
    }

    //------------------------------------------------------------------------
    //------------------------------------------------------------------------

    private function update06_07(object $connection, string $dbdriver): void
    {
        if (strcmp(strtolower($dbdriver), 'mysql') == 0) {
            $connection->execute('ALTER TABLE `polls`
                ADD `limitentry` tinyint(1) NOT NULL DEFAULT 0 AFTER `pwprotect`;');
            $connection->execute('ALTER TABLE `choices`
                ADD `max_entries` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 AFTER `option`;');
        } elseif (strcmp(strtolower($dbdriver), 'postgres') == 0) {
            $connection->execute('ALTER TABLE polls
                ADD limitentry BOOLEAN NOT NULL DEFAULT false;');
            $connection->execute('ALTER TABLE choices
                ADD max_entries SMALLINT NOT NULL DEFAULT 0;');
        }
    }
}
