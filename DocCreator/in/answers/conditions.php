<?php

/**
	@name: conditions;
	@desc: Used for saving if the user has accepted the conditions in the database;
	@type: PHP;
**/

	//print_r($_POST);

	require 'functions.php';

	$userID = $_POST["userID"];
	
	if(isset($_POST["Yes"])){
		acceptConditions($userID);
		$nextPage = $_POST["nextPage|Yes"];
		echo '<meta http-equiv="refresh" content="1;url=../'.$nextPage.'">';
	}

	if(isset($_POST["No"])){
		declineConditions($userID);
		$nextPage = $_POST["nextPage|No"];
		echo '<meta http-equiv="refresh" content="0;url=../'.$nextPage.'">';
	}
?>
