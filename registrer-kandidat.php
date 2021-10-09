<?php
	// registrer-kandidat.php laget av Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
	require_once 'inc/header.php';

	$errorMessage = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$email = sanitizeInput($_POST['email']);
        $faculty = sanitizeInput($_POST['faculty']);
        $institute = sanitizeInput($_POST['institute']);
        $information = sanitizeInput($_POST['information']);
        $pollId = sanitizeInput($_POST['poll-id']);
        $fileName = sanitizeInput(basename($_FILES["picture"]["name"]));
        $fileName = str_replace(array( '..', '/', '\\', ':' ), '', $fileName);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileTmpName = $_FILES['picture']['tmp_name'];
        $fileSize = $_FILES['picture']['size'];
        $targetDir = "img/profiles/";
        $uniqueFileName = uniqid().$fileName;
        if (empty($email) || empty($faculty) || empty($institute) || empty($information) || empty($pollId) || empty($fileName)) {
			$errorMessage = "Alle feltene må fylles ut!";
		} else if (strlen($faculty) > 45 || strlen($institute) > 45 || strlen($information) > 1000) {
			$errorMessage = "For mange tegn i et felt!";
		} else {
			try {
                if (!isValidImageType($fileType)) {
                    throw new Exception('Ugyldig filtype!');
                }
                if (!isValidImageSize($fileSize)) {
                    throw new Exception('Ugyldig filstørrelse! Maks 2MB.');
                }
				createCandidate($email, $pollId, $faculty, $institute, $information, $uniqueFileName);
                move_uploaded_file($fileTmpName, $targetDir.$uniqueFileName);
				$candidate = getCandidateByEmail($email, $pollId);
				if ($email !== $_SESSION['email']) {
					$reciever = getUserByEmail($email);
					createNotification($_SESSION['userId'], $reciever['id'], $pollId, $candidate['id'], 1);
					header('Location: valg.php?id='.$pollId.'&invited='.$email);
				} else { // Hvis brukeren nominerer seg selv
					approveCandidate($candidate['id']);
					header('Location: valg.php?id='.$pollId);
				}
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
		}
	}
?>
	<main>
		<section id="signup">
			<h1>Registrer ny kandidat</h1>
			<div class="form-error-message">
				<?php
					if (!empty($errorMessage)) {
						echo "<img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p style='color:#FF0000;'>" . $errorMessage . "</p>";
					}
				?>
			</div>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype='multipart/form-data'>
                <label class="picture-input" for="picture">
                    <img id="picture-input-image" class="register-candidate-icon" src="img/icons/upload.svg" style="width: 64px; height: 64px;">
                    <span id="picture-input-text">Last opp bilde</span>
                    <div class="change-picture-indicator"></div>
                </label>
                <input type="file" name="picture" id="picture">
				<label for="email">E-post</label>
				<input type="email" placeholder="eksempel@valg2021.usn" name="email" id="email" autofocus="autofocus" onfocus="this.select()" required>
                <label for="faculty">Fakultet</label>
				<input type="text" placeholder="Informasjonsteknologi" name="faculty" id="faculty" required>
                <label for="institute">Institutt</label>
				<input type="text" placeholder="Institutt for mikrosystemer" name="institute" id="institute" required>
                <label for="information">Informasjon</label>
				<textarea placeholder="Skriv litt om kandidaten" name="information" id="information" required></textarea>
                <input type="hidden" name="poll-id" value="<?php if (isset($_GET['id'])) { echo $_GET['id']; } else { echo $pollId; }?>">
				<input class="form-button" type="submit" name="submit" value="Registrer">
			</form>
			<span>Eller <a href="/valgliste.php">gå tilbake.</a></span>
		</section>
	</main>
<?php require 'inc/footer.php' ?>
