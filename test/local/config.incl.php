<?php
namespace booosta;

Framework::$CONFIG = [
'site_name'              => 'Test-App',
'page_title'             => 'Test-App',
'page_title_short'       => 'TEST',
'allow_registration'     => false,
'confirm_registration'   => false,
'aes256_keyfile'         => 'local/key.php',
'language'               => 'de',

'db_module'              => 'mysqli',
'db_hostname'            => 'localhost',
'db_user'                => 'fw4test',
'db_password'            => '.9&V!2Pc2xKr',
'db_database'            => 'fw4test',

'DEBUG_MODE'             => true,
'debug_mode'             => true,
'LOG_MODE'               => false,
'BACKUPMODE'             => false,
'urlhandlermode'         => true,

'serial_field'           => ['usersettings', 'ser_info'],
'ESCAPE_CURL'            => false,
'NO_DBCONNECT'           => false,

];
