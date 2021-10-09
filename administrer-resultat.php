<?php
  // administrer-resultat.php laget av Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
    require 'inc/header.php';
?>
<main>
    <a class="back-button" href="/administrer-valg.php"><img src="img/icons/arrow-left.svg">Gå tilbake</a>
	<header id="header">
		<h1>Administrer resultat</h1>
        <span class="description"><?php echo $poll['title'];?></span>
	</header>
    <ul class="poll-sort-by-list">
        <li id="sort-by-candidates" class="poll-sort-by-list-item poll-sort-by-list-item-active" onclick="sortResults('candidates');">
            <div class="poll-sort-by-button">Kandidater</div>
            <div class="border-bottom"></div>
        </li>
        <li id="sort-by-votes" class="poll-sort-by-list-item" onclick="sortResults('votes');">
            <div class="poll-sort-by-button">Stemmer</div>
            <div class="border-bottom"></div>
        </li>
        <li id="sort-by-choices" class="poll-sort-by-list-item" onclick="sortResults('choices');">
            <div class="poll-sort-by-button">Handlinger</div>
            <div class="border-bottom"></div>
        </li>
    </ul>
	<section class="administration-section">
        <div class="form-error-message form-error-message-hidden"><img src='img/icons/exclamation-triangle.svg' alt='' style = 'width: 20px; height: 20px;' ><p id="error-message" style='color:#FF0000;'></p></div>
        <div class="table-container"></div>
	</section>
</main>

<script>
    const urlParameters = new URLSearchParams(window.location.search); // Henter query strings
    const pollId = urlParameters.get('id');
    function sortResults(value) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("GET", "inc/fetch-results-admin.php?sort=" + value + "&id=" + pollId, true);
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (value === "candidates") {
                    let pressedButton = document.getElementById("sort-by-candidates");
                    let previousButton = document.querySelector(".poll-sort-by-list-item-active");
                    previousButton.classList.remove("poll-sort-by-list-item-active");
                    pressedButton.classList.add("poll-sort-by-list-item-active");
                } else if (value === "votes") {
                    let pressedButton = document.getElementById("sort-by-votes");
                    let previousButton = document.querySelector(".poll-sort-by-list-item-active");
                    previousButton.classList.remove("poll-sort-by-list-item-active");
                    pressedButton.classList.add("poll-sort-by-list-item-active");
                } else if (value === "choices") {
                    let pressedButton = document.getElementById("sort-by-choices");
                    let previousButton = document.querySelector(".poll-sort-by-list-item-active");
                    previousButton.classList.remove("poll-sort-by-list-item-active");
                    pressedButton.classList.add("poll-sort-by-list-item-active");
                }
                document.querySelector(".table-container").innerHTML = this.responseText;
            }
        };
        xmlhttp.send();
    }
    window.onload = () => { // Når siden lastes inn, sorter etter kandidater
        sortResults('candidates', pollId);
    };
    function deleteCandidate(id) {
        if (confirm("Er du sikker på at du vil slette denne kandidaten?")) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "inc/delete-candidate.php", true);
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.readyState == 4 && this.status == 200) {
                        let button = document.querySelector("button[value='"+id+"']");
                        if (button) {
                            /* Ekstreme mengder node-traversal for å komme til elementet, men jeg tror dette er måten man er ment å bruke */
                            if (button.parentElement.parentElement.parentElement.childElementCount === 2) {
                                button.parentElement.parentElement.parentElement.parentElement.parentElement.innerHTML = "<span class='not-found'>Ingen kandidat funnet.</span>";
                            } else {
                                button.parentElement.parentElement.remove();
                            }
                        }
                    }
                }
            };
            xmlhttp.send(`candidateId=${id}`);
        }
    }
    function deleteVote(id) {
        if (confirm("Er du sikker på at du vil slette denne stemmen?")) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "inc/delete-vote.php", true);
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                        if (this.readyState == 4 && this.status == 200) {
                        let button = document.querySelector("button[value='"+id+"']");
                        if (button) {
                            if (button.parentElement.parentElement.parentElement.childElementCount === 2) {
                                button.parentElement.parentElement.parentElement.parentElement.parentElement.innerHTML = "<span class='not-found'>Ingen stemme funnet.</span>";
                            } else {
                                button.parentElement.parentElement.remove();
                            }
                        }
                    }
                }
            };
            xmlhttp.send(`id=${id}`);
        }
    }
    function deletePoll(id) {
        if (confirm("Er du sikker på at du vil slette dette valget?")) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("POST", "inc/delete-poll.php", true);
            xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    window.location.replace("https://godtvalg.kevinnordli.com/administrer-valg.php");
                }
            };
            xmlhttp.send(`id=${id}`);
        }
    }
    function approvePoll(id) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open("POST", "inc/approve-poll.php", true);
        xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                let button = document.querySelector(".choice-button");
                let errorContainer = document.querySelector(".form-error-message");
                let errorMessage = document.querySelector("#error-message");
                errorMessage.innerHTML = this.responseText;
                if (errorMessage.innerHTML !== "") {
                    errorContainer.classList.remove("form-error-message-hidden");
                    errorContainer.style.marginBottom = "32px";
                } else {
                    button.classList.add('choice-button-checked');
                    button.innerHTML = "Godkjent";
                }
            }
        };
        xmlhttp.send(`id=${id}`);
    }
</script>
<?php require 'inc/footer.php' ?>