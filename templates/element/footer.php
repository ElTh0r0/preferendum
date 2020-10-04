<?php
/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-2020 github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @since     0.3.0
 */
?>

<?php if (\Cake\Core\Configure::read('preferendum.footerLink')) { ?>
<div id="footer">
    <em>PREFERendum</em> open source scheduling polls.
    <a href="https://github.com/ElTh0r0/preferendum" target="_blank">
    <em>Visit on GitHub</em>
    <img src=<?php echo $this->request->getAttributes()['webroot'] . 'img/icon-github.png' ?> alt=""/>
    </a>
</div>
<?php } ?>
