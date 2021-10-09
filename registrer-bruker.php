<?php
	// registrer-bruker.php laget av Daniel Ravndal og Leander Didriksen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';

	$errorMessage = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$firstName = sanitizeInput($_POST["firstname"]);
		$lastName = sanitizeInput($_POST["lastname"]);
		$email = sanitizeInput($_POST["email"]);
		$date = sanitizeInput($_POST["birthdate"]);
		$password = sanitizeInput($_POST["password"]);
		$confirmPassword = sanitizeInput($_POST["confirmpassword"]);
		if (empty($firstName) || empty($lastName) || empty($email) || empty($date) || empty($password)) {
			$errorMessage = "Alle feltene må fylles ut!";
		} else if ($password !== $confirmPassword) {
			$errorMessage = "Passordene er ikke like! Vennligst prøv på nytt.";
		} else {
			try {
				createUser($email, $password, $lastName, $firstName, $date);
				loginUser($email, $password);
				header('Location: index.php');
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
		}
	}
?>
	<main>
		<section id="signup">
			<h1>Registrer ny bruker</h1>
			<span class="description">Alle feltene må fylles ut.</span>
			<div class="form-error-message">
				<?php
					if (!empty($errorMessage)) {
						echo "<img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p style='color:#FF0000;'>" . $errorMessage . "</p>";
					}
				?>
			</div>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); // Sender dataen via post-request til samme side så vi slipper å ha en ekstra fil ?>">
				<label for="firstname">Fornavn</label>
				<input type="text" placeholder="Ola" name="firstname" id="firstname" autofocus="autofocus" onfocus="this.select()" required>
				<label for="lastname">Etternavn</label>
				<input type="text" placeholder="Nordmann" name="lastname" id="lastname" required>
				<label for="email">E-post</label>
				<input type="email" placeholder="eksempel@valg2021.usn" name="email" id="email" required>
				<label for="birthdate">Fødselsdato</label>
				<input class="clickable" type="text" name="birthdate" id="birthdate" placeholder="Velg en dato"> <!-- Placeholder er for Safari fallback, siden Safari ikke støtter date input. De har skrevet at neste versjon skal ha støtte for date. -->
				<label for="password">Passord (minst 8 tegn, ett tall, én liten og én stor bokstav)</label>
				<input type="password" placeholder="********" name="password" id="password" required>
				<label for="confirmpassword">Skriv passord på nytt</label>
				<input type="password" placeholder="********" name="confirmpassword" id="comfirmpassword" required>
				<input class="form-button" type="submit" name="submit" value="Registrer">
			</form>
			<span>Allerede registrert? <a href="logg-inn.php">Logg inn her.</a></span>
		</section>
	</main>
<?php require 'inc/footer.php' ?>
