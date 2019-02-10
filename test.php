<?php

require './vendor/autoload.php';

$config = [
    'api_key' => 'B7620A43AE0DA86ABAE3C85B007A5D5A',
    'api_pwd' => 'D0C56FEB74E321041455CEDF2DCD2541'
];

$client = new \ChutaoTech\Jianghuren\Client($config);
$client->getAllProducts();