<?php	

/**
	@name: answers_all;
	@desc: Used for saving answers from the user in the Database;
	@type: PHP;
**/


	//print_r($_POST);
	
	require 'functions.php';

	$userID = $_POST["userID"];
	$participantID = $_POST["participantID"];
	$nextPage = $_POST["nextPage"];
	$startTime = $_POST["startTime"];
	
	$posts = array_keys($_POST);
	
	for($i = 0; $i < count($posts); $i++){
		if($posts[$i] != "userID" && $posts[$i] != "nextPage" && $posts[$i] != "startTime" && $posts[$i] != "participantID"){
			$array = explode_answers($_POST[$posts[$i]]);
			saveAnswers($array, $participantID, $startTime);
		}
	}
	
	echo '<meta http-equiv="refresh" content="0;url=../'.$nextPage.'">';
?>
