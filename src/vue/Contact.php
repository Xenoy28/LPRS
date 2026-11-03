<?php
require_once __DIR__ . '/../repository/ContactRepository.php';

$titre   = 'Contact';
$erreur  = '';
$nom     = $_SESSION['user_nom'] ?? '';     // pré-rempli si connecté
$email   = $_SESSION['user_email'] ?? '';
$sujet   = '';
$message = '';

if (empty($_SESSION['contact_csrf'])) {
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $sujet   = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!hash_equals($_SESSION['contact_csrf'], (string)($_POST['csrf'] ?? ''))) {
        $erreur = 'Session expirée, recharge la page et réessaie.';
    } elseif (!empty($_POST['site_web'])) {
        // Champ piège rempli (robot) : on fait semblant que tout va bien
        header('Location: index.php?page=contact&envoye=1');
        exit;
    } elseif ($nom === '' || $email === '' || $sujet === '' || $message === '') {
        $erreur = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Adresse email invalide.';
    } elseif (mb_strlen($nom) > 150 || mb_strlen($email) > 150 || mb_strlen($sujet) > 200 || mb_strlen($message) > 2000) {
        $erreur = 'Un des champs est trop long.';
    } else {
        (new ContactRepository())->creer($nom, $email, $sujet, $message);
        header('Location: index.php?page=contact&envoye=1');
        exit;
    }
}

require __DIR__ . '/_entete.php';
?>

    <p>
        Une question ou une demande d'information ? Écrivez aux gestionnaires de la plateforme,
        nous vous répondrons dans les meilleurs délais.
    </p>

<?php if (!empty($_GET['envoye'])): ?>
    <div class="succes">Votre message a bien été envoyé. Merci !</div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="erreur"><?= h($erreur) ?></div>
<?php endif; ?>

    <form method="post" action="index.php?page=contact">
        <input type="hidden" name="csrf" value="<?= h($_SESSION['contact_csrf']) ?>">

        <label for="nom">Nom et prénom</label>
        <input type="text" id="nom" name="nom" maxlength="150" value="<?= h($nom) ?>" required>

        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" maxlength="150" value="<?= h($email) ?>" required>

        <label for="sujet">Sujet</label>
        <input type="text" id="sujet" name="sujet" maxlength="200" value="<?= h($sujet) ?>" required>

        <label for="message">Message</label>
        <textarea id="message" name="message" rows="8" maxlength="2000" required><?= h($message) ?></textarea>

        <!-- Champ piège anti-robots : ne pas remplir -->
        <div class="piege" aria-hidden="true">
            <input type="text" name="site_web" tabindex="-1" autocomplete="off">
        </div>

        <p class="maj">
            Les informations saisies servent uniquement à répondre à votre demande
            (voir les <a href="index.php?page=mentions_legales">mentions légales</a>).
        </p>
        <button class="btn" type="submit">Envoyer</button>
    </form>

<?php require __DIR__ . '/_pied.php'; ?>