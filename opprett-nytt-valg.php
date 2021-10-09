<?php
  // opprett-nytt-valg.php laget av Kevin André Torgrimsen Nordli. Sist endret 01/06/2021 av Kevin André Torgrimsen Nordli.
 	require 'inc/header.php';

    $errorMessage = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$title = sanitizeInput($_POST["title"]);
		$start = $_POST["start-date"];
		$end = $_POST["end-date"];
		if (empty($title) || empty($start) || empty($end)) {
			$errorMessage = "Alle feltene må fylles ut!";
		} else if ($start > $end) {
			$errorMessage = "Sluttdatoen må være etter startdatoen!";
		} else {
			try {
				createPoll($title, $start, $end);
				header('Location: administrer-valg.php');
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
		}
	}
?>
<main>
	<a class="back-button" href="/administrasjon.php"><img src="img/icons/arrow-left.svg">Gå tilbake</a>
	<header id="header">
		<h1>Opprett nytt valg</h1>
	</header>
    <section id="create-new-poll-section" class="administration-section" style="margin-bottom: 16px;">
        <div class="form-error-message">
            <?php
                if (!empty($errorMessage)) {
                    echo "<img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p style='color:#FF0000;'>" . $errorMessage . "</p>";
                }
            ?>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <label for="title">Tittel</label>
            <input class="styled-input" type="text" name="title" id="title" placeholder="Tittel">
            <label for="start">Start</label>
			<input class="styled-input clickable" type="text" name="start-date" id="start-date" placeholder="Velg en startdato">
			<!-- PS: Dette var koden før vi brukte flatpickr
			Dessverre er det ikke alle nettlesere som støtter datetime-local input, men det kommer snart støtte til Firefox, som betyr at alle de største nettleserne har støtte for det snart, som en workaround har vi laget en pattern-attributt som validerer i front-end, mens backend-en validerer for dato format også. -->
            <!-- Eksempel på input før flatpickr: <input class="styled-input" type="datetime-local" name="start" id="start" placeholder="åååå-mm-dd tt:mm" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}"> -->
            <label for="end">Slutt</label>
			<input class="styled-input clickable" type="text" name="end-date" id="end-date" placeholder="Velg en sluttdato">
            <!-- <input class="styled-input" type="datetime-local" name="end" id="end" placeholder="åååå-mm-dd tt:mm" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}"> -->
            <input class="form-button" type="submit" value="Lag">
        </form>
    </section>
</main>
<?php require 'inc/footer.php' ?>
