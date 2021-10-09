<?php
	// kandidat.php laget av Simen Jensen og Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
    require 'inc/header.php';
    
    $errorMessage = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $user['first_name']." ".$user['last_name'];
        $faculty = sanitizeInput($_POST['faculty']);
        $institute = sanitizeInput($_POST['institute']);
        $information = sanitizeInput($_POST['information']);
        if (!empty($_FILES["picture"]["name"])) {
            $fileName = sanitizeInput(basename($_FILES["picture"]["name"]));
            $fileName = str_replace(array( '..', '/', '\\', ':' ), '', $fileName);
            $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $fileTmpName = $_FILES['picture']['tmp_name'];
            $fileSize = $_FILES['picture']['size'];
            $targetDir = "img/profiles/";
            $uniqueFileName = uniqid().$fileName;
        }
        if (empty($faculty) || empty($institute) || empty($information)) {
			$errorMessage = "Alle feltene må fylles ut!";
		} else {
			try {
                if (!empty($fileName)) {
                    if (!isValidImageType($fileType)) {
                        throw new Exception('Ugyldig filtype!');
                    }
                    if (!isValidImageSize($fileSize)) {
                        throw new Exception('Ugyldig filstørrelse! Maks 500kb.');
                    }
                    updateCandidate($candidate['id'], $faculty, $institute, $information, $uniqueFileName);
                    move_uploaded_file($fileTmpName, $targetDir.$uniqueFileName);
                } else {
                    updateCandidate($candidate['id'], $faculty, $institute, $information, $candidate['candidate_image']);
                }
                if (!$candidate['approved']) {
                    approveCandidate($candidate['id']);
                    deleteCandidateNotification($user['id'], $poll['id']);
                }
				header('Location: valg.php?id='.$poll['id'].'');
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
			}
		}
	}
?>
<main>
    <!-- Wrapper for alt innholdet-->
    <section id="candidate">
        <?php 
            if ($user['id'] == $_SESSION['userId'] && $poll['start_poll'] > date("Y-m-d H:i:s")) {
                echo '<div class="form-error-message">';
					if (!empty($errorMessage)) {
						echo "<img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p style='color:#FF0000;'>" . $errorMessage . "</p>";
                    }
                echo '</div>';
                echo '<form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" enctype="multipart/form-data">';
                echo '<label class="picture-input" for="picture" style="padding: 0;"><img id="picture-input-image" class="register-candidate-icon" src="img/profiles/'.$candidate['candidate_image'].'" alt="'.$user['first_name'].'" style="width: 100%; height: 100%; transition: none; filter: none; margin: 0; border-radius: 50%;"><div class="change-picture-indicator" style="display: flex;"></div></label><input type="file" name="picture" id="picture"><input type="hidden" name="candidate-id" value="'.$candidate['id'].'">';
            } else {
                echo '<img class="candidate-image" src="img/profiles/'.$candidate['candidate_image'].'" alt="'.$user['first_name'].'" "'.$user['last_name'].'" style="width: 190px; height: 190px;">';
            }
        ?> 
        <span class="candidate-name"><?php echo $user['first_name']." ".$user['last_name']; ?></span>
        <span class="candidate-info-label">Fakultet:</span>
        <?php 
            if ($user['id'] == $_SESSION['userId'] && $poll['start_poll'] > date("Y-m-d H:i:s")) {
                echo '<input type="text" name="faculty" id="faculty" value="'.$candidate['faculty'].'" required>';
            } else {
                echo '<span class="candidate-info">'.$candidate['faculty'].'</span>';
            }
        ?> 
        <span class="candidate-info-label">Institutt:</span>
        <?php 
            if ($user['id'] == $_SESSION['userId'] && $poll['start_poll'] > date("Y-m-d H:i:s")) {
                echo '<input type="text" name="institute" id="institute" value="'.$candidate['institute'].'" required>';
            } else {
                echo '<span class="candidate-info">'.$candidate['institute'].'</span>';
            }
        ?>
        <span class="candidate-info-label">Om kandidaten:</span>
        <?php 
            if ($user['id'] == $_SESSION['userId'] && $poll['start_poll'] > date("Y-m-d H:i:s")) {
                echo '<textarea name="information" id="information" required>'.$candidate['information'].'</textarea>';
            } else {
                echo '<p class="candidate-info block-text">'.$candidate['information'].'</p>';
            }
        ?>
        <?php
            if ($poll['start_poll'] > date("Y-m-d H:i:s")) {
                if ($user['id'] == $_SESSION['userId']) {
                    if ($candidate['approved']) {
                        echo '<input class="form-button" type="submit" name="submit" value="Oppdater">';
                    } else {
                        echo '<input class="form-button" type="submit" name="submit" value="Godkjenn">';
                    }
                    echo '</form>';
                    echo '<button class="delete-button" onclick="deleteCandidate('.$candidate['id'].')">Slett</button>';
                } else {
                    echo '<span class="vote-starts-at-message">Muligheten til å stemme starter '.$poll['start_poll'].'</span>';
                }
            } else {
                $vote = getVoteByPollIdAndUserId($candidate['poll_id'], $_SESSION['userId']);
                if ($vote) {
                    if ($vote['candidate_id'] == $candidateId) {
                        echo '<button class="vote-button" onclick="removeVote('.$candidate['id'].')">Trekk stemme</button>';
                    } else {
                        echo '<button class="vote-button" onclick="changeVote('.$candidate['id'].')">Endre stemme</button>';
                    }
                } else {
                    echo '<button class="vote-button" onclick="vote('.$candidate['id'].')">Stem</button>';
                }
            }
        ?>
    </section>
</main>
<script>
    function vote(candidateId) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "inc/create-vote.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                window.location.reload();  
            }
        };
        xmlhttp.send(`id=${candidateId}`);
    }
    function removeVote(candidateId) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "inc/remove-vote.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                window.location.reload();  
            }
        };
        xmlhttp.send(`id=${candidateId}`);
    }
    function changeVote(candidateId) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "inc/change-vote.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                window.location.reload();  
            }
        };
        xmlhttp.send(`id=${candidateId}`);
    }
    function deleteCandidate(id) {
        if (confirm("Er du sikker på at du vil slette ditt kandidatur?")) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "inc/delete-candidate.php", true);
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    window.location.replace("https://godtvalg.kevinnordli.com/valgliste.php");
                }
            };
            xmlhttp.send(`candidateId=${id}`);
        }
    }
</script>
<?php require 'inc/footer.php' ?>