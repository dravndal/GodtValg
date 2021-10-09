<?php
// poll.php laget av Dennis Næss. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../helpers/utility.php';

$db = new mysqlPDO();

// Henter valg
function getPollById($id) {
    $sql = "SELECT * FROM `poll` WHERE `id` = :id";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $poll = $stmt->fetch(PDO::FETCH_ASSOC);
        return $poll;
    } else {
        return false;
    }
}

// Henter kommende valg
function fetchUpcomingPolls() {
    $sql = "SELECT * FROM `poll` WHERE `start_poll` > NOW()";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->execute();
    $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $polls;
}

// Henter pågående valg
function fetchOngoingPolls() {
    $sql = "SELECT * FROM `poll` WHERE `start_poll` < NOW() AND `end_poll` > NOW()";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->execute();
    $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $polls;

}

// Henter ferdige valg
function fetchFinishedPolls() {
    $sql = "SELECT * FROM `poll` WHERE `end_poll` < NOW()";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->execute();
    $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $polls;
}

// Lager nytt valg
function createPoll($title, $start, $end) {
    if (!isValidDate($start) || !isValidDate($end)) {
        throw new Exception('Ugyldig datoformat!');
    }
    $sql = "INSERT INTO poll(`title`, `start_poll`, `end_poll`) VALUES (:title, :start_poll, :end_poll)";
    $stmt = $GLOBALS['db']->prepare($sql);
    if (!$stmt) {
        throw new Exception('Noe gikk galt!');
    }
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':start_poll', $start);
    $stmt->bindParam(':end_poll', $end);
    $stmt->execute();
}