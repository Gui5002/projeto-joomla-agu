<?php
$token = '9130618814e6981cc1aa223e78575de2';
$endpoint = 'http://localhost:8080/moodle/webservice/rest/server.php';

// 1. Testar core_course_get_courses
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'wstoken' => $token,
    'wsfunction' => 'core_course_get_courses',
    'moodlewsrestformat' => 'json'
]);
$res1 = json_decode(curl_exec($ch), true);
curl_close($ch);

// 2. Pegar o último curso cadastrado
$ultimoCurso = end($res1);

echo "<h2>1. Dados do Último Curso retornado por 'core_course_get_courses':</h2>";
echo "<pre>";
print_r([
    'id' => $ultimoCurso['id'] ?? null,
    'fullname' => $ultimoCurso['fullname'] ?? null,
    'overviewfiles' => $ultimoCurso['overviewfiles'] ?? 'CAMPO NÃO EXISTE NO RETORNO',
    'summary' => substr(strip_tags($ultimoCurso['summary'] ?? ''), 0, 100)
]);
echo "</pre>";

// 3. Testar a função core_course_get_courses_by_field para este curso
$ch2 = curl_init($endpoint);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, [
    'wstoken' => $token,
    'wsfunction' => 'core_course_get_courses_by_field',
    'moodlewsrestformat' => 'json',
    'field' => 'id',
    'value' => (string)($ultimoCurso['id'] ?? 881)
]);
$res2 = json_decode(curl_exec($ch2), true);
curl_close($ch2);

echo "<h2>2. Resposta de 'core_course_get_courses_by_field':</h2>";
echo "<pre>";
print_r($res2);
echo "</pre>";