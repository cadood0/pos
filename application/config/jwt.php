<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['jwt_secret'] = 'aed954e976d016d6431181875d2f8dd506fbaa9b14189986c2ed8acf357119ae';
$config['jwt_algo'] = 'HS256';
$config['jwt_issuer'] = 'pos';
$config['jwt_audience'] = 'pos';
$config['jwt_ttl'] = 7200;
$config['jwt_leeway'] = 30;
