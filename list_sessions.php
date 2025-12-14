<?php
require 'config.php';

header('Content-Type: application/json');

$sessions = [];
$files = glob(DATA_DIR . '/*.json');

// Ordenar por fecha de modificacion (mas reciente primero)
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

foreach ($files as $file) {
    $content = file_get_contents($file);
    if ($content) {
        $data = json_decode($content, true);
        if ($data) {
            // Calcular tiempo inactivo
            $lastSeen = $data['last_seen'] ?? 0;
            $inactiveSeconds = time() - $lastSeen;
            
            // Si tiene mas de 1 hora inactivo, lo ignoramos (opcional, por ahora mostramos todos)
            // if ($inactiveSeconds > 3600) continue;

            $sessions[] = [
                'id' => $data['id'] ?? basename($file, '.json'),
                'usuario' => $data['usuario'] ?? 'Desconocido', // Este campo se guardará en login.php
                'ip' => $data['ip'] ?? '???', // Se guardará en login.php
                'status' => $data['status'] ?? 'unknown',
                'last_seen' => $lastSeen,
                'last_seen_fmt' => date("H:i:s", $lastSeen),
                'is_online' => ($inactiveSeconds < 10) // Online si visto en ultimos 10s
            ];
        }
    }
}

echo json_encode(['status' => 'ok', 'sessions' => $sessions]);
