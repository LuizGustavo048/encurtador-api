<?php

require __DIR__ . '/../src/UrlShortener.php';
$config = require __DIR__ . '/../src/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $urlShortener = new UrlShortener($config);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao conectar com o banco de dados: ' . $e->getMessage()]);
    exit();
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['url'])) {
        $shortCode = $urlShortener->shortenUrl($input['url']);
        echo json_encode([
            'original_url' => $input['url'],
            'short_url' => "https://encurt.vercel.app/{$shortCode}"
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'URL é requerida']);
    }
} elseif ($method === 'GET') {
    if (isset($_SERVER['REQUEST_URI'])) {
        $shortCode = trim($_SERVER['REQUEST_URI'], '/');
        if ($shortCode) {
            $originalUrl = $urlShortener->getOriginalUrl($shortCode);
            if ($originalUrl) {
                header("Location: $originalUrl");
                exit();
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'URL não encontrada']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Código curto não fornecido']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Código curto não fornecido']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
}

?>