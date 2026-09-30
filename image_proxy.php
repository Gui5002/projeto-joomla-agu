<?php
// Desativa exibição de erros na saída da imagem
error_reporting(0);
ini_set('display_errors', 0);

$url = isset($_GET['url']) ? trim($_GET['url']) : '';

if (empty($url)) {
    http_response_code(400);
    exit('URL ausente');
}

// Permite apenas URLs válidas do ecossistema EVA / Moodle
if (!preg_match('#^https?://(localhost|127\.0\.0\.1|eva\.agu\.gov\.br)(:\d+)?/.*pluginfile\.php#i', $url)) {
    http_response_code(403);
    exit('Acesso restrito');
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 6);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$imageData = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$mimeType  = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if ($httpCode === 200 && !empty($imageData) && strpos($mimeType, 'image/') === 0) {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: ' . $mimeType);
    header('Cache-Control: public, max-age=86400');
    header('Content-Length: ' . strlen($imageData));
    echo $imageData;
    exit;
}

http_response_code(404);
exit;