<?php
	// valg.php laget av Leander Didriksen og Dennis Næss. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';
	setlocale(LC_TIME, "nb_NO.utf8");
?>
	<main>
		<!-- Wrapper for alt innholdet-->
		<section id="poll">
			<a class="back-button" href="/valgliste.php"><img src="img/icons/arrow-left.svg">Gå tilbake</a>
			<h1><?php echo $poll['title'];?></h1>
			<span class="description">
				<?php 
					if ($poll['start_poll'] > date("Y-m-d H:i:s")) {
						echo "<span class='description-date'>Valget starter ".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['start_poll']))."</span>";
					} else {
						echo "<span class='description-date'>Valget slutter ".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['end_poll']))."</span>";
					}
					if (isset($_GET['invited'])) {
						$invited = sanitizeInput($_GET['invited']);
						echo "<span style='color: green; margin-top: 16px; font-weight: 700;'>Invitasjon om kandidatur sendt til: ".$invited."</span>";
					}
				?>
			</span>
			<h2>Kandidater</h2>
			<!-- Liste med kandidater -->
			<ul class="candidate-list">
				<button class="candidate-button-previous"><img src="img/icons/arrow-circle-left.svg"
						alt="forrige kandidat"></button>
				<button class="candidate-button-next"><img src="img/icons/arrow-circle-right.svg" alt="neste kandidat"></button>
				<?php
					if ($poll['start_poll'] > date("Y-m-d H:i:s")) { // Gi muligheten til å registrere nye kandidater om valget ikke har startet enda
						echo '<li class="candidate-list-item"><img class="register-candidate-icon" src="img/icons/user-plus.svg" style="width: 120px; height: 120px;"><span class="candidate-name">Registrer ny kandidat</span><a class="candidate-button" href="/registrer-kandidat.php?id='.$_GET['id'].'">Registrer</a></li>';
					}
				?>
				<?php
					$candidates = getCandidatesByPollId($_GET['id']);
					if ($candidates) {
						foreach ($candidates as $candidate) {
							if ($candidate['approved']) { // Vis kandidaten om han eller hun har godkjent kandidaturet
								$user = getUserById($candidate['user_id']);
								echo '<li class="candidate-list-item"><img class="candidate-picture" src="img/profiles/'.$candidate['candidate_image'].'" style="width: 120px; height: 120px;"><span class="candidate-name">'.$user['first_name'].' '.$user['last_name'].'</span><a class="candidate-button" href="/kandidat.php?id='.$candidate['id'].'">Les mer</a></li>';
							}
						}
					}
				?>
			</ul>
		</section>
	</main>
<?php require 'inc/footer.php' ?>
