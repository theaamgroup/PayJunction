<?php

use AAM\PayJunction\PublishableKey;

require_once __DIR__ . '/functions.php';

$rest = useRest();
$rest->useFormContentType();
$pk = new PublishableKey();
$result = $pk->getAll($rest);
echo '<pre>';
print_r($result);
echo '</pre>';
die;
