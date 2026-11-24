<?php
require_once __DIR__ . '/../repository/EvenementRepository.php';
$prochains = (new EvenementRepository())->listePublies(false, 3);
$connecte = !empty($_SESSION['user_id']);
?>
<?php
$titre = 'Accueil';
$sansFil = true;
require __DIR__ . '/_entete.php';
?>

<div class="hero">
    <h2>Bienvenue sur LPRS</h2>
    <p>Fondé en 1920, le lycée Robert Schuman forme chaque année plus de 450 élèves, du bac professionnel au BTS.</p>
    <p>Cette plateforme rassemble l'école, ses anciens élèves, ses étudiants, ses professeurs et ses entreprises partenaires :
        offres de stage et d'emploi, événements, annuaire et échanges.</p>

</div>

<h2>Prochains événements</h2>
<?php foreach ($prochains as $e): ?>
    <div class="carte">
        <div class="meta"><?= date('d/m/Y H:i', strtotime($e->date_debut)) ?> – <?= htmlspecialchars($e->lieu) ?></div>
        <h3>
            <?php if ($connecte && in_array($_SESSION['user_role'], MEMBRES)): ?>
                <a href="index.php?page=evenement&id=<?= (int)$e->id_evenement ?>"><?= htmlspecialchars($e->titre) ?></a>
            <?php else: ?>
                <?= htmlspecialchars($e->titre) ?>
            <?php endif; ?>
        </h3>
    </div>
<?php endforeach; ?>
<?php if (!$prochains): ?><p class="meta">Aucun événement à venir.</p><?php endif; ?>

<?php require __DIR__ . '/_pied.php'; ?>
