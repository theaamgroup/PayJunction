<?php

use AAM\PayJunction\PublishableKey;

require_once __DIR__ . '/functions.php';

$rest = useRest();
echo '<pre>';
print_r(PublishableKey::getAll($rest));
print_r(PublishableKey::getOne($rest));
echo '</pre>';
die;
