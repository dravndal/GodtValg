<?php
  // endre-passord.php laget av Daniel Ravndal og Leander Didriksen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';

	$errorMessage = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$oldPassword = sanitizeInput($_POST["oldpassword"]);
    	$newPassword = sanitizeInput($_POST["newpassword"]);
    	$confirmPassword = sanitizeInput($_POST["confirmpassword"]);
		if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
			$errorMessage = "Alle feltene må fylles ut!";
		} else if ($newPassword !== $confirmPassword) {
			$errorMessage = "Passordene er ikke like! Vennligst prøv på nytt.";
		} else {
      		try {
				changePassword($email, $oldPassword, $newPassword);
				session_start();
				session_unset();
				session_destroy();
					header("location: ./logg-inn.php?status=passwordchanged");
					exit();
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
    	}
	}
?>
	<main>
		<!-- wrapper til skjemaet -->
		<section id="change-password">
			<h1>Endre passord</h1>
			<div class="form-error-message">
				<?php
					if (!empty($errorMessage)) {
						echo "<img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p style='color:#FF0000;'>" . $errorMessage . "</p>";
					}
				?>
			</div>
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="gammeltpassword">Ditt nåværende passord</label>
				<input type="password" placeholder="********" name="oldpassword" id="oldpassword" required>
				<label for="password">Nytt passord (minst 8 tegn, ett tall, én liten og én stor bokstav)</label>
				<input type="password" placeholder="********" name="newpassword" id="newpassword" required>
        <label for="confirmpassword">Gjenta nytt passord</label>
				<input type="password" placeholder="********" name="confirmpassword" id="confirmpassword" required>
				<input id="login-submit" class="form-button" type="submit" name="submit" value="Endre">
      </form>
      <span>Eller <a href="./min-side.php">klikk her</a> for å gå tilbake.</span>
		</section>
	</main>
<?php require 'inc/footer.php' ?>
