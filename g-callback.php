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
	$user_id = $userData['id'];
	$me = $plus->people->get('me');
	
	$place = $me->getPlacesLived();
	$preMail = $me->getEmails();
	$org = $me->getOrganizations();

// ===============Kodingan lama current location========================
	
	// $curr_location = $place[0]['value'];
	// $preLocation = array();
//======================================================================
 
	//	Kodingan location baru
	if (!empty($place[0]['primary'])) {
		$curr_location = $place[0]['value'];
		$prev_location = $place[1]['value'];
	}
	else {
		$curr_location = "";
		$prev_location = $place[0]['value'];
	}

	// print_r($curr_location);
	// echo "<br>";
	// print_r($prev_location);

// ==========Kodingan lama buat prev_location===============
	
	// foreach ($place as $k) {
	// 	if (isset($preLocation)) {
	// 		array_push($preLocation, $k['value']);
	// 	}	
	// }

//

	// unset($preLocation[0]);
	// $prev_location = implode(", ", $preLocation);

	// print_r($prev_location);

// ===========================================================

	$email = ($preMail[0]['value']);
	$school = $org[0]['name'];
	$department = $org[0]['title'];

	//harus di check tiap variable biar gak null, klo bisa ganti empty aja soalnya
	// ada empty string juga

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
	
	
	//var_dump($user);
	
	if($user['hits']['total'] >=1) {

		$_SESSION['user_status'] = 'Status: User already exist';
		// echo("Profile already exist!");
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

	header('Location: index.php?q=');
	exit();
?>