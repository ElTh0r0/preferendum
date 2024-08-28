<?php

/**
 * PREFERendum (https://github.com/ElTh0r0/preferendum)
 * Copyright (c) github.com/ElTh0r0, github.com/bkis
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright 2019-present github.com/ElTh0r0, github.com/bkis
 * @license   MIT License (https://opensource.org/licenses/mit-license.php)
 * @link      https://github.com/ElTh0r0/preferendum
 * @version   0.8.0
 */
?>

<?php

use Cake\I18n\I18n;

$locale = I18n::getLocale();
$locale = str_replace('_', '-', $locale);

$this->Html->css('datepicker.css', ['block' => true]);
$this->Html->script('datepicker/datepicker.min.js', ['block' => true]);
$this->Html->script('datepicker/datepicker.' . $locale . '.js', ['block' => true]);

$this->Html->scriptStart(['block' => true]);
// Datepicker localizations
echo 'var jslocale = ' . json_encode($locale) . ';';
echo 'var jsdateformat = ' . json_encode(
    \Cake\Core\Configure::read('preferendum.datepickerFormat')
) . ';';
$this->Html->scriptEnd();

?>
