<?php
    // administrasjon.php laget av Kevin André Torgrimsen Nordli. Sist endret 21.05.2021 av Kevin André Torgrimsen Nordli.
    require 'inc/header.php';
?>
<main>
    <header id="header">
		<h1>Administrasjon</h1>
	</header>
    <section id="administration-navigation">
        <?php 
            if ($isLoggedIn) {
                if ($_SESSION['userType'] > 1) {
                    if ($_SESSION['userType'] == 3) {
                        echo '<ul class="administration-list">
                        <li class="administration-list-item">
                            <a class="administration-list-link" href="/opprett-nytt-valg.php">
                                <img class="administration-list-icon" src="img/icons/plus-square.svg" alt="">
                                <span class="administration-list-text">Opprett nytt valg</span>
                            </a>
                        </li>';
                    }
                    echo '<li class="administration-list-item">
                        <a class="administration-list-link" href="/administrer-valg.php">
                            <img class="administration-list-icon" src="img/icons/poll.svg" alt="">
                            <span class="administration-list-text">Administrer valg</span>
                        </a>
                    </li>';
                    if ($_SESSION['userType'] == 3) {
                        echo '<li class="administration-list-item">
                                <a class="administration-list-link" href="/administrer-brukere.php">
                                    <img class="administration-list-icon" src="img/icons/user-cog.svg" alt="">
                                    <span class="administration-list-text">Administrer brukere</span>
                                </a>
                            </li>
                        </ul>';
                    }
                }
            }
        ?>
    </section>
</main>
<?php require 'inc/footer.php' ?>