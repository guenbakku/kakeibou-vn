<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

A PHP Error was encountered

Severity:    <?= $severity, "\n"; ?>
Message:     <?= $message, "\n"; ?>
Filename:    <?= $filepath, "\n"; ?>
Line Number: <?= $line; ?>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === true) { ?>

Backtrace:
<?php foreach (debug_backtrace() as $error) { ?>
<?php if (isset($error['file']) && 0 !== strpos($error['file'], realpath(BASEPATH))) { ?>
    File: <?= $error['file'], "\n"; ?>
    Line: <?= $error['line'], "\n"; ?>
    Function: <?= $error['function'], "\n\n"; ?>
<?php } ?>
<?php } ?>

<?php } ?>
