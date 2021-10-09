<?php
  // min-side.php laget av Daniel Ravndal. Sist endret 01/06/2021 av Daniel Ravndal.
	require 'inc/header.php';
?>
<main>
	<header id="header">
		<h1>Din side</h1>
		<span>Her kan du se over, endre passord eller slette profilen din.</span>
	</header>
	<section class="profile-section profile-section-filled-background">
		<div class="user-info">
			<span class="user-name"><?php echo $firstName . ' ' . $lastName; ?></span>
			<span class="user-email"><?php echo $email; ?></span>
			<span class="user-type">Brukertype: <?php echo $userType; ?></span>
		</div>
		<div class="button-wrapper">
			<a class="profile-button" href="/endre-passord.php">Endre passord</a>
			<button class="profile-button" onclick="deleteUser()" style="background-color:#ff3b3b;">Slett bruker</button>
		</div>
	</section>
</main>
<script>
	function deleteUser() {
		if (confirm("Er du sikker på at du vil slette brukeren din?")) { // Confirm-boks som spør om brukeren ønsker å slette seg
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.open("GET", "inc/delete-user.php", true);
			xmlhttp.onreadystatechange = function() {
				window.location.replace('https://godtvalg.kevinnordli.com'); // Redirecter til hjemmesiden
			};
			xmlhttp.send();
		}
	}
</script>
<?php require 'inc/footer.php' ?>
