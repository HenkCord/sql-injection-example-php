<?php
	include('Config.class.php');
	$error_message = null;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$login = $_POST['login'];
		$password = $_POST['password'];
		$sql = "SELECT id FROM users WHERE login = '$login' and password = '$password'";
		$result = mysqli_query($db, $sql);
		
		if($result) {
			echo $result;
		} else {
			$error_message = mysql_error($db);
		}
	}
	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Sql injection example</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<link href="/css/style.css" rel="stylesheet">
	</head>
	<body>
		<div class="container">
			<div class="row justify-content-center mainLayout">
				<div class="col-4 align-self-center mainLayout__container">
					<form method="post" action="/">
						<?php if (!empty($error_message)) { ?>
							<div class="alert alert-danger" role="alert">
								<?=$error_message?>
							</div>
						<?php } ?>
						<div class="form-group">
							<label for="inputLogin">Login</label>
							<input type="text" name="login" class="form-control" id="inputLogin" placeholder="Enter login">
						</div>
						<div class="form-group">
							<label for="inputPassword">Password</label>
							<input type="password" name="password" class="form-control" id="inputPassword" placeholder="Enter Password">
						</div>
						<button type="submit" class="btn btn-primary">Sign in</button>
					</form>
				</div>
			</div>
		</div>
	</body>
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap.min.js"></script>
</html>
	