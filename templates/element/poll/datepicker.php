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
use Cake\I18n\I18n;
?>

<?php
$locale = I18n::getLocale();
$locale = str_replace('_', '-', $locale);

$this->Html->css('datepicker.css', ['block' => true]);
$this->Html->script('datepicker/datepicker.min.js', ['block' => true]);
$this->Html->script('datepicker/datepicker.' . $locale . '.js', ['block' => true]);

$this->Html->scriptStart(['block' => true]);
// Datepicker localizations
echo 'var jslocale = ' . json_encode($locale) . ';';
echo 'var jsdateformat = ' . json_encode(Configure::read('preferendum.datepickerFormat')) . ';';
$this->Html->scriptEnd();
