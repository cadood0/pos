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

$route['api/categories']['get'] = 'api/categories/index';
$route['api/categories']['post'] = 'api/categories/store';
$route['api/categories/(:num)']['get'] = 'api/categories/show/$1';
$route['api/categories/(:num)']['put'] = 'api/categories/update/$1';
$route['api/categories/(:num)']['delete'] = 'api/categories/destroy/$1';

$route['api/products']['get'] = 'api/products/index';
$route['api/products']['post'] = 'api/products/store';
$route['api/products/(:num)']['get'] = 'api/products/show/$1';
$route['api/products/(:num)']['put'] = 'api/products/update/$1';
$route['api/products/(:num)']['delete'] = 'api/products/destroy/$1';

$route['categories']['get'] = 'web/categories/index';
$route['categories/create']['get'] = 'web/categories/create';
$route['categories/(:num)/edit']['get'] = 'web/categories/edit/$1';
$route['categories/(:num)']['get'] = 'web/categories/show/$1';

$route['products']['get'] = 'web/products/index';
$route['products/create']['get'] = 'web/products/create';
$route['products/(:num)/edit']['get'] = 'web/products/edit/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
