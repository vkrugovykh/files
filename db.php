<?php

define('HOST', 'localhost');
define('DB_NAME', 'academy');
define('USER_NAME', 'root');
define('CHARSET', 'utf8');
define('PASS', '');

$connection = new PDO('mysql:host=' . HOST . '; dbname=' . DB_NAME . '; charset=' . CHARSET, USER_NAME, PASS);
