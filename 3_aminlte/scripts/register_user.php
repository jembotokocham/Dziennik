<?php
	//print_r($_POST);
	session_start();
	$errors = [];
	foreach ($_POST as $key => $value){
		//echo "$key: $value<br>";
		if (empty($value)){
			$errors[] = "Pole <b>$key</b> jest wymagane";
		}
	}
	if (!isset($_POST["terms"]))
		$errors[] = "Pole <b>terms</b> jest wymagane";

//dodaj walidację dla hasła i adresu email

	if ($_POST["email"] != $_POST["email2"])
		$errors[] = "Adresy poczty elektronicznej są różne!";

	if ($_POST["pass"] != $_POST["pass2"])
		$errors[] = "Hasła są różne!";

	//print_r($errors);
	if (!empty($errors)){
		//$_SESSION["error_message"] = implode(", ", $errors);
		$_SESSION["error_message"] = implode("<br>", $errors);
		//print_r($_SESSION["error_message"]);
		//echo $_SESSION["error_message"];
		echo "<script>history.back();</script>";
		exit();
	}

	try{
		require_once "./connect.php";
		$stmt = $conn->prepare("INSERT INTO `user` (`email`, `password`,`lastName`,`firstName`,`role_id`) VALUES (?,?,?,?,?);");
		$pass = password_hash($_POST["pass"], PASSWORD_ARGON2ID);
		$stmt->bind_param('ssssi',$_POST["email"],$pass,$_POST["lastName"],$_POST["firstName"],$_POST["role_id"]);
		$stmt->execute();
		//$stmt->affected_rows == 1)
		if ($stmt->affected_rows == 1){
			$_SESSION["success"] = "Prawidłowo dodano użytkownika $_POST[firstName] $_POST[lastName]";
			header("location: ../pages/view/index.php");
		}
	}catch(mysqli_sql_exception $e){
		//echo $e->getMessage();
			$_SESSION["error_message"] = $e->getMessage();
			echo "<script>history.back();</script>";
			exit();
}
?>
