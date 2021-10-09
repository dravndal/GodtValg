<?php
	// logg-inn.php laget av Daniel Ravndal og Simen Jensen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';

	$errorMessage = "";
	$statusMessage = "";

	if (isset($_GET['status'])) {
		if ($_GET['status'] === 'sessionexpired') {
			$errorMessage = "Sesjonen har utløpt! Vennligst logg inn på nytt.";
		} else if ($_GET['status'] === 'passwordchanged') {
			$statusMessage = "Passord endret! Vennligst logg inn på nytt.";
		}
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$email = sanitizeInput($_POST["email"]);
		$password = sanitizeInput($_POST["password"]);
		if (empty($email) || empty($password)) {
			$errorMessage = "Alle feltene må fylles ut!";
		} else {
			try {
				loginUser($email, $password);
				header("location: /index.php");
				exit();
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
		}
	}
?>
	<main>
		<!-- wrapper til skjemaet -->
		<section id="login">
			<h1>Logg inn</h1>
			<span class="description">Fyll inn e-post og passord.</span>
			<div class="form-error-message">
				<?php
					if (!empty($statusMessage)) {
						echo '<p style="color:green;">' . $statusMessage . '</p>';
					}
					if (!empty($errorMessage)) {
						echo '<img src="img/icons/exclamation-triangle.svg" alt="" style = "width: 20px; height: 20px;" ><p style="color:#FF0000;">' . $errorMessage . '</p>';
					}
				?>
			</div>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<label for="email">E-post</label>
				<input type="email" placeholder="eksempel@epost.no" name="email" id="email" autofocus="autofocus" onfocus="this.select()" required>
					<label for="password">Passord</label>
				<input type="password" placeholder="********" name="password" id="password" required>
				<a class="forgot-password-link" href="/glemt-passord.php">Glemt passordet?</a>
				<input id="login-submit" class="form-button" type="submit" name="submit" value="Logg inn">
			</form>
			<span>Ikke registrert? <a href="registrer-bruker.php">Klikk her</a> for å registrere deg.</span>
		</section>
	</main>
<?php require 'inc/footer.php' ?>
