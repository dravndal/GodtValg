<?php
  // varsler.php laget av Daniel Ravndal og Dennis Næss. Sist endret 01/06/2021 av Dennis Næss.
	require 'inc/header.php';
	$notifications = getNotificationsByUserId($_SESSION['userId']);
?>
<main>
	<header id="header">
		<h1>Varsler</h1>
	</header>
	<section>
		<?php
			if (count($notifications) > 0) { // Hvis det er minst 1 varsel
				$notificationList = '<ul class="notification-list">';
				foreach ($notifications as $notification) {
					updateSeenNotification($notification['id']); // Legg til i databasen at varsel har blitt sett
					$sender = getUserById($notification['sender']); // Hent bruker basert på sender (som er bruker-id til den som har sendt)
					if ($notification['poll_id']) {
						$poll = getPollById($notification['poll_id']);
					}
					/* Switch for å håndtere hvilken notifikasjonstype notifikasjonen har */
					switch ($notification['notification_type']) {
						case 1:
							$notificationList .= '<li class="notification-list-item"><span class="notification-sender">'.$sender['email'].'</span> har nominert deg som kandidat til valget "<span class="notification-poll">'.$poll['title'].'</span>"<a class="notification-link" href="/kandidat.php?id='.$notification['candidate_id'].'">Se kandidatur</a></li>';
							break;
						case 2:
							$notificationList .= '<li class="notification-list-item"><span class="notification-congratulations">Gratulerer!</span> Du har vunnet valget "<span class="notification-poll">'.$poll['title'].'</span>"<a class="notification-link" href="/resultat.php?id='.$notification['poll_id'].'">Se resultat</a></li>';
							break;
						default:
							break;
					}
				}
				$notificationList .= '</ul>';
				echo $notificationList;
			} else {
				echo '<span>Ingen varsler.</span>';
			}
		?>
	</section>
</main>
<script>
</script>
<?php require 'inc/footer.php' ?>
