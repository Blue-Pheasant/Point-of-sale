<?php
define('DEFAULT_CONTROLLER', 'HomeController');
define('PROOT', '/erp_sales/');
define('SITE_TITLE', 'ERP Sales');
define('DEFAULT_LAYOUT', 'default');

define('DB_HOST', 'localhost');
define('DB_NAME', 'erpsystem');
define('DB_USER', 'root');
define('DB_PASSWORD', 'quan0402');
define('BASE_URL', "http" . ($_SERVER['HTTPS'] ? 's' : '') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");

$path = str_replace("\\", "/", "http://" . $_SERVER['SERVER_NAME'] . __DIR__ . "/");
$path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);


define('ROOT', str_replace("app/core", "public", $path));
define('ASSETS', str_replace("app/core", "public/assets", $path));