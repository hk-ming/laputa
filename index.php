<?php
require_once __DIR__ . '/vendor/autoload.php';

$a=parse_str('access_token=593f005fe070eacb1f56137c70a050dee57725d1&scope=user&token_type=bearer',$arr);
var_dump($arr);

$github=new \hyperqing\oauth\Github();
$github->getAccessToken();
//$github->getUser();