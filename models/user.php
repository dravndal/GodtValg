<?php
// user.php laget av Kevin André Torgrimsen Nordli. Sist endret 01.06.2021 av Kevin André Torgrimsen Nordli.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__.'/../libs/PHPMailer/src/Exception.php';
require __DIR__.'/../libs/PHPMailer/src/PHPMailer.php';
require __DIR__.'/../libs/PHPMailer/src/SMTP.php';

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../helpers/utility.php';

$db = new mysqlPDO();

// Henter alle brukere i databasen
function getUsers() {
    $sql = "SELECT * FROM user";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    } else {
        return false;
    }
}

// Henter bruker fra databasen basert på e-post
function getUserByEmail($email) {
    $sql = "SELECT * FROM user WHERE email = :email";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    } else {
        return false;
    }
}

// Henter bruker basert på id
function getUserById($id) {
    $sql = "SELECT * FROM user WHERE id = :id";
    $stmt = $GLOBALS['db']->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    } else {
        return false;
    }
}
  
// Legger til ny bruker i databasen
function createUser($email, $password, $lastName, $firstName, $date) {
    if (!isValidName($firstName) || !isValidName($lastName)) {
        throw new Exception('Ugyldig tegn i navn! Kun bokstaver tillatt.');
    } else if (strlen($firstName) > 45 || strlen($lastName) > 45) {
        throw new Exception('Ugyldig navn! Maks 45 tegn.');
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 45) { // PHP-filter for å sjekke om e-posten er gyldig
        throw new Exception('Ugyldig e-post!');
    } else if (!isValidDate($date)) {
        throw new Exception('Ugyldig datoformat!');
    } else if (!isValidPassword($password)) {
        throw new Exception('Passordet må inneholde minst 8 tegn, minst ett tall, én stor og én liten bokstav!');
    } else if (getUserByEmail($email)) {
        throw new Exception('E-post allerede registrert!');
    } else {
        $sql = "INSERT INTO user(`email`, `password`, `last_name`, `first_name`, `user_type`, `dob`) VALUES (:email, :hashed_password, :last_name, :first_name, 1, :dob)";
        $stmt = $GLOBALS['db']->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash passordet
        if (!$stmt || !$hashedPassword) {
            throw new Exception('Noe gikk galt!');
        }
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hashed_password', $hashedPassword); // Legg det hashede passordet i databasen
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':dob', $date);
        $stmt->execute();
    }
}

// Sletter password reset token i databasen basert på e-post
function deletePasswordResetToken($email) {
    $sql = 'DELETE FROM `password_reset` WHERE `email` = :email';
    $stmt = $GLOBALS['db']->prepare($sql);
    if (!$stmt) {
        throw new Exception('Noe gikk galt!');
    }
    $stmt->bindParam(':email', $email);
    $stmt->execute();
}

// Send password reset token til en e-post
function sendPasswordResetToken($email) {
    $user = getUserByEmail($email);
    if (!$user) {
        /* Om brukeren ikke finnes vil vi ikke gi en feilmelding, men heller si at vi har sendt en e-post dersom brukeren er registrert hos oss. Er dette security through obscurity? Kanskje, men jeg synes det er viktig å mitigere antall forsøk på brute-force uansett */
        return;
    } else {
        // Først slett tokens fra brukeren om det er der fra før av
        deletePasswordResetToken($email);
        // Opprett en token i databasen
        $sql = 'INSERT INTO password_reset(`email`, `selector`, `token`, `expires`) VALUES (:email, :selector, :token, DATE_ADD(NOW(), INTERVAL 30 MINUTE))';
        $stmt = $GLOBALS['db']->prepare($sql);
        if (!$stmt) {
            throw new Exception('Noe gikk galt!');
        }
        $selector = bin2hex(random_bytes(8)); // Gjør om tilfeldige bytes til hexadecimal
        $token = random_bytes(32); // Tilfeldige bytes for bruk til token
        $hashedToken = password_hash($token, PASSWORD_DEFAULT); // Hashing av token slik at en inntrenger ikke kan stjele dem og bytte passord
        $url = "https://godtvalg.kevinnordli.com/lag-nytt-passord.php?selector=".$selector."&validator=".bin2hex($token); // URL-en som blir sendt til e-posten slik at de kan enkelt trykke seg tilbake til lag-nytt-passord.php med selector og validator
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':selector', $selector);
        $stmt->bindParam(':token', $hashedToken);
        $stmt->execute();

        $mail = new PHPMailer(true); // Opprett ny instans av PHPMailer

        try {
            $config = require 'config/config.php'; // Hent konfigurasjonsvariablene
            $mail->isSMTP(); // Sett PHPMailer til å bruke SMTP
            /* Oppsett av PHPMailer, fra host, som i vårt tilfelle er Outlooks SMTP tjener, til brukernavn og passord, og port. */
            $mail->Host = $config['mail_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['mail_username'];
            $mail->Password = $config['mail_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['mail_port'];

            $mail->setFrom($config['mail_username'], $config['mail_fullName']); // Fra e-post
            $mail->addAddress($email, $user['first_name']); // Til e-post
            $mail->addReplyTo($config['mail_username'], $config['mail_fullName']); // Hvis brukeren sender e-post tilbake, sendes den til samme e-post som sendte

            $mail->isHTML(true); // Formater teksten i e-posten som HTML
            /* Sett encoding og charset til base64 og UTF-8 så vi kan vise norske bokstaver i e-posten */
            $mail->Encoding = 'base64';
            $mail->CharSet = 'UTF-8';
            /* Tittel */
            $mail->Subject = 'Tilbakestilling av passord hos Godtvalg.';
            /* Hovedinnholdet i e-posten */
            $mail->Body = '<p>Hei! Trykk på linken under for å tilbakestille ditt passord på Godtvalg.</p>';
            $mail->Body .= '<a href="'.$url.'">'.$url.'</a></br>';
            $mail->Body .= '<p>Hvis du ikke sendte denne forespørselen kan du se bort fra denne e-posten.</p>';
            /* Om e-post-klienten ikke støtter HTML formatering, så vil denne vises i steden for */
            $mail->AltBody = 'Hei! Du kan kopiere og gå til denne linken for å tilbakestille passordet ditt på Godvalg: '.$url;

            $mail->send(); // Send e-posten
        } catch (Exception $e) {
            throw new Exception('Noe gikk galt! '.$mail->ErrorInfo);
        }
    }
}

// Hent password reset token fre databasen
function getPasswordResetToken($selector) {
    $sql = 'SELECT * FROM `password_reset` WHERE `selector` = :selector AND `expires` >= NOW()';
    $stmt = $GLOBALS['db']->prepare($sql);
    if (!$stmt) {
        throw new Exception('Noe gikk galt!');
    }
    $stmt->bindParam(':selector', $selector);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $token = $stmt->fetch(PDO::FETCH_ASSOC);
        return $token;
    } else {
        throw new Exception('Forespørsel har utløpt eller ikke blitt sendt. Send ny forespørsel.');
    }
}

// Valider password reset token
function checkPasswordResetToken($validator, $token) {
    $validatorBinary = hex2bin($validator); // Gjør om fra hexadecimal
    if (!password_verify($validatorBinary, $token)) { // Om den hash-ede tokenen i databasen ikke stemmer
        throw new Exception('Kunne ikke validere! Send ny forespørsel.');
    }
}

// Forandre glemt passord
function changeForgottenPassword($email, $password) {
    $user = getUserByEmail($email);
    if (!isValidPassword($password)) {
        throw new Exception('Passordet må inneholde minst 8 tegn, minst ett tall, én stor, én liten bokstav og ingen spesielle tegn!');
    } else {
        $sql = "UPDATE `user` SET `password` = :hashed_password WHERE `email` = :email";
        $stmt = $GLOBALS['db']->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if (!$stmt || !$hashedPassword) {
            throw new Exception('Noe gikk galt!');
        }
        $stmt->bindParam(':hashed_password', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    }
}

// Loggfører autentiseringsforsøk så vi kan utestenge noen fra å prøve for ofte
function logAuthentication($email, $response) {
    $sql = 'INSERT INTO authentication_log(`email`, `ip`, `response`, `time`) VALUES (:email, :ip, :response, NOW());';
    $stmt = $GLOBALS['db']->prepare($sql);
    if (!$stmt) {
        throw new Exception('Noe gikk galt!');
    }
    $ip = @$_SERVER['REMOTE_ADDR']; // Suppress-er warning dersom remote address ikke finnes
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':ip', $ip);
    $stmt->bindParam(':response', $response);
    $stmt->execute();
}

// Sjekker om en ip-addresse har prøvd for mange ganger å logge inn på en bruker.
function checkIfTooManyLoginAttempts($email) {
    $sql = 'SELECT COUNT(id) FROM `authentication_log` WHERE `email` = :email AND `ip` = :ip AND `response` = 0 AND `time` > DATE_SUB(NOW(), INTERVAL 30 MINUTE);';
    $stmt = $GLOBALS['db']->prepare($sql);
    if (!$stmt) {
        throw new Exception('Noe gikk galt!');
    } else {
        $ip = @$_SERVER['REMOTE_ADDR']; // Suppress-er warning dersom remote address ikke finnes
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ip', $ip);
        $stmt->execute();
        $loginAttempts = $stmt->fetchColumn();
        
        if ($loginAttempts > 10) { // Om brukeren har prøvd over 10 ganger den siste halvtimen
            return true;
        }
    }
}
  
// Logger bruker inn
function loginUser($email, $password) {
    $user = getUserByEmail($email); // Henter bruker
    if ($user) { // Om bruker finnes
        if (checkIfTooManyLoginAttempts($user['email'])) { // Hvis denne ip-addressen har prøvd å logge på denne brukeren over 10 ganger i løpet av en halvtime
            logAuthentication($email, 0); // Loggfør mislykket innloggingsforsøk
            throw new Exception('For mange forsøk! Vennligst prøv på nytt senere.');
        }
        if (!password_verify($password, $user['password'])) {
            logAuthentication($user['email'], 0);
            throw new Exception('Feil epost eller passord!');
        }
        logAuthentication($user['email'], 1); // Vellykket forsøk
        $_SESSION['userId'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['firstName'] = $user['first_name'];
        $_SESSION['lastName'] = $user['last_name'];
        $_SESSION['userType'] = $user['user_type'];
        $_SESSION['lastActivity'] = time();
    } else {
        logAuthentication($email, 0); // Loggfører også om epost ikke finnes i databasen for å avverge at en ondsinnet aktør kan teste om en epost finnes i databasen gjennom brute force angrep
        throw new Exception('Feil epost eller passord!');
    }
}

// Funksjon for å bytte passordet til en bruker
function changePassword($email, $oldPassword, $newPassword) {
    $user = getUserByEmail($email);
    if (password_verify($oldPassword, $user['password'])) {
        if (!isValidPassword($newPassword)) {
            throw new Exception('Passordet må inneholde minst 8 tegn, minst ett tall, én stor, én liten bokstav og ingen spesielle tegn!');
        } else {
            $sql = "UPDATE `user` SET `password` = :hashed_password WHERE `email` = :email";
            $stmt = $GLOBALS['db']->prepare($sql);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if (!$stmt || !$hashedPassword) {
                throw new Exception('Noe gikk galt!');
            }
            $stmt->bindParam(':hashed_password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
        }
    } else {
        throw new Exception('Feil passord! Vennligst prøv på nytt.');
    }
}

// Gammel kode som sjekker antall innloggingsforsøk, dette er kode som vi brukte når oppgaven var basert på Ringerikets database
/* function loginUser($email, $password) {
    date_default_timezone_set('Europe/Oslo'); //Setter tidssone slik at vi kan sammenligne datetimen i databasen som er norsk tid med tiden på serveren
    $user = getUserByEmail($email); // Henter bruker
    if ($user) { // Tester om bruker finnes
        $secondsSinceLastTry = time() - strtotime($user['sisteforsok']); // Sekunder siden forrige innloggingsforsøk
        if ($user['antallforsok'] < 5 || $user['antallforsok'] >= 5 && $secondsSinceLastTry >= 20) { // Sjekker om det er under 5 forsøk eller over 5 men brukeren har ventet i 20 sekunder
            if (sha1($GLOBALS['db']::$salt.$password) === $user['passord']) {
                $sql = "UPDATE bruker SET antallforsok = 1, sisteforsok = NOW() WHERE epost = :epost";
                $stmt = $GLOBALS['db']->prepare($sql);
                $stmt->bindParam(':epost', $email);
                $stmt->execute();
                session_start();
                $_SESSION['email'] = $user['epost'];
                $_SESSION['firstName'] = $user['fnavn'];
                $_SESSION['lastName'] = $user['enavn'];
                $_SESSION['userType'] = $user['brukertype'];
                $_SESSION['lastActivity'] = time();
                return true;
                } else {
                    $sql = ($user['antallforsok'] >= 5) ? "UPDATE bruker SET antallforsok = 1, sisteforsok = NOW() WHERE epost = :epost" : "UPDATE bruker SET antallforsok = antallforsok + 1, sisteforsok = NOW() WHERE epost = :epost";
                    $stmt = $GLOBALS['db']->prepare($sql);
                    $stmt->bindParam(':epost', $email);
                    $stmt->execute();
                    throw new Exception('Feil epost eller passord!');
                }
            } else {
                throw new Exception('For mange forsøk! Vennligst prøv på nytt om ' . (20 - $secondsSinceLastTry) . ' sekunder.'); // Sender feilmelding med tiden du må vente
            }
        } else {	
            throw new Exception('Feil epost eller passord!');
    }
} */