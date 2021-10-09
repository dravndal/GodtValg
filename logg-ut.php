<?php
// logg-ut.php laget av Leander Didriksen. Sist endret 20.11.2020 av Leander Didriksen.

session_start();
session_unset();
session_destroy();

header("location: /index.php");
exit();
