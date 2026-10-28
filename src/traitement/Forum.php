<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/../repository/CanalRepository.php';
require_once __DIR__ . '/../repository/PostRepository.php';
require_once __DIR__ . '/../repository/ReponseRepository.php';

const FORUM_URL = 'Forum.php';

const LONGUEUR_TITRE   = 150;
const LONGUEUR_MESSAGE = 5000;
const POSTS_PAR_PAGE   = 10;

function e($texte)
{
    return htmlspecialchars((string)$texte, ENT_QUOTES, 'UTF-8');
}

function lien_forum(array $params = [])
{
    if (!$params) {
        return FORUM_URL;
    }
    return FORUM_URL . (strpos(FORUM_URL, '?') === false ? '?' : '&') . http_build_query($params);
}

function format_date($date)
{
    return date('d/m/Y à H:i', strtotime($date));
}

/* ---------- Protection CSRF ---------- */
function csrf_champ()
{
    if (empty($_SESSION['forum_csrf'])) {
        $_SESSION['forum_csrf'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf" value="' . $_SESSION['forum_csrf'] . '">';
}

function csrf_valide()
{
    return isset($_POST['csrf'], $_SESSION['forum_csrf'])
        && hash_equals($_SESSION['forum_csrf'], (string)$_POST['csrf']);
}