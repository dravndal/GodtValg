<?php
// notification.php laget av Leander Didriksen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../helpers/utility.php';

$db = new mysqlPDO();

// Henter alle varslene til en bruker basert på bruker-id
function getNotificationsByUserId($userId) {
    $sql = "SELECT * FROM `notification` WHERE `reciever` = :userId";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $notifications;
}

// Henter alle varslene til en bruker basert på bruker-id
function deleteCandidateNotification($userId, $pollId) {
    $sql = "DELETE FROM `notification` WHERE `reciever` = :userId AND `poll_id` = :pollId AND `notification_type` = 1";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->bindParam(':pollId', $pollId);
    $stmt->execute();
}

// Henter alle varsler som ikke har blitt sett enda basert på bruker-id
function getNewNotificationsByUserId($userId) {
    $sql = "SELECT * FROM `notification` WHERE `reciever` = :userId AND `seen` IS NULL";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $notifications;
}

// Lager ny varsel 
function createNotification($sender, $reciever, $pollId, $candidateId, $notificationType) {
    $sql = "INSERT INTO `notification`(`sender`, `reciever`, `poll_id`, `candidate_id`, `notification_type`) VALUES (:sender, :reciever, :pollId, :candidateId, :notificationType)";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':sender', $sender);
    $stmt->bindParam(':reciever', $reciever);
    $stmt->bindParam(':pollId', $pollId);
    $stmt->bindParam(':candidateId', $candidateId);
    $stmt->bindParam(':notificationType', $notificationType);
    $stmt->execute();
}

// Setter seen til datoen nå på varsel basert på varsel-id
function updateSeenNotification($notificationId) {
    $sql = "UPDATE `notification` SET `seen` = NOW() WHERE `id` = :notificationId";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':notificationId', $notificationId);
    $stmt->execute();
}