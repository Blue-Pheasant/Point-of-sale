<?php
<<<<<<< HEAD
define('DEFAULT_CONTROLLER','HomeController');
define('PROOT','/erp_sales/');
define('SITE_TITLE','ERP Sales');
=======
define('DEFAULT_CONTROLLER', 'HomeController');
define('PROOT', '/erp_sales/');
define('SITE_TITLE', 'ERP Sales');
>>>>>>> 3f3e78561431b2e4a0dd3c02e71dac3d6925280d
define('DEFAULT_LAYOUT', 'default');

define('DB_HOST', 'localhost');
define('DB_NAME', 'erpsystem');
define('DB_USER', 'root');
<<<<<<< HEAD
define('DB_PASSWORD', '');
=======
define('DB_PASSWORD', '');
define('BASE_URL', "http" . ($_SERVER['HTTPS'] ? 's' : '') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

$path = str_replace("\\", "/", "http://" . $_SERVER['SERVER_NAME'] . __DIR__ . "/");
$path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);


define('ROOT', str_replace("app/core", "public", $path));
define('ASSETS', str_replace("app/core", "public/assets", $path));
>>>>>>> 3f3e78561431b2e4a0dd3c02e71dac3d6925280d
