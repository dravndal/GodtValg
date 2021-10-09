<?php
// utility.php laget av Daniel Ravndal og Kevin André Torgrimsen Nordli. Sist endret 08/05/2021 av Kevin André Torgrimsen Nordli

// Sjekker om lovlig fornavn og etternavn
function isValidName($name) {
	$result = preg_match("/^[\p{Latin}\-\s]+$/u", $name); // Regular expression for å sørge for at $name er kun bokstaver (og bindestrek og space) og ikke tall
	return $result;
}

// Sjekker om datoen er en gyldig dato
function isValidDate($date) {
	$result = preg_match("/^\d{4}-\d{2}-\d{2}/", $date);
	return $result;
}

// Sjekker om lovlig passord
function isValidPassword($password) {
	$result = preg_match("/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])[a-zA-Z0-9]{8,64}$/", $password); // Passordet må inneholde minst ett tall, en liten og en stor bokstav, og må være minst 8 til 64 tegn.
	return $result;
}

// "Desinfiserer" input fra brukeren så de ikke kan sende inn skadelig kode. 
function sanitizeInput($input) {
	$input = trim($input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);
	return $input;
}

// Sjekker om filen har en lovlig filtype. I vårt tilfelle tillater vi bare bilder i form av jpg, jpeg og png-er.
function isValidImageType($fileType) {
	$result = $fileType == "jpg" || $fileType == "jpeg" || $fileType == "png";
	return $result;
}

// Sjekker om filen har tilatt filstørrelse. Denne har vært et samtaletema flere ganger, men vi endte opp med 2MB som maks størrelse.
function isValidImageSize($fileSize) {
	$result = $fileSize < 2000000;
	return $result;
}
