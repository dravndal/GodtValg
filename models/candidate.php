<?php
// candidate.php laget av Daniel Ravndal. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/poll.php';
require_once __DIR__.'/user.php';
require_once __DIR__.'/../helpers/utility.php';

// Henter kandidat basert på e-post og poll-id
function getCandidateByEmail($email, $pollId) {
    $user = getUserByEmail($email);
    if ($user) {
        $sql = "SELECT * FROM candidate WHERE `user_id` = :userId AND `poll_id` = :pollId";
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':userId', $user['id']);
        $stmt->bindParam(':pollId', $pollId);
        $stmt->execute();
        if ($stmt->rowCount()) {
            $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
            return $candidate;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Henter kandidat basert på kandidat-id
function getCandidateById($candidateId) {
    $sql = "SELECT * FROM candidate WHERE id = :id";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':id', $candidateId);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        return $candidate;
    } else {
        return false;
    }
}

// Henter alle kandidater som er assosiert med gitt poll-id
function getCandidatesByPollId($pollId) {
    $sql = "SELECT * FROM candidate WHERE `poll_id` = :poll_id";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':poll_id', $pollId);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $candidates;
    } else {
        return false;
    }
}

// Lager ny kandidat
function createCandidate($email, $pollId, $faculty, $institute, $information, $imageName) {
    $poll = getPollById($pollId);
    if (!$poll) {
        throw new Exception('Valget finnes ikke!');
    } else if (!getUserByEmail($email)) {
        throw new Exception('Bruker finnes ikke!');
    } else if (getCandidateByEmail($email, $pollId)) {
        throw new Exception('Kandidat allerede registrert!');
    } else if ($poll['end_poll'] <= date("Y-m-d H:i:s")) {
        throw new Exception('Valget har startet!');
    } else {
        $sql = "INSERT INTO candidate(`user_id`, `poll_id`, `faculty`, `institute`, `information`, `candidate_image`) VALUES (:userid, :poll_id, :faculty, :institute, :information, :image_name);";
        $stmt = $GLOBALS['db']->prepare($sql);
        if (!$stmt) {
            throw new Exception('Noe gikk galt!');
        }
        $user = getUserByEmail($email);
        $stmt->bindParam(':userid', $user['id']);
        $stmt->bindParam(':poll_id', $pollId);
        $stmt->bindParam(':faculty', $faculty);
        $stmt->bindParam(':institute', $institute);
        $stmt->bindParam(':information', $information);
        $stmt->bindParam(':image_name', $imageName);
        $stmt->execute();
    }
}

// Sletter kandidat basert på kandidat-id
function deleteCandidate($candidateId) {
    $candidate = getCandidateById($candidateId);
    if ($candidate) {
        try {
            unlink(__DIR__."/../img/profiles/".$candidate['candidate_image']);
            $sql = 'DELETE FROM `candidate` WHERE `id` = :candidate_id';
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':candidate_id', $candidateId);
            $stmt->execute();
        } catch (Exception $e) {
            echo $e;
        }
    }
}

// Oppdaterer kandidat med gitte argumenter
function updateCandidate($candidateId, $faculty, $institute, $information, $imageName) {
    $candidate = getCandidateById($candidateId);
    if ($candidate) {
        $poll = getPollById($candidate['poll_id']);
    } else {
        throw new Exception('Kandidaten finnes ikke!');
    }
    if ($poll['end_poll'] <= date("Y-m-d H:i:s")) {
        throw new Exception('Valget har startet!');
    } else {
        if ($imageName !== $candidate['candidate_image']) { // Om imageName er forskjellig fra det som ligger i databasen, så fjerner vi bildet fra serveren
            unlink(__DIR__."/../img/profiles/".$candidate['candidate_image']);
        }
        $sql = "UPDATE candidate SET `faculty` = :faculty, `institute` = :institute, `information` = :information, `candidate_image` = :image_name WHERE `id` = :id";
        $stmt = $GLOBALS['db']->prepare($sql);
        if (!$stmt) {
            throw new Exception('Noe gikk galt!');
        }
        $stmt->bindParam(':faculty', $faculty);
        $stmt->bindParam(':institute', $institute);
        $stmt->bindParam(':information', $information);
        $stmt->bindParam(':image_name', $imageName);
        $stmt->bindParam(':id', $candidate['id']);
        $stmt->execute();
    }
}

// Funksjon for å godkjenne en kandidat
function approveCandidate($candidateId) {
    $sql = "UPDATE `candidate` SET `approved` = NOW() WHERE `id` = :candidateId";
    $stmt = $GLOBALS['db']->prepare($sql);
    if (!$stmt) {
        throw new Exception('Noe gikk galt!');
    }
    $stmt->bindParam(':candidateId', $candidateId);
    $stmt->execute();
}