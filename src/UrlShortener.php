<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

class UrlShortener {
    private $pdo;

    public function __construct($dbConfig) {
        $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};sslmode=require;sslrootcert={$dbConfig['sslrootcert']}";
        try {
            $this->pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    private function generateRandomString($length = 6) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    public function shortenUrl($originalUrl) {
        $stmt = $this->pdo->prepare("SELECT short_code FROM urls WHERE original_url = :originalUrl");
        $stmt->execute(['originalUrl' => $originalUrl]);
        $existingUrl = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUrl) {
            return $existingUrl['short_code'];
        } else {
            $shortCode = $this->generateRandomString();
            $stmt = $this->pdo->prepare("INSERT INTO urls (original_url, short_code) VALUES (:originalUrl, :shortCode)");
            $stmt->execute(['originalUrl' => $originalUrl, 'shortCode' => $shortCode]);
            return $shortCode;
        }
    }

    public function getOriginalUrl($shortCode) {
        $stmt = $this->pdo->prepare("SELECT original_url FROM urls WHERE short_code = :shortCode");
        $stmt->execute(['shortCode' => $shortCode]);
        $url = $stmt->fetch(PDO::FETCH_ASSOC);
        return $url ? $url['original_url'] : null;
    }
}
?>