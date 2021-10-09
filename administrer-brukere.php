<?php
  // administrer-brukere.php laget av Kevin André Torgrimsen Nordli. Sist endret 21.05.2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';
?>
<main>
    <a class="back-button" href="administrasjon.php"><img src="img/icons/arrow-left.svg">Gå tilbake</a>
	<header id="header">
		<h1>Administrer brukere</h1>
	</header>
	<section class="administration-section">
        <label class="searchbar">
            <input class="searchbar-input" type="text" placeholder="Søk etter e-post" onkeyup="showUsers(this.value)">
        </label>
        <div id="user-table-container">
            <?php

            ?>
        </div>
	</section>
    <!-- Link for å gå tilbake til toppen om du når bunnen -->
	<a class="back-to-top-link" href="#top">Tilbake til toppen</a>
</main>
<script>
function showUsers(str) {
    if (str.length == 0) {
        showAllUsers();
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", "inc/fetch-users.php?q=" + str, true);
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("user-table-container").innerHTML = this.responseText;
            }
        };
        xmlhttp.send();
    }
}
function showAllUsers() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "inc/fetch-all-users.php", true);
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("user-table-container").innerHTML = this.responseText;
        }
    };
    xmlhttp.send();
}
window.onload = () => {
    showAllUsers();
};
function updateUserType(value) {
    var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "inc/update-user-type.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.send(`value=${value}`);
}
function deleteUser(email) {
    if (confirm("Er du sikker på at du vil slette " + email + "?")) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "inc/delete-user-admin.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.readyState == 4 && this.status == 200) {
                let button = document.querySelector("button[value='"+email+"']");
                if (button) {
                    if (button.parentElement.parentElement.parentElement.childElementCount === 2) {
                        button.parentElement.parentElement.parentElement.parentElement.parentElement.innerHTML = "<span class='not-found'>Ingen bruker funnet.</span>";
                    } else {
                        button.parentElement.parentElement.remove();
                    }
                }
            }
            }
        };
        xmlhttp.send(`email=${email}`);
    }
}
</script>
<?php require 'inc/footer.php' ?>
