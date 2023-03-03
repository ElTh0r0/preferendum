<?php

/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
$permanent = false;
if (!empty($params['permanent'])) {
    $permanent = $params['permanent'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<?php if ($permanent) { ?>
    <div class="<?= h($class) ?>"><?= $message ?></div>
<?php } else { ?>
    <div class="<?= h($class) ?>" onclick="this.classList.add('hidden');"><?= $message ?></div>
<?php } ?>
