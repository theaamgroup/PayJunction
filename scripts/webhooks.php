<?php

use AAM\PayJunction\Webhook;

require_once __DIR__ . '/functions.php';

$rest = useRest();
echo 'All Webhooks';
echo '<pre>';
print_r(Webhook::getAll($rest));
echo '</pre>';
die;
