<?php
// Evita acesso direto ao arquivo
defined('_JEXEC') or die;

// Configurações da API do Moodle
$domainname = 'http://localhost:8080/moodle'; 
$token = '948e425a5455fc2049612b8332abe167';            
$functionname = 'core_course_get_courses';

$serverurl = $domainname . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $functionname . '&moodlewsrestformat=json';

$ch = curl_init($serverurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$courses = json_decode($response, true);

if (!empty($courses) && is_array($courses)) {
    echo '<div class="moodle-courses-grid" style="display: grid; gap: 15px;">';
    foreach ($courses as $course) {
        if (isset($course['id']) && $course['id'] == 1) continue;

        $fullname = isset($course['fullname']) ? htmlspecialchars($course['fullname']) : '';
        $summary = isset($course['summary']) ? strip_tags($course['summary']) : '';
        $summary_short = mb_substr($summary, 0, 120) . '...';
        $course_id = isset($course['id']) ? $course['id'] : '';

        echo '<div class="course-card" style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; background: #fff;">';
        echo '<h3 style="margin-top: 0; color: #0066cc;">' . $fullname . '</h3>';
        echo '<p style="color: #555; font-size: 14px;">' . $summary_short . '</p>';
        echo '<a href="' . $domainname . '/course/view.php?id=' . $course_id . '" target="_blank" style="display: inline-block; background: #0066cc; color: #fff; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 13px;">Acessar Ambiente</a>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>Nenhum curso encontrado ou erro na conexão com a API do Moodle.</p>';
}
?>