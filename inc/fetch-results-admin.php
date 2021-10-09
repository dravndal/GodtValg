<?php
// fetch-results-admin.php laget av Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../models/poll.php';
require_once '../models/vote.php';
require_once '../models/candidate.php';
require_once '../models/user.php';
require_once __DIR__.'/../helpers/utility.php';
session_start();
if (isset($_SESSION['userId']) && isset($_GET["sort"]) && isset($_GET["id"])) {
    if ($_SESSION['userType'] > 1) {
        $sort = $_GET["sort"];
        $pollId = sanitizeInput($_GET["id"]);
        if ($sort == "candidates") {
            $candidates = getCandidatesByPollId($pollId);
            if ($candidates) {
                $candidatesTable = "<table>";
                $candidatesTable .= "<tr><th>Kandidat</th><th>Stemmer</th><th>Handling</th></tr>";
                foreach ($candidates as $candidate) {
                    $votes = getVotesByCandidateId($candidate['id']);
                    $numberOfVotes = ($votes !== false) ? count($votes) : 0;
                    $user = getUserById($candidate['user_id']);
                    $candidatesTable .= "<tr><td>".$user['email']."</td><td>".$numberOfVotes."</td><td class='delete-button-cell'><button class='delete-button' onclick='deleteCandidate(this.value)' value='".$candidate['id']."'>Slett</button></td></tr>";
                }
                $candidatesTable .= "</table>";
                echo $candidatesTable;
            } else {
                echo "<span class='not-found'>Ingen kandidater funnet.</span>";
            }
        } else if ($sort == "votes") {
            $votes = getVotesByPollId($pollId);
            if ($votes) {
                $votesTable = "<table>";
                $votesTable .= "<tr><th>Kandidat</th><th>Velger</th><th>Handling</th></tr>";
                foreach ($votes as $vote) {
                    $user = getUserById($vote['user_id']);
                    $candidate = getCandidateById($vote['candidate_id']);
                    $userCandidate = getUserById($candidate['user_id']);
                    $votesTable .= "<tr><td>".$userCandidate['email']."</td><td>".$user['email']."</td><td class='delete-button-cell'><button class='delete-button' onclick='deleteVote(this.value)' value='".$vote['id']."'>Slett</button></td></tr>";
                }
                $votesTable .= "</table>";
                echo $votesTable;
            } else {
                echo "<span class='not-found'>Ingen stemmer funnet.</span>";
            }
        } else if ($sort == "choices") {
            $poll = getPollById($pollId);
            if ($poll['end_poll'] > date("Y-m-d H:i:s")) { // Om valget ikke har sluttet enda
                $approveButton = "<p class='choice-button-disabled-text'>Valget kan godkjennes etter ".$poll['end_poll']."</p><button class='choice-button choice-button-disabled'>Godkjenn valget</button>";
            } else if ($poll['checked'] == NULL) { // Om valget ikke har blitt sjekket
                $approveButton = "<button class='choice-button' onclick='approvePoll(".$pollId.")'>Godkjenn valget</button>";
            } else { // Om valget har blitt sjekket
                $approveButton = "<button class='choice-button choice-button-checked'>Godkjent</button>";
            }
            echo "<div class='choice-button-container'>".$approveButton."<button class='choice-button choice-button-red' onclick='deletePoll(".$pollId.")'>Slett valget</button></div>";
        } else {
            return;
        }  
    }
}