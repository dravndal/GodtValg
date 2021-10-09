<?php
  // header.php laget av Kevin André Torgrimsen Nordli og Daniel Ravndal. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli
  session_start();
  $currentPage = strtolower(basename($_SERVER['PHP_SELF'])); // Henter ut hvilken side brukeren er på, og for sikkerhets skyld gjør vi det om til liten skrift for å unngå konflikter hvis det skulle oppstå
  $isLoggedIn = isset($_SESSION['userId']); // Sjekker om sesjonsvariabelet userId finnes. Om det gjør det vet vi at brukeren er logget inn

  if ($isLoggedIn) {
    require_once 'models/notification.php';
    if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] > 1800)) {
      // Logger bruker ut om det er mer enn en halvtime siden siste aktivitet
      session_start();
      session_unset();
      session_destroy();
			header("location: ./logg-inn.php?status=sessionexpired"); // Sett query string-en status til sessionexpired så vi kan gjøre noe på logg-inn.php når script-et mottar get-variabelet
			exit();
    }
    $_SESSION['lastActivity'] = time(); // Sett lastActivity sesjonsvariablet til timestampet nå (sekunder siden 01.01.1970) slik at vi kan se hvor lenge brukeren har vært på siden neste gang de bytter
    $email = $_SESSION['email'];
		$firstName = $_SESSION['firstName'];
    $lastName = $_SESSION['lastName'];
    /* Enklere å sette teksten på brukertype med denne associative array-en fremfor å hente det fra databasen (sparer nettverkstrafikk og siden vi vet at det er få rader og verdiene er konstante så burde ikke dette være et problem) */
    $userTypes = array(1 => 'Bruker', 2 => 'Kontrollør', 3 => 'Administrator');
    $userType = $userTypes[$_SESSION['userType']];
  }

  /* Denne Switch-en er et nødvendig onde siden vi bruker forskjellige css-filer for hver side. I et virkelig prosjekt hvor vi ikke MÅTTE hatt en side hver (pga. individuelle karakterer) ville vi heller hatt én monolitisk css-fil med alle reglene i. Vi har også valgt å sette alle require-statements her siden noen sider krever dynamisk tittel (i tab-en). Dette er nok ikke den peneste måten å gjøre dette på, men for å være konsistent så gjorde vi dette på alle sidene (redirects, setting av tittel og pagestyle). Som sagt ville jeg nok heller satt alle CSS-filene i én fil og satt tittelen dynamisk gjennom en buffer i et ekte prosjekt. */
  $pageStyle = "";
  switch ($currentPage) {
    case "index.php":
      $pageStyle = "hjem";
      $title = 'Hjem';
      break;
    case "valg.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if (!isset($_GET['id'])) {
        header("location: /index.php");
        exit();
      }
      require_once 'models/poll.php';
      require_once 'models/candidate.php';
      require_once 'models/user.php';
      require_once 'helpers/utility.php';
      $pageStyle = "valg";
      $pollId = sanitizeInput($_GET['id']);
      $poll = getPollById($pollId);
      $title = $poll['title'];
      if ($poll['end_poll'] <= date("Y-m-d H:i:s")) {
        header("location: /resultat.php?id=".$poll['id']);
        exit();
      }
      break;
    case "registrer-bruker.php":
      if ($isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      }
      require_once 'helpers/utility.php';
	    require_once 'models/user.php';
      $pageStyle = "logg-inn";
      $title = 'Registrer bruker';
      break;
    case "logg-inn.php":
      if ($isLoggedIn) {
        header("location: /index.php");
        exit();
      }
      require_once 'helpers/utility.php';
	    require_once 'models/user.php';
      $pageStyle = "logg-inn";
      $title = 'Logg inn';
      break;
    case "endre-passord.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      }
      require_once 'helpers/utility.php';
	    require_once 'models/user.php';
      $pageStyle = "logg-inn";
      $title = 'Endre passord';
      break;
    case "glemt-passord.php":
      require 'helpers/utility.php';
      require 'models/user.php';
      $pageStyle = "logg-inn";
      $title = 'Glemt passord';
      break;
    case "lag-nytt-passord.php":
      if ($isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      }
      require_once 'helpers/utility.php';
      require_once 'models/user.php';
      $pageStyle = "logg-inn";
      $title = 'Lag nytt passord';
      break;
    case "registrer-kandidat.php":
      if (!$isLoggedIn) {
        header("location: /index.php");
        exit();
      }
      require_once 'helpers/utility.php';
      require_once 'models/candidate.php';
      require_once 'models/notification.php';
      require_once 'models/user.php';
      $pageStyle = "logg-inn";
      $title = 'Registrer kandidat';
      break;
    case "min-side.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      }
      $pageStyle = "min-side";
      $title = $firstName . " " . $lastName;
      break;
    case "varsler.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if (!isset($_GET['id'])) {
        header("location: /index.php");
        exit();
      } else if ($_GET['id'] !== $_SESSION['userId']) {
        header("location: /index.php");
        exit();
      }
      require_once 'models/notification.php';
      require_once 'models/user.php';
      require_once 'models/poll.php';
      $pageStyle = "varsler";
      $title = 'Varsler';
      break;
    case "administrer-brukere.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if ($_SESSION['userType'] != 3) {
        header("location: /index.php");
        exit();
      }
      $pageStyle = "administrasjon";
      $title = 'Administrer brukere';
      break;
    case "administrer-valg.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if (!$_SESSION['userType'] > 1) {
        header("location: /index.php");
        exit();
      }
      $pageStyle = "administrasjon";
      $title = 'Administrer valg';
      break;
    case "administrer-resultat.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if (!$_SESSION['userType'] > 1) {
        header("location: /index.php");
        exit();
      } else if (!isset($_GET['id'])) {
        header("location: /index.php");
        exit();
      }
      require_once 'helpers/utility.php';
      require_once 'models/poll.php';
      $pollId = sanitizeInput($_GET['id']);
      $poll = getPollById($pollId);
      $pageStyle = "administrasjon";
      $title = $poll['title'];
      break;
    case "opprett-nytt-valg.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if ($_SESSION['userType'] != 3) {
        header("location: /index.php");
        exit();
      }
      require_once 'helpers/utility.php';
	    require_once 'models/poll.php';
      $pageStyle = "administrasjon";
      $title = "Opprett nytt valg";
      break;
    case "administrasjon.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if (!$_SESSION['userType'] > 1) {
        header("location: /index.php");
        exit();
      }
      $pageStyle = "administrasjon";
      $title = 'Administrasjon';
      break;
    case "kandidat.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      }
      if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $candidateId = $_GET['id'];
      } else if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $candidateId = $_POST['candidate-id'];
      }
      if (empty($candidateId)) {
          header("location: /index.php");
          exit();
      }
      require_once 'models/poll.php';
      require_once 'models/candidate.php';
      require_once 'models/user.php';
      require_once 'models/vote.php';
      require_once 'models/notification.php';
      $candidate = getCandidateById($candidateId);
      if (!$candidate) {
        header("location: /index.php");
        exit();
      }
      $user = getUserById($candidate['user_id']);
      $poll = getPollById($candidate['poll_id']);
      $title = $user['first_name']." ".$user['last_name'];
      $pageStyle = "kandidat";
      break;
    case "valgliste.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      }
      require_once 'models/poll.php';
      $pageStyle = "valgliste";
      $title = 'Valgliste';
      break;
    case "resultat.php":
      if (!$isLoggedIn) {
        header("location: /logg-inn.php");
        exit();
      } else if (!isset($_GET['id'])) {
        header("location: /index.php");
        exit();
      }
      require_once 'models/poll.php';
      require_once 'models/candidate.php';
      require_once 'models/vote.php';
      require_once 'models/user.php';
      require_once 'helpers/utility.php';
      $pollId = sanitizeInput($_GET['id']);
      $poll = getPollById($pollId);
      if ($poll['end_poll'] > date("Y-m-d H:i:s")) {
        header("location: /valg.php?id=".$poll['id']);
        exit();
      } else if (!$poll['checked']) {
        header("location: /index.php");
        exit();
      }
      $pageStyle = "valgliste";
      $title = $poll['title'];
  }

  if (isset($title) && !empty($title)) {
    $title .= " - Godt Valg";
  } else {
    $title = "Godt Valg";
  }
?>
<!DOCTYPE html>
<html lang="no">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title ?></title>
  <link rel=icon href=favicon.svg>
  <link rel="stylesheet" href="styles/main.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <?php if ($currentPage == "kandidat.php") { echo '<link rel="stylesheet" href="./styles/logg-inn.css">'; } // kandidat.php deler mye css med logg-inn.css, så vi inluderer det her ?>
  <?php if (!empty($pageStyle)) { echo '<link rel="stylesheet" href="./styles/' . $pageStyle . '.css">'; } // Setter inn stilark basert på hvilken side du er på ?>
</head>

<body>
  <nav id="navbar" class="navbar">
    <a href="/index.php"><img class="navbar_logo" style="width: 135px;" src="img/logo.svg"></a>
    <div class="navbar-right">
      <!-- Hamburger-meny-knapp -->
      <img class="navbar-button" src="img/icons/bars-solid.svg" style="width: 38px; height: 38px;">
    </div>
    <!-- Navigasjonslisten -->
    <ul class="navbar-list">
      <li class="navbar-list-item">
        <a class="navbar-link navbar-link-background <?php if($currentPage == "index.php" ) { echo 'navbar-link-background-active';} ?>" href="/index.php"><img
            class="navbar-link-icon" src="img/icons/home.svg" alt="" style="width: 20px; height: 20px;">Hjem</a>
      </li>
      <li class="navbar-list-item">
        <a class="navbar-link navbar-link-background  <?php if($currentPage == "valgliste.php" ) { echo 'navbar-link-background-active';} ?>" href="/valgliste.php"><img class="navbar-link-icon"
            src="img/icons/check-square.svg" alt="" style="width: 20px; height: 20px;">Valg</a>
      </li>
      <?php
        /* Om brukeren er admin eller kontrollør vil de få tilgang til administrasjon-siden */
        if ($isLoggedIn && $_SESSION['userType'] > 1) {
          echo '<li class="navbar-list-item"><a class="navbar-link navbar-link-background ';?><?php if($currentPage == "administrasjon.php" || $currentPage == "opprett-nytt-valg.php" || $currentPage == "administrer-valg.php" || $currentPage == "administrer-brukere.php") { echo 'navbar-link-background-active';} ?><?php echo '" href="/administrasjon.php"><img class="navbar-link-icon" src="img/icons/user-cog.svg" alt="" style="width: 20px; height: 20px;">Administrasjon</a></li>';?>
      <?php } ?>
      <div class="navbar-divider navbar-divider--vertical"></div>
      <div class='notification-container'></div>
      <?php
        if ($isLoggedIn) {
          $newNotifications = getNewNotificationsByUserId($_SESSION['userId']);
          $newNotificationCount = ($currentPage == "varsler.php") ? 0 : count($newNotifications); // Om brukeren befinner seg på varsler.php, så sett nye-varsler-antallet til 0
          /* Viser varsel-linken. På ikonet vil det dukke opp en klassisk rød sirkel med antall nye varsler, som blir borte når du har sett de. Antallet er i en data-attributt kalt count som vi manipulerer i CSS-en */
          echo "<li class='navbar-list-item'><a class='navbar-link' href='varsler.php?id=".$_SESSION['userId']."'><div class='notification-wrapper' data-count='".$newNotificationCount."' style='width: 20px; height: 20px;'><img class='notification-button' src='img/icons/bell.svg' alt='varsler' style='width: 100%; height: 100%;'></div>Varsler</a></li>";
          /* Linken til min-side.php. I utgangspunktet skulle vi ha profilbilde her fremfor brukerikonet som er der nå, men siden kun kandidater kan ha bilder så ble det ikke noe av */
          echo "<li class='navbar-list-item'><a class='navbar-link' href='min-side.php'><img class='navbar-link-icon' src='img/icons/user-circle.svg' alt='' style='width: 20px; height: 20px; border-radius: 50%;'>$firstName</a></li>";
          /* Link til logg-ut.php */
          echo "<li class='navbar-list-item'><a class='navbar-link' href='logg-ut.php'><img class='navbar-link-icon' src='img/icons/sign-out-alt-solid.svg' alt='' style='width: 20px; height: 20px;'>Logg ut</a></li>";
        }
        else {
          /* Om du ikke er innlogget vises registrer-bruker og logg-inn i steden for. */
          echo "<li class='navbar-list-item'><a class='navbar-link' href='/registrer-bruker.php'><img class='navbar-link-icon' src='img/icons/user-circle.svg' alt='' style='width: 20px; height: 20px;'>Registrer bruker</a></li>";
          echo "<li class='navbar-list-item'><a class='navbar-link' href='/logg-inn.php'><img class='navbar-link-icon' src='img/icons/sign-in-alt-solid.svg' alt='' style='width: 20px; height: 20px;'>Logg inn</a></li>";
        }
      ?>
    </ul>
  </nav>
  <!-- Det klikkbare mørke feltet som kan brukes for å lukke navigasjonsmenyen på mobil -->
  <div class="navbar-menu-background"></div>
