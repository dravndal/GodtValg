<?php
require_once '../models/bruker.php';
if (isset($_SESSION['userId']) && isset($_POST['kode'])) {
      deleteMontor($_POST['kode']);
    }
} else{
  echo "noe gikk galt!";
}
