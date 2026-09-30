<?php
$token      = '9130618814e6981cc1aa223e78575de2';
$endpoint   = 'http://localhost:8080/moodle/webservice/rest/server.php';
$targetId   = '931'; // ID do Curso de Ambientação da imagem

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'wstoken'            => $token,
    'wsfunction'         => 'core_course_get_courses_by_field',
    'moodlewsrestformat' => 'json',
    'field'              => 'id',
    'value'              => $targetId
]);

$response = json_decode(curl_exec($ch), true);
curl_close($ch);

echo "<h1>Inspeção do Curso ID {$targetId}</h1>";
echo "<pre style='background:#f1f5f9; padding:15px; border-radius:8px;'>";
print_r($response);
echo "</pre>";