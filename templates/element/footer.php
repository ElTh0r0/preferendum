<?php

/**
 * PREFERendum
 *
 * SPDX-FileCopyrightText: codeberg.org/ElTh0r0, github.com/bkis
 * SPDX-License-Identifier: MIT
 *
 * @copyright 2019-present codeberg.org/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/license/MIT)
 * @link      https://codeberg.org/ElTh0r0/preferendum
 */

use Cake\Core\Configure;
?>

<?php if (Configure::read('preferendum.footerLink')) { ?>
    <div id="footer">
        <em>PREFERendum</em> open source scheduling polls.
        <a href="https://codeberg.org/ElTh0r0/preferendum" target="_blank">
            <em>Visit on Codeberg</em>
            <img src=<?php echo $this->request->getAttributes()['webroot'] . 'img/icon-codeberg.png' ?> alt="">
        </a>
    </div>
<?php } ?>
