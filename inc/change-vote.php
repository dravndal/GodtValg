<?php
// change-vote.php laget av Simen Jensen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
/* __DIR__ er nødvendig i noen tilfeller for å sørge for at den starter fra mappen som filen ligger i, og så går bakover til der vi ønsker å importere kode fra */
require_once __DIR__.'/../models/vote.php';
require_once __DIR__.'/../models/candidate.php';
require_once __DIR__.'/../models/poll.php';
require_once __DIR__.'../helpers/utils.php';
session_start();
if (isset($_SESSION['userId']) && isset($_POST['id'])) {
    $candidateId = sanitizeInput($_POST['id']);
    $candidate = getCandidateById($candidateId);
    if ($candidateId && $candidate) {
        try {
            changeVote($candidate['poll_id'], $_SESSION['userId'], $candidate['id']); // Forandre stemmen til ny kandidat basert på sesjonens bruker-id
        } catch (Exception $e) {
            return;
        }
    }
}