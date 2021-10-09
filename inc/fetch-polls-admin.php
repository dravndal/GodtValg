<?php
// fetch-polls-admin.php laget av Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
/* Henter alle valg for visning i administrer-valg. Forskjellen mellom fetch-polls og fetch-polls-admin er at i denne så går linkene til administrer-resultat i steden for resultat */
require_once '../models/poll.php';
session_start();
if (isset($_SESSION['userId']) && isset($_GET['sort'])) {
    setlocale(LC_TIME, "nb_NO.utf8");
    if ($_SESSION['userType'] > 1) {
        $sort = $_GET['sort'];
        if ($sort == "upcoming") {
            $upcomingPolls = fetchUpcomingPolls();
            if (count($upcomingPolls) > 0) {
                $upcomingPollsTable = "<table>";
                $upcomingPollsTable .= "<tr><th>Tittel</th><th>Start</th><th>Slutt</th></tr>";
                foreach ($upcomingPolls as $poll) {
                    $upcomingPollsTable .= "<tr class='table-row-clickable' onclick='window.location=\"/administrer-resultat.php?id=".$poll['id']."\"'><td>".$poll['title']."</td><td>".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['start_poll']))."</td><td>".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['end_poll']))."</td></tr>";
                }
                $upcomingPollsTable .= "</table>";
                echo $upcomingPollsTable;
            } else {
                echo "<span class='not-found'>Ingen kommende valg.</span>";
            }
        } else if ($sort == "ongoing") {
            $ongoingPolls = fetchOngoingPolls();
            if (count($ongoingPolls) > 0) {
                $ongoingPollsTable = "<table>";
                $ongoingPollsTable .= "<tr><th>Tittel</th><th>Start</th><th>Slutt</th></tr>";
                foreach ($ongoingPolls as $poll) {
                    $ongoingPollsTable .= "<tr class='table-row-clickable' onclick='window.location=\"/administrer-resultat.php?id=".$poll['id']."\"'><td>".$poll['title']."</td><td>".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['start_poll']))."</td><td>".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['end_poll']))."</td></tr>";
                }
                $ongoingPollsTable .= "</table>";
                echo $ongoingPollsTable;
            } else {
                echo "<span class='not-found'>Ingen pågående valg.</span>";
            }
        } else if ($sort == "finished") {
            $finishedPolls = fetchFinishedPolls();
            if (count($finishedPolls) > 0) {
                $finishedPollsTable = "<table>";
                $finishedPollsTable .= "<tr><th>Tittel</th><th>Start</th><th>Slutt</th><th>Kontrollert</th></tr>";
                foreach ($finishedPolls as $poll) {
                    $checked = ($poll['checked']) ? $poll['checked'] : 'Nei'; // Om valget har blitt sjekket, sett $checked til resultatet fra checked i databasen, ellers sett "Nei"
                    $finishedPollsTable .= "<tr class='table-row-clickable' onclick='window.location=\"/administrer-resultat.php?id=".$poll['id']."\"'><td>".$poll['title']."</td><td>".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['start_poll']))."</td><td>".strftime("%e. %B %Y kl. %H:%M", strtotime($poll['end_poll']))."</td><td>".$checked."</td></tr>";
                }
                $finishedPollsTable .= "</table>";
                echo $finishedPollsTable;
            } else {
                echo "<span class='not-found'>Ingen ferdige valg.</span>";
            }
        } else {
            return;
        }
    }
}
