<?php

/**
 * @var \App\View\AppView $this
 * @var string $message
 * @var string $url
 */

use Cake\Core\Configure;

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.php');

    $this->start('file');
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>

<div class="center-box">
    <br>
    <h1><?php echo h($message) ?></h1>
    <p class="fail">
        <strong><?php echo __('Error') ?>: </strong>
        <?php echo __('The requested address {0} was not found on this server.', "<strong>'{$url}'</strong>") ?>
        <br><br>
        <?php echo $this->Html->link(__('Back'), 'javascript:history.back()') ?>
    </p>
</div>
