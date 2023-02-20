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
 * @version   0.5.0
 */
declare(strict_types=1);

namespace App\Controller;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;


class InstalldbController extends AppController
{
    public function index(): void
    {
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>PREFERendum database setup</title></head>';
        echo '<body>';

        $this->checkEnvironment();
        $this->checkFilesystem();
        $dbconnection = $this->checkDatabase();
        $this->createTables($dbconnection);
        
        echo '<p>DONE!</p>';
        echo '<strong>!!! Please delete "src/Controller/InstalldbController.php" !!!</strong>';
        echo '</body></html>';

        $this->autoRender = false;
    }

    //------------------------------------------------------------------------

    private function checkEnvironment()
    {
        echo '<h4>Environment</h4>';
        echo '<ul>';
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            echo '<li>Your version of PHP is 7.4.0 or higher (detected ' . PHP_VERSION . ').</li>';
        } else {
            echo '<li><strong>Problem:</strong> Your version of PHP is too low. You need PHP 7.4.0 or higher (detected ' . PHP_VERSION . ').</li>';
            die;
        }

        if (extension_loaded('mbstring')) {
            echo '<li>Your version of PHP has the mbstring extension loaded.</li>';
        } else {
            echo '<li><strong>Problem:</strong> Your version of PHP does NOT have the mbstring extension loaded.</li>';
            die;
        }

        if (extension_loaded('openssl')) {
            echo '<li>Your version of PHP has the openssl extension loaded.</li>';
        } elseif (extension_loaded('mcrypt')) {
            echo '<li>Your version of PHP has the mcrypt extension loaded.</li>';
        } else {
            echo '<li><strong>Problem:</strong> Your version of PHP does NOT have the openssl or mcrypt extension loaded.</li>';
            die;
        }

        if (extension_loaded('intl')) {
            echo '<li>Your version of PHP has the intl extension loaded.</li>';
        } else {
            echo '<li><strong>Problem:</strong> Your version of PHP does NOT have the intl extension loaded.</li>';
            die;
        }
        echo '</ul>';
    }

    //------------------------------------------------------------------------

    private function checkFilesystem()
    {
        echo '<h4>Filesystem</h4>';
        echo '<ul>';
        if (is_writable(TMP)) {
            echo '<li>Your tmp directory is writable.</li>';
        } else {
            echo '<li><strong>Problem:</strong> Your tmp directory is NOT writable.</li>';
            die;
        }

        if (is_writable(LOGS)) {
            echo '<li>Your logs directory is writable.</li>';
        } else {
            echo '<li><strong>Problem:</strong> Your logs directory is NOT writable.</li>';
            die;
        }

        $settings = Cache::getConfig('_cake_core_');
        if (!empty($settings)) {
            echo '<li>The <em>' . $settings["className"] . 'Engine</em> is being used for core caching. To change the config edit config/app.php</li>';
        } else {
            echo '<li><strong>Problem:</strong> Your cache is NOT working. Please check the settings in config/app.php</li>';
            die;
        }
        echo '</ul>';
    }

    //------------------------------------------------------------------------

    private function checkDatabase()
    {
        echo '<h4>Database</h4>';
        echo '<ul>';
        try {
            $connection = ConnectionManager::get('default');
            $connected = $connection->connect();
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
            echo '<li>Database connection successful</li>';
        } else {
            echo '<li><strong>Problem:</strong> NOT able to connect to the database.<br />' . $errorMsg . '</li>';
            die;
        }

        $table = $connection->execute('SELECT IF( EXISTS(
            SELECT *
            FROM INFORMATION_SCHEMA.TABLES
          WHERE TABLE_SCHEMA = "' . $connection->config()['database'] .'" AND TABLE_NAME = "polls"), 1, 0) as "exists";')->fetchAll('assoc');
        if ($table[0]['exists']) {
            echo '<li><strong>Attention:</strong> Install script was already executed - stopping execution!</li>';
            die;
        }
        echo '</ul>';

        return $connection;
    }

    //------------------------------------------------------------------------

    private function createTables($connection)
    {
        echo '<ul>';
        echo '<li>Creating "comments" table</li>';
        $connection->execute('CREATE TABLE `comments` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `poll_id` varchar(32) NOT NULL,
            `text` varchar(512) NOT NULL,
            `name` varchar(32) NOT NULL,
            `created` DATETIME NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        echo '<li>Creating "choices" table</li>';
        $connection->execute('CREATE TABLE `choices` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `poll_id` varchar(32) NOT NULL,
            `option` varchar(32) NOT NULL,
            `sort` tinyint(4) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        echo '<li>Creating "entries" table</li>';
        $connection->execute('CREATE TABLE `entries` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `choice_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `value` tinyint(4) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        echo '<li>Creating "users" table</li>';
        $connection->execute('CREATE TABLE `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` varchar(32) NOT NULL,
            `role` varchar(16) DEFAULT "",
            `password` varchar(255) DEFAULT "",
            `info` varchar(255) DEFAULT ""
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        echo '<ul><li>Creating default admin user</li></ul>';
        $connection->execute('INSERT INTO `users` (`name`, `role`, `password`) VALUES
        ("admin", "admin", "$2y$10$YW0XBpcu4RoiUR5tW/rImuChkO1h8LDyecm6F1/Cty5QhJrwP958e");');

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
            `comment` tinyint(1) NOT NULL DEFAULT 0,
            `hideresult` tinyint(1) NOT NULL DEFAULT 0,
            `locked` tinyint(1) NOT NULL DEFAULT 0,
            `modified` DATETIME NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        echo '<li>Creating keys</li>';
        $connection->execute('ALTER TABLE `comments`
            ADD KEY `poll_id` (`poll_id`);');
        $connection->execute('ALTER TABLE `choices`
            ADD KEY `poll_id` (`poll_id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD KEY `choice_id` (`choice_id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD KEY `user_id` (`user_id`);');

        echo '<li>Creating contraints</li>';
        $connection->execute('ALTER TABLE `comments`
            ADD CONSTRAINT `fk_comm_pollid` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`);');
        $connection->execute('ALTER TABLE `choices`
            ADD CONSTRAINT `fk_choi_pollid` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD CONSTRAINT `fk_entr_choiceid` FOREIGN KEY (`choice_id`) REFERENCES `choices` (`id`);');
        $connection->execute('ALTER TABLE `entries`
            ADD CONSTRAINT `fk_entr_userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);');

        echo '</ul>';
    }
}
