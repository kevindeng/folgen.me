<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_folgen = "folgenme.db.7851672.hostedresource.com";
$database_folgen = "folgenme";
$username_folgen = "folgenme";
$password_folgen = "YahooUR2012";
$folgen = mysql_pconnect($hostname_folgen, $username_folgen, $password_folgen) or trigger_error(mysql_error(),E_USER_ERROR); 
?>