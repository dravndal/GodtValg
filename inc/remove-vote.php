<?php
// remove-vote.php laget av Simen Jensen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once __DIR__.'/../models/vote.php';
require_once __DIR__.'/../models/candidate.php';
require_once __DIR__.'/../helpers/utility.php';
session_start();
if (isset($_SESSION['userId']) && isset($_POST['id'])) {
    $candidateId = sanitizeInput($_POST['id']);
    $candidate = getCandidateById($candidateId);
    if ($candidate) {
        try {
            removeVote($candidate['poll_id'], $_SESSION['userId']);
        } catch (Exception $e) {
            return;
        }
    }
}