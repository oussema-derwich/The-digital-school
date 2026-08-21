<?php
// Simple development server for the frontend
// Serves static files from public/ and index.html as fallback

$root = __DIR__;
$requested = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = $root . '/index.html'; // Fallback pour Vue Router

// Si c'est un fichier statique qui existe, le servir
if (file_exists($root . $requested) && is_file($root . $requested)) {
    $file = $root . $requested;
}

// Déterminer le type MIME
$mimeTypes = [
    'js'   => 'application/javascript',
    'json' => 'application/json',
    'css'  => 'text/css',
    'html' => 'text/html',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'svg'  => 'image/svg+xml',
    'woff' => 'font/woff',
    'woff2'=> 'font/woff2',
    'ttf'  => 'font/ttf',
    'eot'  => 'application/vnd.ms-fontobject',
];

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mime = $mimeTypes[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (file_exists($file)) {
    readfile($file);
} else {
    http_response_code(404);
    echo "404 Not Found: $requested";
}
