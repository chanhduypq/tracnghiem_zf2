<?php
// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH',
realpath(dirname(dirname(__FILE__)) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV',
(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
: 'production'));
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('FILE_CACHE_DIRECTORY', APPLICATION_PATH ."/../var/cache");
//Duong dan den thu muc /templates
define('BLOCK_PATH',APPLICATION_PATH . '/blocks');
//------------KHAI BAO DUONG DAN URL DEN CAC THU MUC --------------
define('APPLICATION_URL', '');