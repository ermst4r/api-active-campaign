<?php
include 'MailExpert.php';
$api_limit = 10;
$arr = [
    ['name'=>'zoetermeer','api_key'=>'e32e21ec0d9c4e219c26e6039216830fe5a12f86dfe5d05a4c46040d380c158fa3e5d9e5','api_url'=>'https://spareribexpresszoetermeer.api-us1.com']
    // add more

];



$Mailexpert = new MailexpertApi();

foreach($arr as $filialen) {
    echo "Starting {$filialen['name']}\n ";
    $Mailexpert->setToApi($filialen['api_url']);
    $Mailexpert->setToApiKey($filialen['api_key']);
    $Mailexpert->syncUsers($api_limit,$filialen['name']);
}
