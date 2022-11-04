<?php

use AAM\PayJunction\Terminal;

require_once __DIR__ . '/functions.php';

$rest = useRest();
$terminal = new Terminal();
$result = $terminal->getAll($rest);
echo '<pre>';
print_r($result);
echo '</pre>';
die;
