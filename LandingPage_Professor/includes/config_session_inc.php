<?php
ini_set("session.use_only_cookies", 1);
ini_set("session.use_strict_mode", 1);

$lifetime = 1800; // 30mins

session_set_cookie_params([
    "lifetime" => $lifetime,
    "domain" => "localhost",
    "path" => "/",
    "secure" => true,
    "httponly" => true,
]);

session_start();

if (!isset($_SESSION['last_regeneration'])) {
    regenerateSessionId();
    return;
}

// regenerate session id every 30mins
if (time() - $_SESSION['last_regeneration'] >= $lifetime) {
    regenerateSessionId();
}

function regenerateSessionIdLoggedIn()
{
    session_regenerate_id(true);
    $newSessionId = session_create_id() . "_" . $_SESSION['user_ID'];
    session_id($newSessionId);
    $_SESSION['last_regeneration'] = time();
}

function regenerateSessionId()
{
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}