<?php
  // lag-nytt-passord.php laget av Daniel Ravndal, Leander Didriksen og Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';

	$errorMessage = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$password = sanitizeInput($_POST["password"]);
    	$confirmPassword = sanitizeInput($_POST["confirmpassword"]);
		if (empty($password) || empty($confirmPassword)) {
			$errorMessage = "Alle feltene må fylles ut!";
		} else if ($password !== $confirmPassword) {
			$errorMessage = "Passordene er ikke like! Vennligst prøv på nytt.";
		} else {
      		try {
                $selector = $_POST['selector'];
                $validator = $_POST['validator'];
                $token = getPasswordResetToken($selector);
                checkPasswordResetToken($validator, $token['token']);
				changeForgottenPassword($token['email'], $password);
                deletePasswordResetToken($token['email']);
				header("location: /logg-inn.php?status=passwordchanged");
				exit();
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
    	}
	} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $selector = $_GET['selector'];
        $validator = $_GET['validator'];
    }
?>
	<main>
		<!-- wrapper til skjemaet -->
		<section id="change-password">
			<h1>Lag nytt passord</h1>
			<div class="form-error-message">
				<?php
					if (!empty($errorMessage)) {
						echo "<img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p style='color:#FF0000;'>" . $errorMessage . "</p>";
					}
				?>
			</div>
            <?php
                if (empty($selector) || empty($validator)) {
                    $errorMessage = 'Kunne ikke validere forespørselen!';        
                } else {
                    if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) { // Sjekk at selector og validator er hexadecimaler
                        ?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <input type="hidden" name="selector" value="<?php echo $selector; ?>">
                            <input type="hidden" name="validator" value="<?php echo $validator; ?>">
                            <label for="password">Nytt passord (minst 8 tegn, ett tall, én liten og én stor bokstav)</label>
                            <input type="password" placeholder="********" name="password" id="password" required>
                            <label for="confirmpassword">Gjenta nytt passord</label>
                            <input type="password" placeholder="********" name="confirmpassword" id="confirmpassword" required>
                            <input id="login-submit" class="form-button" type="submit" name="submit" value="Endre">
                        </form>
                        <?php
                    }
                }
            ?>
            
		</section>
	</main>
<?php require 'inc/footer.php' ?>
