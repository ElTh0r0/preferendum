<?php
/**
 * Sprudel-ng (https://github.com/ElTh0r0/sprudel-ng)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2020 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/sprudel-ng
 * @since     0.1.0
 */
?>

<?php if (\Cake\Core\Configure::read('Sprudel-ng.footerLink')) { ?>
<div id="footer">
    <em>sprudel-ng</em> open source scheduling polls.
    <a href="https://github.com/ElTh0r0/sprudel-ng" target="_blank">
    <em>Visit on GitHub</em>
    <img src=<?php echo $this->request->getAttributes()['webroot'] . 'img/icon-github.png' ?> alt=""/>
    </a>
</div>
<?php } ?>
