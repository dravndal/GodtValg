<?php
// delete-candidate.php laget av Leander Didriksen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../models/candidate.php';
require_once __DIR__.'/../helpers/utility.php';
session_start();
if (isset($_SESSION['userId']) && isset($_POST['candidateId'])) {
    $candidateId = sanitizeInput($_POST['candidateId']);
    $candidate = getCandidateById($candidateId);
    if ($_SESSION['userType'] == 3 || $_SESSION['userId'] == $candidate['user_id']) { // Om brukertype er admin eller bruker id-en i sesjonen er den samme som kandidatens bruker id.
        try {
            deleteCandidate($candidateId);
        } catch (Exception $e) {
            echo $e;
        }
    }
}