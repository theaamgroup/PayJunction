<?php

use AAM\PayJunction\SmartTerminal;
use AAM\PayJunction\Terminal;

require_once __DIR__ . '/functions.php';

$rest = useRest();
echo 'Terminals';
echo '<pre>';
print_r(Terminal::getAll($rest));
echo '</pre>';
echo 'Smart Terminals';
echo '<pre>';
print_r(SmartTerminal::getAll($rest));
echo '</pre>';
die;
