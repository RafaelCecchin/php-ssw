<?php

require_once __DIR__ . '/vendor/autoload.php';

use RafaelCecchin\phpSSW\SSW;

$url = SSW::danfeUrl('CODIGO_44_DIGITOS');

var_dump($url);
