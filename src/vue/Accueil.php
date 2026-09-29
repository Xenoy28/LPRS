<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil</title>
  <style>
body { margin: 0; font-family: sans-serif; background: #f5f7f8; }

    .topbar { display: grid; grid-template-columns: 1fr 2fr 1fr; height: 60px; background: #0f4c5c; color: #fff; }
    .topbar > div { display: flex; align-items: center; padding: 0 20px; }
    .centre { justify-content: center; gap: 20px; }
    .droite { justify-content: flex-end; position: relative; }
    .centre a { color: #fff; text-decoration: none; }

            .burger { background: none; border: none; color: #fff; font-size: 30px; cursor: pointer; }

                .menu { display: none; position: absolute; top: 60px; right: 0; background: #fff; list-style: none; margin: 0; padding: 0; min-width: 180px; box-shadow: 0 4px 12px rgba(0,0,0,.2); }
    .menu a { display: block; padding: 14px 20px; color: #222; text-decoration: none; }
    .menu a:hover { background: #e6eef0; }

                            .hero { text-align: center; padding: 80px 20px; }
  </style>
</head>
<body>

  <div class="topbar">
    <div>MonSite</div>

    <div class="centre">
      <a href="#">Accueil</a>
      <a href="#">Services</a>
      <a href="#">Contact</a>
    </div>

    <div class="droite">
      <button class="burger" onclick="toggleMenu()">☰</button>
      <ul class="menu" id="menu">
        <li><a href="#">Profil</a></li>
        <li><a href="#">Se connecter</a></li>
        <li><a href="#">Déconnexion</a></li>
        <li><a href="#">Paramètres</a></li>
      </ul>
    </div>
  </div>

  <div class="hero">
    <h1>Bienvenue sur MonSite</h1>
    <p>Voici la page d'accueil.</p>
  </div>

  <script>
    function toggleMenu() {
      const menu = document.getElementById("menu");
      menu.style.display = menu.style.display === "block" ? "none" : "block";
    }
  </script>
</body>
</html>