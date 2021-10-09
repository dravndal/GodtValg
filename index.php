<?php
  // index.php laget av Kevin André Torgrimsen Nordli. Sist endret 08.12.2020 av Kevin André Torgrimsen Nordli.
  require 'inc/header.php';
?>
  <main>
    <!-- Call To Action -->
    <section id="cta">
      <h1 class="cta-heading">Gi din stemme i dag!</h1>
      <p class="cta-paragraph">Godt Valg er en digital løsning for valg av rektor ved universiteter rundt om i
        Norge. Ved å registrere en bruker kan du avgi en stemme til kandidaten du mener skal bli rektor.</p>
      <?php 
        echo ($isLoggedIn) ? '<a class="cta-btn" href="/valgliste.php">Se valg</a>' : '<a class="cta-btn" href="registrer-bruker.php">Registrer deg</a>';
      ?>
    </section>
  </main>
<?php require 'inc/footer.php' ?>
