<?php
/**
	@name: connect;
	@desc: Used for connection to the database;
	@type: PHP;
**/


	$host = "132.199.143.90"; 
	$user = "urwalking"; 
	$pass = "F7ZHRpWnU9Co6sH7"; 
	$db = "regensburg_alt"; 

	$con = pg_connect("host=$host dbname=$db user=$user password=$pass") or die("Could not connect to server<br/>");
?>
