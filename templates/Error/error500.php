<?php

/**
 * @var \App\View\AppView $this
 * @var string $message
 * @var string $url
 */

use Cake\Core\Configure;
use Cake\Error\Debugger;

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.php');

    $this->start('file');
?>
    <?php if ($error instanceof Error) : ?>
        <?php $file = $error->getFile() ?>
        <?php $line = $error->getLine() ?>
        <strong>Error in: </strong>
        <?= $this->Html->link(sprintf('%s, line %s', Debugger::trimPath($file), $line), Debugger::editorUrl($file, $line)); ?>
    <?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    $this->end();
endif;
?>

<div class="center-box">
    <br>
    <h1><?php __('An internal error has occurred') ?></h1>
    <p class="fail">
        <strong><?php echo __('Error') ?>: </strong>
        <?php echo h($message) ?>
        <br><br>
        <?php echo $this->Html->link(__('Back'), 'javascript:history.back()') ?>
    </p>
</div>
