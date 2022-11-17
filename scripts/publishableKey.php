<?php

use AAM\PayJunction\PublishableKey;

require_once __DIR__ . '/functions.php';

$rest = useRest();
echo '<pre>';
print_r(PublishableKey::getAll($rest));
echo '</pre>';
die;
