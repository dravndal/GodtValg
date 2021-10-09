<?php
// delete-user.php laget av Dennis Næss. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../config/db.php';
session_start();
if (isset($_SESSION['userId'])) {
    $db = new mysqlPDO();
    try {
        $sql = 'DELETE FROM `user` WHERE `id` = :id';
        $stmt = $GLOBALS['db']->prepare($sql);
        $stmt->bindParam(':id', $_SESSION['userId']);
        $stmt->execute();
        /* Logg bruker ut etter at brukeren er slettet fra databasen */
        session_unset();
        session_destroy();
        exit();
    } catch (Exception $e) {
        echo $e;
    }
}