<?php
/**
	@name: functions;
	@desc: All sort of functions used by the php scripts;
	@type: PHP;
**/


//##########################################################################
//                             Functions                                   #
//##########################################################################

//##########################################################################
//   		loadSurvey


/**
	@name: loadSurveys;
	@desc: used to load the Surveydata from the database;
	@type: FUNCTION;
	@lang: PHP;
	@return: Hashtable;
	
**/

	function loadSurveys(){
		
		require 'connect.php';
	
		$query = "SELECT uniqueid,survey,ordinalnumber FROM urw_survey WHERE refstatus='1' ORDER BY ordinalnumber";
		$res = pg_query($con, $query) or die (pg_last_error($con)); 

		$data = pg_fetch_all($res);
		pg_close($con);
		return $data;
	}

	function loadSections($survey_id){
		require 'connect.php';

		$query = "SELECT uniqueid,name,ordinalnumber,predecessor,successor,timeconstraint FROM urw_survey_section WHERE refsurvey='".$survey_id."' ORDER BY ordinalnumber";
		$res = pg_query($con, $query) or die (pg_last_error($con)); 

		$data = pg_fetch_all($res);
		if(empty($data)){
			echo "No Data <br/>";
		}
		pg_close($con);
		return $data;
	}


	function loadQuestions($section_id){
		require 'connect.php';
		//echo 'Sectionid: '.$section_id.'<br/>';
		$query = "SELECT uniqueid,text,itemnumber,refscale,refpicture, refexpectedanswer FROM urw_questions WHERE refsectionnumber='".$section_id."' ORDER BY itemnumber";
		$res = pg_query($con, $query) or die (pg_last_error($con)); 

		$data = pg_fetch_all($res);
		pg_close($con);
		return $data;
	}
	
	
	function loadQuestionsIDs($section_id){
		require 'connect.php';
		//echo 'Sectionid: '.$section_id.'<br/>';
		$query = "SELECT uniqueid FROM urw_questions WHERE refsectionnumber='".$section_id."' ORDER BY itemnumber";
		$res = pg_query($con, $query) or die (pg_last_error($con)); 

		$data = pg_fetch_all($res);
		pg_close($con);
		return $data['uniqueid'];
	}
	
	function loadOneQuestionsIDs($section_id){
		require 'connect.php';
		//echo 'Sectionid: '.$section_id.'<br/>';
		$query = "SELECT uniqueid FROM urw_questions WHERE refsectionnumber='".$section_id."' ORDER BY itemnumber LIMIT 1";
		$res = pg_query($con, $query) or die (pg_last_error($con)); 

		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "";
		}
		return $data[0]['uniqueid'];
	}

	function loadScale($refscale){
		if($refscale !=  ""){
			require 'connect.php';

			$query = "SELECT name FROM urw_scale WHERE uniqueid='".$refscale."'";

			$res = pg_query($con, $query) or die (pg_last_error($con)); 
			$data = pg_fetch_all($res);
			pg_close($con);
			return $data[0]['name'];
		}
		return "";
	}

	function loadScaleMapping($refscale){
		if($refscale != null && $refscale != ""){
			require 'connect.php';

			$query = "SELECT intvalue,name FROM urw_scale_mapping WHERE refscale=".$refscale."";

			$res = pg_query($con, $query) or die (pg_last_error($con)); 
			$data = pg_fetch_all($res);
			pg_close($con);
			return $data;
		}
		return "";
	}

	function loadExpectedAnswer($refexpectedanswer, $refscale){
		if($refscale != null && $refscale != "" && $refexpectedanswer != null && $refexpectedanswer != ""){
			require 'connect.php';
			$query = "SELECT name FROM urw_scale_mapping WHERE intvalue=".$refexpectedanswer." AND refscale=".$refscale."";

			$res = pg_query($con, $query) or die (pg_last_error($con)); 
			//$data = pg_fetch_all($res);
			$name = pg_fetch_result($res, 'name');
			pg_close($con);
			return $name;
		}

		return "";
	}

	function loadPicture($refpicture){
		if($refpicture != null && $refpicture != ""){
			require 'connect.php';
			$query = "SELECT picturedata,description FROM urw_pictures WHERE uniqueid=".$refpicture."";

			$res = pg_query($con, $query) or die (pg_last_error($con)); 
			$data = pg_fetch_all($res);
			pg_close($con);
			return $data[0]['picturedata'];
		}

		return "";
	}
	
	function checkUserAnswersSection($refsection, $refparticipant){
		
		
		$questionid = loadOneQuestionsIDs($refsection);
		$refparticipantsurvey = getParticipantSurveyID_section($refsection, $refparticipant);
		
		if($questionid == "" || $refparticipantsurvey == "") return false;
		require 'connect.php';
		$query = "SELECT * FROM urw_participants_questions_answered WHERE refquestion=".$questionid." AND refparticipantsurvey=".$refparticipantsurvey."";
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$rows = pg_num_rows($res);
		pg_close($con);
		if($rows == 0){
			return false;
		}
		return true;
	}
	
	function getParticipantSurvey_section($refsection, $refparticipant){
		require 'connect.php';
		
		$refsurvey = getSurveyFromSection($refsection);
		$query = "SELECT * FROM urw_participants_survey WHERE refsurvey=".$refsurvey." AND refparticipant=".$refparticipant."";
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "";
		}
		return $data;
	}
	
	function getParticipantSurveyID_section($refsection, $refparticipant){
		
		
		$refsurvey = getSurveyFromSection($refsection);
		$query = "SELECT uniqueid FROM urw_participants_survey WHERE refsurvey='".$refsurvey."' AND refparticipant='".$refparticipant."' LIMIT 1";
		require 'connect.php';
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$rows = pg_num_rows($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "";
		}
		return $data[0]['uniqueid'];
	}
	
	function getParticipantSurvey($refsurvey, $refparticipant){
		require 'connect.php';
		
		$query = "SELECT * FROM urw_participants_survey WHERE refsurvey=".$refsurvey." AND refparticipant=".$refparticipant."";
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "";
		}
		return $data;
	}
	
	function getParticipantSurveyID($refsurvey, $refparticipant){
		require 'connect.php';
		
		$query = "SELECT uniqueid FROM urw_participants_survey WHERE refsurvey=".$refsurvey." AND refparticipant=".$refparticipant." ORDER BY uniqueid DESC";
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		$rows = pg_num_rows($res);
		pg_close($con);
		if($rows == 0){
			return "";
		}
		return $data[0]['uniqueid'];
	}
	
	function getSurveyFromSection($refsection){
		require 'connect.php';
		
		$query = "SELECT refsurvey FROM urw_survey_section WHERE uniqueid=".$refsection;
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		return $data[0]['refsurvey'];
	}
	
	function getSectionFromQuestion($refquestion){
		require 'connect.php';
		
		$query = "SELECT refsection FROM urw_questions WHERE uniqueid=".$refquestion."";
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		return $data[0]['refsection'];
	}
	
	function getSurveyFromQuestion($refquestion){
		require 'connect.php';
		
		$query = "SELECT refsurvey FROM urw_questions WHERE uniqueid=".$refquestion."";
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		return $data[0]['refsurvey'];
	}

	function saveAnswers($answer_array, $participantID, $startTime){
		$questionID = $answer_array["questionid"];
		$count = $answer_array["count"];
		echo "QuestionID: ".$questionID;
		print_r($answer_array);
		$refsurvey = getSurveyFromQuestion($questionID);
			
		$participantsurvey = getParticipantSurveyID($refsurvey, $participantID);
		if($participantsurvey == ""){
			createParticipantSurvey($refsurvey, $participantID, $startTime);
			$participantsurvey = getParticipantSurveyID($refsurvey, $participantID);
		}
		
		for($i = 0; $i < $count; $i++){
			$timestamp = $answer_array["ms_".$i];
			$answer = $answer_array["answer_".$i];
			saveAnswerVersion($timestamp, $answer, $startTime);
			$answer_version = getAnswerVersion($timestamp, $answer, $startTime);
			createQuestionAnswered($questionID, $participantsurvey, $answer_version);
		}
		
		
	}

	
	function createQuestionAnswered($questionID, $participantsurvey, $answer_version){
		require 'connect.php';
		
		$query = "INSERT INTO
               				urw_participants_questions_answered
						(refquestion, refparticipantsurvey, refanswerversion)
					VALUES
						('$questionID','$participantsurvey','$answer_version')";
						
		pg_query($con, $query) or die (pg_last_error($con)); 
		pg_close($con);
	}
	
	function getQuestionAnswered($refquestion, $refparticipantsurvey){
		require 'connect.php';
		
		$query = "SELECT 
						* 
				FROM 
						urw_participants_questions_answered 
				WHERE 
						refquestion=".$refquestion." AND refparticipantsurvey=".$refparticipantsurvey."";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "false";
		}
		return "true";
	
	
	}
	
	function saveAnswerVersion($timestamp, $answer, $startTime){
		require 'connect.php';
		
		$query = "INSERT INTO
               				urw_participants_answer_versions
						(uniqueid, timestampanswered, timestampstartsection, answer)
					VALUES
						(nextval(pg_get_serial_sequence('urw_participants_answer_versions','uniqueid')), '$timestamp','$startTime','$answer')";
						
		pg_query($con, $query) or die (pg_last_error($con)); 
		pg_close($con);
	}
	
	function getAnswerVersion($timestamp, $answer, $startTime){
		require 'connect.php';

		$query = "SELECT
               			uniqueid
				FROM
						urw_participants_answer_versions
				WHERE
						answer='$answer' AND timestampanswered='$timestamp' AND timestampstartsection='$startTime'
				ORDER BY
						uniqueid DESC";
		
			
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "-1";
		}
		return $data[0]['uniqueid'];
	}
	
	function createParticipantSurvey($refsurvey, $refparticipant, $startTime){
		require 'connect.php';
		$timestamp = $startTime;
		$query = "INSERT INTO
               				urw_participants_survey
						(uniqueid, timestampdate, refparticipant,refsurvey)
					VALUES
						(nextval(pg_get_serial_sequence('urw_participants_survey','uniqueid')),'$timestamp','$refparticipant','$refsurvey')";
						
		pg_query($con, $query) or die (pg_last_error($con)); 
		pg_close($con);
	}
	
	function checkParticipant($userID){
		$md5hash = createHash($userID);
	
		require 'connect.php';
		$query = "SELECT
               				uniqueid
				FROM
						urw_participants
				WHERE
						md5hash = '$md5hash'";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "-1";
		}
		return $data[0]["uniqueid"];
	}

	function saveConfig($userID, $yeargroup, $birthplace, $gender, $residence){
		
		$timestamp = get_timestamp();
		$md5hash = createHash($userID);
		createParticipant($md5hash, $timestamp);
		$participantID = checkParticipant($userID);
		$refsurvey = 5;
		
		createParticipantSurvey($refsurvey, $participantID, $timestamp);
		$participantsurvey = getParticipantSurveyID($refsurvey, $participantID);

		//Birthplace
		saveAnswerVersionFreeText($timestamp, $birthplace, $timestamp);
		$answer_version = getAnswerVersionFreeText($timestamp, $birthplace, $timestamp);
		createQuestionAnswered(87, $participantsurvey, $answer_version);
	
		//Yeargroup
		saveAnswerVersionFreeText($timestamp, $yeargroup, $timestamp);
		$answer_version = getAnswerVersionFreeText($timestamp, $yeargroup, $timestamp);
		createQuestionAnswered(88, $participantsurvey, $answer_version);

		//Gender
		saveAnswerVersionFreeText($timestamp, $gender, $timestamp);
		$answer_version = getAnswerVersionFreeText($timestamp, $gender, $timestamp);
		createQuestionAnswered(89, $participantsurvey, $answer_version);
	
		//Residence
		saveAnswerVersionFreeText($timestamp, $residence, $timestamp);
		$answer_version = getAnswerVersionFreeText($timestamp, $residence, $timestamp);
		createQuestionAnswered(90, $participantsurvey, $answer_version);
	}

	function createParticipant($md5hash, $timestamp){
		require 'connect.php';
		$query = "INSERT INTO
               				urw_participants
						(uniqueid, md5hash, createdon)
					VALUES
						(nextval(pg_get_serial_sequence('urw_participants','uniqueid')),'$md5hash', '$timestamp')";
						
		pg_query($con, $query) or die (pg_last_error($con)); 
		echo $query;
		pg_close($con);


	}

	function saveAnswerVersionFreeText($timestamp, $answer, $startTime){
		require 'connect.php';
		
		$query = "INSERT INTO
               				urw_participants_answer_versions
						(uniqueid, timestampanswered, timestampstartsection, freetextanswer)
					VALUES
						(nextval(pg_get_serial_sequence('urw_participants_answer_versions','uniqueid')), '$timestamp','$startTime','$answer')";
						
		pg_query($con, $query) or die (pg_last_error($con)); 
		pg_close($con);
	}
	
	function getAnswerVersionFreeText($timestamp, $answer, $startTime){
		require 'connect.php';

		$query = "SELECT
               			uniqueid
				FROM
						urw_participants_answer_versions
				WHERE
						freetextanswer='$answer' AND timestampanswered='$timestamp' AND timestampstartsection='$startTime'
				ORDER BY
						uniqueid DESC";
		
			
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "-1";
		}
		return $data[0]['uniqueid'];
	}
	
/*
########################old
	function saveConfig($userID, $yeargroup, $birthplace, $birth_postalcode, $gender, $mothertongue, $residence, $res_postalcode){
		$refbirthplace = getPlaceID($birth_postalcode);
		
		if($refbirthplace == ""){
			createPlace($birthplace, $birth_postalcode);
			$refbirthplace = getPlaceID($birth_postalcode);
		}
	
		if($birth_postalcode != $res_postalcode){
			$refresidence = getPlaceID($res_postalcode);
			if($refresidence == ""){
				createPlace($residence, $res_postalcode);
				$refresidence = getPlaceID($res_postalcode);
			}
		}else{
			$refresidence = $refbirthplace;
		}
	
		if($gender == "male"){
			$refgender = "FALSE";
		}else{
			$refgender = "TRUE";
		}
		
		$refmothertongue = getMotherTongue($mothertongue);
		
		if($refmothertongue == ""){
			$refmothertongue = getMotherTongue('nA');
		}
		
		$md5hash = createHash($userID);
	
		require 'connect.php';
		$query = "INSERT INTO
               				urw_participants
						(uniqueid, yeargroup, refbirthplace, female, refmothertongue, refresidence, md5hash)
					VALUES
						(nextval(pg_get_serial_sequence('urw_participants','uniqueid')),'$yeargroup', '$refbirthplace', '$refgender', '$refmothertongue', '$refresidence',  '$md5hash')";
						
		pg_query($con, $query) or die (pg_last_error($con)); 
		echo $query;
		pg_close($con);
	}

*/
	function createHash($userID){
		require 'connect.php';
		
		$query = "SELECT
               			email
				FROM
						urw_users
				WHERE
						uniqueid='$userID'";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "";
		}
		return md5($data[0]['email']);
	}
	
	function getPlaceID($postalcode){
		require 'connect.php';
		
		$query = "SELECT
               			uniqueid
				FROM
						urw_places
				WHERE
						postalcode='$postalcode'";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "";
		}
		return $data[0]['uniqueid'];
	}
	
	function createPlace($place, $postalcode){
		require 'connect.php';
		$query = "INSERT INTO
               				urw_places
						(uniqueid, name, refcountry, postalcode)
					VALUES
						(nextval(pg_get_serial_sequence('urw_places','uniqueid')), '$place', '57', '$postalcode')";
						
		pg_query($con, $query) or die (pg_last_error($con)); 
		pg_close($con);
	}
	
	function getMotherTongue($tongue){
		require 'connect.php';
		
		$query = "SELECT
               			uniqueid
				FROM
						urw_languages
				WHERE
						short='$tongue'";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);
		if(pg_num_rows($res) == 0){
			return "";
		}
		return $data[0]['uniqueid'];
	}

	function checkConditions($userID){
		require 'connect.php';
		
		$query = "SELECT
               				hasacceptedconditions
			FROM
					urw_users
			WHERE
					uniqueid='$userID'";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		$data = pg_fetch_all($res);
		pg_close($con);

		if($data[0]["hasacceptedconditions"] == "NULL"){
			return "null";
		}

		if($data[0]["hasacceptedconditions"] == "t"){
			return "true";
		}

		if($data[0]["hasacceptedconditions"] == "f"){
			return "false";
		}

		return "null";
	}

	function acceptConditions($userID){
		require 'connect.php';
		
		$query = "UPDATE
					urw_users
			SET
               				hasacceptedconditions=TRUE
			WHERE
					uniqueid='$userID'";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		pg_close($con);
	}

	function declineConditions($userID){
		require 'connect.php';
		
		$query = "UPDATE
					urw_users
			SET
               				hasacceptedconditions=FALSE
			WHERE
					uniqueid='$userID'";
						
		$res = pg_query($con, $query) or die (pg_last_error($con));
		pg_close($con);


	}

//##########################################################################
//                           explode_answers                                    

function explode_answers($answers) {
	echo "To Explode: ".$answers."<br/>";

	//QuestionID
	$array = explode("=",$answers);
    
	$return_array["questionid"] = $array[0];
	$versions = $array[1];

	$versions_array = explode("|", $versions);

	for($i = 0; $i < count($versions_array); $i+=2){
		$nr = ($i - $i / 2);		
		$return_array["ms_".$nr] = $versions_array[$i];
		$return_array["answer_".$nr] = $versions_array[$i+1];
	}
	$return_array["count"] = count($versions_array) / 2;
	
    	return $return_array;
}

//##########################################################################
//                             is_value_set                                              

function is_value_set($input) {
// Die Funktion prüft einfache Eingabefelder auf IRGENDEINE Eingabe. Liefert Fehlerangabe für Benutzer zurück.
    if ($input == "") {
        $return = "<font size=\"2\" color=\"#000000\" face=\"Arial\">Eingabe notwendig!</font>";
    } else {
        $return = "";
    }
    return $return;
}

//##########################################################################
//                             is_mail_set                                              

function is_mail_set($input) {
// Die Funktion prüft Maileingaben: Mindestens ein @ und ein . müssen enthalten sein. Liefert Fehlerangabe für Benutzer zurück.
    if ($input == "") {
        $return = "<font size=\"2\" color=\"#000000\" face=\"Arial\">Eingabe notwendig!</font>";
    } else {
        if (strstr($input, "@") && strstr($input, ".")) {
            $return = "";
        } else {
            $return = "<font size=\"2\" color=\"#FF0000\" face=\"Arial\">Eingabe ung&uuml;ltig!</font>";
        }
    }
    return $return;
}



//##########################################################################
//                             is_selection_set                                              

function is_selection_set($input) {
    if ($input == "") {
        $return = "<font size=\"2\" color=\"#000000\" face=\"Arial\">Eingabe notwendig!</font>";
    } else {
        $return = "";
    }
    return $return;
}

//##########################################################################
//                             make_date                                              

function make_date($input) {
    $datum = date("d.m.Y", (int) $input);
    return $datum;
}

//##########################################################################
//                             set_boolean_false

function set_boolean_false($input) {
    $input = 0;
    return $input;
}

//##########################################################################
//                             print_bool

function print_bool($input) {
    $return = "";
    if ($input == "1") {
        $return = "Ja";
    }
    if ($input == "0") {
        $return = "Nein";
    }
    return $input;
}



##############################################################################    
#                           set_current_time   

function set_current_time() {
    $return = time();
    return $return;
}


##############################################################################
# date_iso_german


function date_iso_german($input) {
    list($jahr, $monat, $tag) = explode("-", $input);

    return sprintf("%02d.%02d.%04d", $tag, $monat, $jahr);
}

##############################################################################
# date_german_iso


function date_german_iso($input) {
    list($tag, $monat, $jahr) = explode(".", $input);

    return sprintf("%04d-%02d-%02d", $jahr, $monat, $tag);
}



//##########################################################################
//   		is_val_pwd_set

function is_val_pwd_set($pwd, $val_pwd) {
    $return = is_value_set($val_pwd);

    if ($return != "") {
        return $return;
    } else if ($pwd != $val_pwd) {
        return '<font size="2" color="#FF0000" face="Arial">Passwoerter stimmen nicht ueberein!</font>';
    }

    return "";
}

##############################################################################
# check_set_password

function check_set_password($password, $val_password, $email) {
    if (is_value_set($password) != "")
        return false;
    if (is_val_pwd_set($password, $val_password) != "")
        return false;
    if (is_mail_set($email) != "")
        return false;

    return true;
}

//##########################################################################
//   			is_password_set

function is_password_set($input, $wrong_password) {
    $return = is_value_set($input);

    if ($return != "") {
        return $return;
    } else if ($wrong_password == true) {
        return '<font size="2" color="#FF0000" face="Arial">Passwort falsch!</font>';
    }

    return "";
}




##############################################################################
# check_username

function check_username($username) {
    $sql = 'SELECT
        				count(*) as anzahl
	    			FROM	
	       				user
	    			WHERE
	        			user_name = "' . $username . '"';

    $result = mysql_query($sql);
    $sql_data = mysql_fetch_array($result);
    $anzahl = $sql_data["anzahl"];

    if ($anzahl > 0) {
        return '<font size="2" color="#FF0000" face="Arial">Username bereits in Benutzung!</font>';
    } else {
        return "";
    }
}



##############################################################################
# get_timestamp

function get_timestamp() {

    $timestamp = time();
    $datum = date("Y-m-d", $timestamp);
    $uhrzeit = date("H:i:s", $timestamp);
    return $datum . " " . $uhrzeit;
}

//##########################################################################
//   			is_email_set_login

function is_email_set_login($input, $wrong_email) {
    $return = is_value_set($input);

    if ($return != "") {
        return $return;
    } else if ($wrong_email == true) {
        return '<font size="2" color="#FF0000" face="Arial">Username falsch!</font>';
    }

    return "";
}

//##########################################################################
//   			check_login

function check_login($uid) {
    require "connect.php";
    if ($uid == "" || $uid == null) {
        echo "Weiterleitung zu Login";
    } else {
	$sql = 'SELECT active FROM user WHERE uid="' . $uid . '"';
	$usr_sql_data = pg_query($con, $sql) or die (pg_last_error($con));
	$status = pg_fetch_result($usr_sql_data, 'status');
    }
}

//##########################################################################
//   			escape_string

function escape_string($string) {
    $string = trim($string);
	$von = array("ä","ö","ü","ß","Ä","Ö","Ü", '"');
	$zu  = array("&auml;","&ouml;","&uuml;","&szlig;","&Auml;","&Ouml;","&Uuml;", '\'');
	$string = str_replace($von, $zu, $string);
	$string = pg_escape_string($string);
	return $string;
}
?>

