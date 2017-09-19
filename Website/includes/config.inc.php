<?php
define('HTP_DB','mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=pringles;host=10.69.69.11');
define('HTP_USER','pringles');
define('HTP_PASS','68v7ED9FchaUxUv2rXL7');

$db = new PDO(HTP_DB, HTP_USER, HTP_PASS);
