<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <style>
        body { margin: 0; font-family: sans-serif; background: #f5f7f8; min-height: 100vh; display: flex; flex-direction: column; }

        /* Barre en 3 compartiments */
        .topbar { display: grid; grid-template-columns: 1fr 2fr 1fr; height: 60px; background: #0f4c5c; color: #fff; }
        .topbar > div { display: flex; align-items: center; padding: 0 20px; }
        .centre { justify-content: center; gap: 20px; }
        .droite { justify-content: flex-end; position: relative; }
        .centre a { color: #fff; text-decoration: none; }

        /* Burger */
        .burger { background: none; border: none; color: #fff; font-size: 30px; cursor: pointer; }

        /* Menu */
        .menu { display: none; position: absolute; top: 60px; right: 0; background: #fff; list-style: none; margin: 0; padding: 0; min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,.2); }
        .menu a { display: block; padding: 14px 20px; color: #222; text-decoration: none; }
        .menu a:hover { background: #e6eef0; }

        .hero { text-align: center; padding: 80px 20px; flex: 1; }

        /* Pied de page (style site gouvernemental) */
        .footer { background: #fff; border-top: 3px solid #0f4c5c; padding: 20px 30px; }
        .footer-liens { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; justify-content: flex-start; }
        .footer-liens li { padding: 0 16px; border-left: 1px solid #c9d3d6; }
        .footer-liens li:first-child { padding-left: 0; border-left: none; }
        .footer-liens a { color: #161616; font-size: 14px; text-decoration: none; }
        .footer-liens a:hover { text-decoration: underline; color: #0f4c5c; }
        .footer-copy { margin: 14px 0 0; font-size: 12px; color: #666; text-align: left; }
    </style>
</head>
<body>

<div class="topbar">
    <div>LPRS</div>

    <div class="centre">
        <a href="#">Accueil</a>
        <a href="#">Services</a>
        <a href="src/traitement/Forum.php">Forum</a>
    </div>

    <div class="droite">
        <button class="burger" onclick="toggleMenu()">☰</button>
        <ul class="menu" id="menu">
            <li><a href="index.php?page=profil">Profil</a></li>
            <li><a href="index.php?page=login">Se connecter</a></li>
            <li><a href="index.php?page=inscription">S'inscrire</a></li>
            <li><a href="src/traitement/Logout.php">Déconnexion</a></li>
            <li><a href="#">Paramètres</a></li>
        </ul>
    </div>
</div>

<div class="hero">
    <h1>Bienvenue sur LPRS</h1>
    <p>Voici la page d'accueil.</p>
</div>

<footer class="footer">
    <ul class="footer-liens">
        <li><a href="#">Plan du site</a></li>
        <li><a href="#">Mentions légales</a></li>
        <li><a href="#">Contact</a></li>
    </ul>
    <p class="footer-copy">© 2026 LPRS – Tous droits réservés</p>
</footer>

<script>
    function toggleMenu() {
        const menu = document.getElementById("menu");
        menu.style.display = menu.style.display === "block" ? "none" : "block";
    }
</script>
</body>
</html>