<?php
  // resultat.php laget av Kevin André Torgrimsen Nordli. Sist endret 01/06/2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';
?>
<main>
    <a class="back-button" href="/valgliste.php"><img src="img/icons/arrow-left.svg">Gå tilbake</a>
	<header id="header">
		<h1>Resultat</h1>
        <span class="description"><?php echo $poll['title'];?></span>
	</header>
	<section>
        <div class="table-container">
        <?php
            $candidates = getCandidatesByPollId($pollId);
            if ($candidates) {
                $candidatesTable = "<table>";
                $candidatesTable .= "<tr><th>Kandidat</th><th>Stemmer</th></tr>";
                foreach ($candidates as $candidate) {
                    $votes = getVotesByCandidateId($candidate['id']);
                    $numberOfVotes = ($votes !== false) ? count($votes) : 0; // Om det ikke er registrert noen stemmer, så sett $numberOfVotes til 0
                    $user = getUserById($candidate['user_id']);
                    $candidatesTable .= "<tr><td>".$user['email']."</td><td>".$numberOfVotes."</td></tr>";
                }
                $candidatesTable .= "</table>";
                echo $candidatesTable;
            }
        ?>
        </div>
	</section>
</main>
<?php require 'inc/footer.php' ?>