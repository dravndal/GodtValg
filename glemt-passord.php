<?php
  // glemt-passord.php laget av Daniel Ravndal og Leander Didriksen. Sist endret 01.06.2021 av Leander Didriksen.
	require 'inc/header.php';

    $errorMessage = "";
    $statusMessage = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = sanitizeInput($_POST["email"]);
        if (empty($email)) {
            $errorMessage = "Alle feltene må fylles ut!";
        } else {
            try {
                sendPasswordResetToken($email);
                $statusMessage = "Hvis det finnes en bruker med denne e-postadressen kan du nå sjekke din e-post! Husk å se i søppelpost hvis du ikke finner den.";;
            } catch (Exception $e) {
                $errorMessage = $e;
            }

        }

    }
?>
	<main>
		<!-- wrapper til skjemaet -->
		<section id="forgot-password">
			<h1>Glemt passord</h1>
            <span class="description">Skriv din e-postadresse så sender vi deg en link for å tilbakestille passordet ditt.</span>
			<div class="form-error-message">
				<?php
                    if (!empty($statusMessage)) {
                        echo '<p style="color:green;">' . $statusMessage . '</p>';
                    }
					if (!empty($errorMessage)) {
						echo "<img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p style='color:#FF0000;'>" . $errorMessage . "</p>";
					}
				?>
			</div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label for="email">Epost</label>
                <input type="email" placeholder="eksempel@epost.no" name="email" id="email" autofocus="autofocus" onfocus="this.select()" required>
                <input id="forgot-password-submit" class="form-button" type="submit" name="submit" value="Tilbakestill passord">
            </form>
            <span>Eller <a href="./logg-inn.php">klikk her</a> for å gå tilbake.</span>
	    </section>
	</main>
<?php require 'inc/footer.php' ?>
