<?php
// create-vote.php laget av Simen Jensen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once __DIR__.'/../models/vote.php';
require_once __DIR__.'/../models/candidate.php';
require_once __DIR__.'/../helpers/utils.php';
session_start();
if (isset($_SESSION['userId']) && isset($_POST['id'])) {
    $candidateId = sanitizeInput($_POST['id']);
    $candidate = getCandidateById($candidateId);
    if ($candidate) {
        try {
            createVote($candidate['poll_id'], $_SESSION['userId'], $candidate['id']); // Avgi stemme til en kandidat basert på sesjonens bruker-id
        } catch (Exception $e) {
            return;
        }
    }
}