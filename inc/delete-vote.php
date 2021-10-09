<?php
// delete-vote.php laget av Simen Jensen. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../config/db.php';
require_once __DIR__.'/../helpers/utility.php';
session_start();
if (isset($_SESSION['userId']) && isset($_POST['id'])) {
    if ($_SESSION['userType'] > 1) {
        $db = new mysqlPDO();
        $id = sanitizeInput($_POST['id']);
        try {
            $sql = 'DELETE FROM `vote` WHERE `id` = :id';
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (Exception $e) {
            echo $e;
        }
    }
}
