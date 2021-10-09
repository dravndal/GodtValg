<?php
// approve-poll.php laget av Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../config/db.php';
require_once '../models/candidate.php';
require_once '../models/poll.php';
require_once '../models/vote.php';
require_once '../models/notification.php';
require_once '../helpers/utils.php';

session_start();
if (isset($_SESSION['userId']) && isset($_POST['id'])) { // Sørg for at kun innloggede brukere kan logge inn, og at variabelet id har en verdi
    if ($_SESSION['userType'] > 1) { // Sørg for at kun kontrollører og administratorer kan utføre resten av koden
        $pollId = sanitizeInput($_POST['id']);
        $db = new mysqlPDO(); // Lag en ny instans av databaseklassen
        try {
            /* Hent det høyeste antallet stemmer og kandidat id-en til den med høyest antall stemmer fra tabellen vote hvor poll id-en er poll id-en hentet fra POST variabelet. */
            $sql = 'SELECT MAX(votes) AS max_votes,candidate_id FROM (SELECT candidate_id, COUNT(candidate_id) AS votes FROM vote WHERE poll_id = :poll_id GROUP BY candidate_id) AS T';
            $stmt = $GLOBALS['db']->prepare($sql);
            /* Prepared statements: Bind variabelet $pollId til :poll_id. Dette bruker vi for å beskytte mot SQL injection hvor en bruker kan "sprøyte" inn SQL-kode for å hente ut sensitiv data. */
            $stmt->bindParam(':poll_id', $pollId);
            $stmt->execute();
            if ($stmt->rowCount()) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC); // Hent raden(e) i form av et associative array
                $isValidResult = true;
                $candidates = getCandidatesByPollId($pollId);
                if ($candidates) {
                    /* En foreach-loop for å sjekke om det er flere kandidater som har fått likt antall stemmer */
                    foreach ($candidates as $candidate) {
                        if ($candidate['id'] !== $result['candidate_id']) { // Ikke sjekk vinnerkandidaten
                            $votes = getVotesByCandidateId($candidate['id']);
                            $numberOfVotes = ($votes !== false) ? count($votes) : 0; // Ternary-operator som setter $numberOfVotes til antall stemmer eller 0 om det ikke er noen stemmer
                            if ($numberOfVotes == $result['max_votes']) { // Om antall stemmer er likt som vinnerkandidaten så kan ikke valget godkjennes.
                                $isValidResult = false;
                            }
                        }
                    }
                    /* Om det kun er én kandidat som har maks stemmer, så sett checked til datoen nå og selected til id-en til kandidaten som har vunnet */
                    if ($isValidResult) {
                        $sql = 'UPDATE `poll` SET `checked` = NOW(), `selected` = :candidate_id WHERE `id` = :poll_id';
                        $stmt = $GLOBALS['db']->prepare($sql);
                        $stmt->bindParam(':candidate_id', $result['candidate_id']);
                        $stmt->bindParam(':poll_id', $pollId);
                        $stmt->execute();
                        $receiver = getCandidateById($result['candidate_id']); // Receiver blir satt til kandidaten som vant valget
                        createNotification($_SESSION['userId'], $receiver['user_id'], $pollId, $result['candidate_id'], 2); // Send varsel om vunnet valg til kandidaten som vant
                    } else {
                        echo "Samme antall stemmer på vinnere! Omvalg kreves."; // Si ifra om at omvalg kreves dersom likt antall stemmer
                    }
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }
}