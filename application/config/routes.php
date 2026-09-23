<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'web/dashboard';

$route['login'] = 'web/auth/login';
$route['dashboard'] = 'web/dashboard/index';
$route['logout-ui'] = 'web/auth/logout';

$route['api/register'] = 'api/auth/register';
$route['api/login'] = 'api/auth/login';
$route['api/me'] = 'api/auth/me';
$route['api/logout'] = 'api/auth/logout';
$route['api/reports/summary'] = 'api/reports/summary';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
