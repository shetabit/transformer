<?php

use Shetabit\Transformer\Classes\Transform;
use Shetabit\Transformer\Classes\Transformer;

require "vendor/autoload.php";

$originalData = [
    'f_name' => 'mahdi',
    'l_name' => 'khanzadi'
];

$transformer = (new Transformer())->from('f_name')->to('first_name')->from('l_name')->to('last_name');

$transformedData = (new Transform($originalData))->get();

print_r($transformedData);