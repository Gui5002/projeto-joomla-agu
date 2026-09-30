<?php
$token = '9130618814e6981cc1aa223e78575de2';
$imgUrl = "http://127.0.0.1:8080/moodle/webservice/pluginfile.php/42909/course/overviewfiles/BannerPrincipal_Novos%20integrantes%20AGU_Turma%20I.jpg?token=" . $token;

$ch = curl_init($imgUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

echo "<h3>HTTP Code: $httpCode</h3>";
echo "<h3>MIME: $mime</h3>";

if ($httpCode === 200 && strpos($mime, 'image/') === 0) {
    echo '<img src="data:' . $mime . ';base64,' . base64_encode($data) . '" style="max-width:400px;"/>';
} else {
    echo "<pre>Resposta: " . htmlspecialchars(substr($data, 0, 300)) . "</pre>";
}