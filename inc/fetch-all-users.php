<?php
// fetch-all-users.php laget av Dennis Næss. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
require_once '../models/user.php';
session_start();
if (isset($_SESSION['userId'])) {
    $users = getUsers();
    if (count($users) > 0) { // Hvis det er minst 1 verdi i arrayet
        $userTable = "<table id='user-table'>"; // Opprett først en tabell med id "uesr-table"
        $userTable .= "<tr><th>E-post</th><th>Fornavn</th><th>Etternavn</th><th>Brukertype</th><th>Slett</th></tr>"; // Append table headers
        $userTypes = array(1 => 'Bruker', 2 => 'Kontrollør', 3 => 'Administrator'); // Associative array for brukertypene
        /* For hver bruker, lag en table row med data om brukeren */
        foreach ($users as $user) {
            $userTable .= "<tr><td>".$user['email']."</td><td>".$user['first_name']."</td><td>".$user['last_name']."</td><td><select onchange='updateUserType(this.value)'>";?><?php foreach($userTypes as $key => $value) {$userTable .= "<option value='".$key.",".$user['email']."'";?><?php if ($user['user_type'] == $key) {$userTable .= "selected";}?><?php $userTable .= ">".$value."</option>";} ?><?php $userTable .= "</select></td><td><button class='delete-button' onclick='deleteUser(this.value)' value='".$user['email']."'>Slett</button></td></tr>";
        }
        $userTable .= "</table>"; // Avslutt tabellen
        echo $userTable; // Vis tabellen
    } else {
        echo '<span class="not-found">Ingen brukere funnet.</span>';

    }
}