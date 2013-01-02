<?php	

/**
	@name: save_config;
	@desc: Used for saving the answers from config2.php;
	@type: PHP;
**/

	//print_r($_POST);

	require 'functions.php';

	$userID = $_POST["userID"];
	$nextPage = $_POST["nextPage"];

	
	saveConfig($userID, $_POST["Jahrgang"], $_POST["Geburtsort"], $_POST["Geschlecht"], $_POST["Wohnort"]);
	
	echo '<meta http-equiv="refresh" content="0;url=../'.$nextPage.'">';
	
?>
