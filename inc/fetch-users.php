<?php
// fetch-users.php laget av Dennis Næss. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../config/db.php';
require_once __DIR__.'/../helpers/utility.php';
session_start();
if (isset($_SESSION['userId']) && isset($_GET['q'])) {
    // Hent query string-en og sett det inn i variabelet queryString
    $queryString = sanitizeInput($_GET['q']);
    $db = new mysqlPDO();
    if ($queryString !== "") { // Om query string-en ikke er tom
        try {
            $queryString = strtolower($queryString);
            $sql = "SELECT * FROM user WHERE `email` LIKE \"%\":email\"%\"";
            $stmt = $GLOBALS['db']->prepare($sql);
            $stmt->bindParam(':email', $queryString);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) > 0) {
                $userTable = "<table id='user-table'>";
                $userTable .= "<tr><th>E-post</th><th>Fornavn</th><th>Etternavn</th><th>Brukertype</th><th>Slett</th></tr>";
                $userTypes = array(1 => 'Bruker', 2 => 'Kontrollør', 3 => 'Administrator');
                foreach ($rows as $user) {
                    $userTable .= "<tr><td>".$user['email']."</td><td>".$user['first_name']."</td><td>".$user['last_name']."</td><td><select onchange='updateUserType(this.value)'>";?><?php foreach($userTypes as $key => $value) {$userTable .= "<option value='".$key.",".$user['email']."'";?><?php if ($user['user_type'] == $key) {$userTable .= "selected";}?><?php $userTable .= ">".$value."</option>";} ?><?php $userTable .= "</select></td><td><button class='delete-button' onclick='deleteUser(this.value)' value='".$user['email']."'>Slett</button></td></tr>";
                }
                $userTable .= "</table>";
                echo $userTable;
            } else {
                echo '<span class="not-found">Ingen bruker funnet.</span>';
            }
        } catch (Exception $e) {
            throw $e;
        }
        
    } else {
        return;
    }
}
