<?php
// update-user-type.php laget av Dennis Næss. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../config/db.php';
require_once __DIR__.'/../helpers/utility.php';
session_start();
if (isset($_SESSION['userId']) && isset($_POST['value'])) {
    if ($_SESSION['userType'] == 3) {
        $value = sanitizeInput($_POST['value']);
        $db = new mysqlPDO();
        $values = explode(',', $value); // Del opp verdiene hentet fra value
        try {
            $sql = 'UPDATE `user` SET `user_type` = :user_type WHERE `email` = :email';
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':user_type', $values[0]);
            $stmt->bindParam(':email', $values[1]);
            $stmt->execute();
        } catch (Exception $e) {
            echo $e;
        }
    }
}
