<?php
require_once 'app/init.php';

	session_start();
	if(!isset($_SESSION['access_token'])) {
	header('Location: google_login.php');
	exit();
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>User Profile</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css"> 
</head>
<body>
<div class="container" style="margin-top: 100px">
	<div class="row">
		<div class="col-md-3">
			<img src="img/profile-default.png" style="width: 90%">
		</div>

		<div class="col-md-9">
			<div class="user-name"></div><a href="index.php?q=#">back</a>
			<table class="table table-hover table-bordered">
				<tbody>
					<tr>
						<td>ID</td>
						<td><?php echo $_SESSION['id'] ?></td>
					</tr>
					<tr>
						<td>Display Name</td>
						<td><?php echo $_SESSION['display_name'] ?></td>
					</tr>
					<tr>
						<td>First Name</td>
						<td><?php echo $_SESSION['given_name'] ?></td>
					</tr>
					<tr>
						<td>Last Name</td>
						<td><?php echo $_SESSION['family_name'] ?></td>
					</tr>
					<tr>
						<td>Email</td>
						<td><?php echo $_SESSION['email'] ?></td>
					</tr>
					<tr>
						<td>Gender</td>
						<td><?php echo $_SESSION['gender'] ?></td>
					</tr>
					<tr>
						<td>School</td>
						<td><?php echo $_SESSION['school'] ?></td>
					</tr>
					<tr>
						<td>Department</td>
						<td><?php echo $_SESSION['department'] ?></td>
					</tr>
					<tr>
						<td>Current Location</td>
						<td><?php echo $_SESSION['curr_location'] ?></td>
					</tr>
					<tr>
						<td>Previous Location</td>
						<td><?php echo $_SESSION['prev_location'] ?></td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>
</div>
</body>
</html>