<?php
  // administrer-valg.php laget av Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
	require 'inc/header.php';
?>
<main>
    <a class="back-button" href="/administrasjon.php"><img src="img/icons/arrow-left.svg">Gå tilbake</a>
	<header id="header">
		<h1>Administrer valg</h1>
	</header>
    <ul class="poll-sort-by-list">
        <li id="sort-by-upcoming" class="poll-sort-by-list-item poll-sort-by-list-item-active" onclick="sortPolls('upcoming');">
            <div class="poll-sort-by-button">Kommende</div>
            <div class="border-bottom"></div>
        </li>
        <li id="sort-by-ongoing" class="poll-sort-by-list-item" onclick="sortPolls('ongoing');">
            <div class="poll-sort-by-button">Pågående</div>
            <div class="border-bottom"></div>
        </li>
        <li id="sort-by-finished" class="poll-sort-by-list-item" onclick="sortPolls('finished');">
            <div class="poll-sort-by-button">Ferdig</div>
            <div class="border-bottom"></div>
        </li>
    </ul>
	<section class="administration-section">
        <div class="table-container"></div>
	</section>
</main>
<script>
function sortPolls(value) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", "inc/fetch-polls-admin.php?sort=" + value, true);
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if (value === "upcoming") {
                let pressedButton = document.getElementById("sort-by-upcoming");
                let previousButton = document.querySelector(".poll-sort-by-list-item-active");
                previousButton.classList.remove("poll-sort-by-list-item-active");
                pressedButton.classList.add("poll-sort-by-list-item-active");
            } else if (value === "ongoing") {
                let pressedButton = document.getElementById("sort-by-ongoing");
                let previousButton = document.querySelector(".poll-sort-by-list-item-active");
                previousButton.classList.remove("poll-sort-by-list-item-active");
                pressedButton.classList.add("poll-sort-by-list-item-active");
            } else if (value === "finished") {
                let pressedButton = document.getElementById("sort-by-finished");
                let previousButton = document.querySelector(".poll-sort-by-list-item-active");
                previousButton.classList.remove("poll-sort-by-list-item-active");
                pressedButton.classList.add("poll-sort-by-list-item-active");
            }
            document.querySelector(".table-container").innerHTML = this.responseText;
        }
    };
    xmlhttp.send();
}
window.onload = () => {
    sortPolls('upcoming');
};
</script>
<?php require 'inc/footer.php' ?>
