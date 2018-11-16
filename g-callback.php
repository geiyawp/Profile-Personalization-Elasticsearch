<?php
	require_once 'app/init.php';
	require_once "inc/g_config.php";

	if (isset($_SESSION['access_token']))
		$go->setAccessToken($_SESSION['access_token']);
	else if (isset($_GET['code'])) {
		$token = $go->fetchAccessTokenWithAuthCode($_GET['code']);
		$_SESSION['access_token'] = $token;
	} else {
		header('Location: google_login.php');
		exit();
	}

	$oAuth = new Google_Service_Oauth2($go);
	$userData = $oAuth->userinfo_v2_me->get();
	$plus = new Google_Service_Plus($go);
	$user_id = $userData['id']; // getting google user id
	$me = $plus->people->get('me');
	
	$place = $me->getPlacesLived(); // getting places data from the API
	$preMail = $me->getEmails(); // getting email data
	$org = $me->getOrganizations(); // getting organization data for education

 
	
	if (!empty($place[0]['primary'])) {
		$curr_location = $place[0]['value'];  // getting current location and store to variable
		$prev_location = $place[1]['value'];  // getting previous location and store to variable
	}
	else {
		$curr_location = "";
		$prev_location = $place[0]['value'];
	}



	$email = ($preMail[0]['value']);	// store email to variable
	$school = $org[0]['name'];		// store school to variable	
	$department = $org[0]['title'];		// store the department to variable

	// checking value in every variable to make sure there's no null value, cuz it will
	// made elasticsearch error when indexing, also other errors u don't want to bother

	if (!empty($me['id'])) {
		$_SESSION['id'] = $me['id'];
	}
	else {
		$_SESSION['id'] = '-';
	}
	if (!empty($email)) {
		$_SESSION['email'] = $email;
	}
	else {
		$_SESSION['email'] = '-';
	}
	if (!empty($userData['gender'])) {
		$_SESSION['gender'] = $userData['gender'];
	}
	else {
		$_SESSION['gender'] = '-';
	}
	if (!empty($me['displayName'])) {
		$_SESSION['display_name'] = $me['displayName'];
	}
	else {
		$_SESSION['display_name'] = '-';
	}
	if (!empty($userData['familyName'])) {
		$_SESSION['family_name'] = $userData['familyName'];
	}
	else {
		$_SESSION['family_name'] = '-';
	}
	if (!empty($userData['givenName'])) {
		$_SESSION['given_name'] = $userData['givenName'];
	}
	else {
		$_SESSION['given_name'] = '-';
	}
	if (!empty($school)) {
		$_SESSION['school'] = $school;
	}
	else {
		$_SESSION['school'] = '-';
	}
	if (!empty($department)) {
		$_SESSION['department'] = $department;
	}
	else {
		$_SESSION['department'] = '-';
	}
	if (!empty($curr_location)) {
		$_SESSION['curr_location'] = $curr_location;
	}
	else {
		$_SESSION['curr_location'] = '-';
	}
	if (!empty($prev_location)) {
		$_SESSION['prev_location'] = $prev_location;
	}
	else {
		$_SESSION['prev_location'] = '-';
	}
	
	// checking whether the user already exist in users index or not
	// if the users already exist then the system won't index em again
	// if the user has not exist in the index, then the system will index the user data
	$params = [
	    'index' => 'users',
	    'type' => 'user_mapping',
	    'size' => '30',
	    'body' => [						
			'query' => [
            	'match' => [
            		'id' => $_SESSION['id']
            ]
        ]
    ]
];

	if (!empty($_SESSION['id'])){
		$user = (array) $client->search($params);	
	}
	
	
	if($user['hits']['total'] >=1) {

		$_SESSION['user_status'] = 'Status: User already exist';
	}
	else {
	$indexed = $client->index([
      'index' => 'users',
      'type' => 'user_mapping',
      'id' => $_SESSION['id'],
      'body' => [
          'id' => $_SESSION['id'],
          'display_name' => $_SESSION['display_name'],
          'email' => $_SESSION['email'],
          'given_name' => $_SESSION['given_name'],
          'family_name' => $userData['familyName'],
          'prev_location' => $_SESSION['prev_location'],
          'school' => $_SESSION['school'],
          'department' => $_SESSION['department'],
          'curr_location' => $_SESSION['curr_location'],
          'gender' => $_SESSION['gender']
      ]
    ]);
	
	}
	if($indexed) {
     $_SESSION['user_status'] = 'Status: user indexed';
  }

	header('Location: index.php?q=');		// redirect to index page
	exit();
?>
