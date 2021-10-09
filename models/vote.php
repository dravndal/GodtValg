<?php
// vote.php laget av Simen Jensen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../helpers/utility.php';
require_once __DIR__.'/poll.php';
require_once __DIR__.'/user.php';

// Hent stemme basert på poll-id og bruker-id
function getVoteByPollIdAndUserId($pollId, $userId) {
    $sql = "SELECT * FROM vote WHERE `poll_id` = :poll_id AND `user_id` = :user_id";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':poll_id', $pollId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $vote = $stmt->fetch(PDO::FETCH_ASSOC);
        return $vote;
    } else {
        return false;
    }
}

// Hent alle stemmer basert på kandidat-id
function getVotesByCandidateId($candidateId) {
    $sql = "SELECT * FROM vote WHERE `candidate_id` = :candidate_id";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':candidate_id', $candidateId);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $votes;
    } else {
        return false;
    }
}

// Hent alle stemmer basert på valg-id
function getVotesByPollId($pollId) {
    $sql = "SELECT * FROM vote WHERE `poll_id` = :poll_id";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':poll_id', $pollId);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $votes;
    } else {
        return false;
    }
}

// Lag ny stemme
function createVote($pollId, $userId, $candidateId) {
    if (!getPollById($pollId)) {
        throw new Exception('Valget finnes ikke!');
    } else if (getVoteByPollIdAndUserId($pollId, $userId)) {
        throw new Exception('Stemme allerede registrert!');
    } else {
        $sql = "INSERT INTO vote(`poll_id`, `user_id`, `candidate_id`) VALUES (:poll_id, :user_id, :candidate_id);";
        $stmt = $GLOBALS['db']->prepare($sql);
        if (!$stmt) {
            throw new Exception('Noe gikk galt!');
        }
        $stmt->bindParam(':poll_id', $pollId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':candidate_id', $candidateId);
        $stmt->execute();
    }
}

// Fjern stemme basert på valg-id og bruker-id
function removeVote($pollId, $userId) {
    if (!getPollById($pollId)) {
        throw new Exception('Valget finnes ikke!');
    } else {
        $sql = "DELETE FROM vote WHERE `poll_id` = :poll_id AND `user_id` = :user_id;";
        $stmt = $GLOBALS['db']->prepare($sql);
        if (!$stmt) {
            throw new Exception('Noe gikk galt!');
        }
        $stmt->bindParam(':poll_id', $pollId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
}

// Forandre stemmen til en bruker
function changeVote($pollId, $userId, $candidateId) {
    if (!getPollById($pollId)) {
        throw new Exception('Valget finnes ikke!');
    } else {
        $sql = "UPDATE vote SET `candidate_id` = :candidate_id WHERE `poll_id` = :poll_id AND `user_id` = :user_id;";
        $stmt = $GLOBALS['db']->prepare($sql);
        if (!$stmt) {
            throw new Exception('Noe gikk galt!');
        }
        $stmt->bindParam(':candidate_id', $candidateId);
        $stmt->bindParam(':poll_id', $pollId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
}