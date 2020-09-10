<?php
/**
 * @var \App\View\AppView $this
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.php');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?= $this->element('auto_table_warning') ?>
<?php

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
