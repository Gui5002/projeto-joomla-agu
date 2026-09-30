<?php
namespace MyCompany\Module\MoodleCourses\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Http\HttpFactory;
use Joomla\Registry\Registry;

class MoodleHelper
{
    public function getCourses($params = null): array
    {
        if (!$params instanceof Registry) {
            $params = new Registry($params ?? []);
        }

        $moodleUrl = rtrim($params->get('moodle_url', ''), '/');
        $token     = trim($params->get('moodle_token', ''));

        if (empty($moodleUrl) || empty($token)) {
            return [];
        }

        $endpoint = $moodleUrl . '/webservice/rest/server.php';

        try {
            $http = HttpFactory::getHttp();

            // 1. LISTA DE IDS DE CATEGORIAS PAI A SEREM OCULTADAS
            // 38  = Cursos e Eventos Inativos
            // 19  = Pós-Graduação
            // 280 = Categoria Oculta Solicitada
            $rootCategoriesToExclude = [38, 19, 280];

            // Resolve toda a árvore de subcategorias e busca os cursos dessas categorias
            $excludedCourseIds = $this->getExcludedCourseIdsViaWebService($rootCategoriesToExclude, $endpoint, $token, $http);

            // 2. Busca todos os cursos via WebService do Moodle
            $response = $http->post($endpoint, [
                'wstoken'            => $token,
                'wsfunction'         => 'core_course_get_courses',
                'moodlewsrestformat' => 'json'
            ], [], 15);

            if ($response->code !== 200) {
                return [];
            }

            $courses = json_decode($response->body, true);
            if (!is_array($courses) || isset($courses['exception'])) {
                return [];
            }

            // 3. Filtra cursos mantendo apenas os visíveis e fora dos IDs excluídos
            $filtered = array_filter($courses, function ($c) use ($excludedCourseIds) {
                $cid   = isset($c['id']) ? (int)$c['id'] : 0;

                $isNotFrontpage = $cid > 1;
                $isVisible      = !isset($c['visible']) || (int)$c['visible'] === 1;
                $isNotExcluded  = !in_array($cid, $excludedCourseIds, true);

                return $isNotFrontpage && $isVisible && $isNotExcluded;
            });

            // Ordena em Ordem Alfabética Natural (A-Z e números) ignorando acentos
            usort($filtered, function ($a, $b) {
                $nameA = mb_strtolower(trim($a['fullname'] ?? ''), 'UTF-8');
                $nameB = mb_strtolower(trim($b['fullname'] ?? ''), 'UTF-8');
                
                // Mapeia e substitui caracteres acentuados para suas versões limpas
                $cleanA = preg_replace(
                    ['/[áàãâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòõôö]/u', '/[úùûü]/u', '/[ç]/u'],
                    ['a', 'e', 'i', 'o', 'u', 'c'],
                    $nameA
                );
                
                $cleanB = preg_replace(
                    ['/[áàãâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòõôö]/u', '/[úùûü]/u', '/[ç]/u'],
                    ['a', 'e', 'i', 'o', 'u', 'c'],
                    $nameB
                );

                return strnatcasecmp($cleanA, $cleanB);
            });

            $filtered = array_values($filtered);

            // 4. Busca capas (overviewfiles) e nomes das categorias aceitas
            $courseIdsChunk = array_slice(array_column($filtered, 'id'), 0, 150);
            $overviewMap = [];
            $categoryMap = [];

            if (!empty($courseIdsChunk)) {
                try {
                    $batchResp = $http->post($endpoint, [
                        'wstoken'            => $token,
                        'wsfunction'         => 'core_course_get_courses_by_field',
                        'moodlewsrestformat' => 'json',
                        'field'              => 'ids',
                        'value'              => implode(',', $courseIdsChunk)
                    ], [], 8);

                    if ($batchResp->code === 200) {
                        $batchData = json_decode($batchResp->body, true);
                        if (!empty($batchData['courses'])) {
                            foreach ($batchData['courses'] as $cDetail) {
                                $cid = (int)$cDetail['id'];
                                $categoryMap[$cid] = $cDetail['categoryname'] ?? '';

                                if (!empty($cDetail['overviewfiles'][0]['fileurl'])) {
                                    $rawFileUrl = $cDetail['overviewfiles'][0]['fileurl'];
                                    $cleanUrl   = str_replace('/webservice/pluginfile.php', '/pluginfile.php', $rawFileUrl);
                                    $evaUrl     = preg_replace('#^https?://[^/]+(/moodle)?/#i', 'https://eva.agu.gov.br/', $cleanUrl);

                                    $overviewMap[$cid] = $this->fetchImageAsBase64($evaUrl);
                                }
                            }
                        }
                    }
                } catch (\Throwable $t) {}
            }

            // 5. Lê cargas horárias
            $workloadMap = $this->getWorkloadsFromEvaTable();

            // 6. Monta o payload final
            $result = [];
            foreach ($filtered as $course) {
                $cid = (int)$course['id'];
                $course['image_url']     = $overviewMap[$cid] ?? '';
                $course['category_name'] = !empty($categoryMap[$cid]) ? $categoryMap[$cid] : 'Cursos';
                $course['workload']      = $workloadMap[$cid] ?? '';

                $result[] = $course;
            }

            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Mapeia todas as subcategorias via WebService e retorna a lista de IDs de cursos a excluir
     */
    private function getExcludedCourseIdsViaWebService(array $rootCatIds, string $endpoint, string $token, $http): array
    {
        $allExcludedCatIds = $rootCatIds;
        $excludedCourseIds = [];

        try {
            // 1. Busca a lista completa de categorias do Moodle
            $respCats = $http->post($endpoint, [
                'wstoken'            => $token,
                'wsfunction'         => 'core_course_get_categories',
                'moodlewsrestformat' => 'json'
            ], [], 8);

            if ($respCats->code === 200) {
                $categories = json_decode($respCats->body, true);
                if (is_array($categories) && !isset($categories['exception'])) {
                    // Percorre recursivamente para encontrar todas as subcategorias filhas das categorias informadas
                    $addedNew = true;
                    while ($addedNew) {
                        $addedNew = false;
                        foreach ($categories as $cat) {
                            $catId    = (int)($cat['id'] ?? 0);
                            $parentId = (int)($cat['parent'] ?? 0);
                            $path     = (string)($cat['path'] ?? '');

                            // Verifica se o pai ou algum ancestral está no array de excluídos
                            $isDescendant = false;
                            if (in_array($parentId, $allExcludedCatIds, true)) {
                                $isDescendant = true;
                            } else if (!empty($path)) {
                                foreach ($allExcludedCatIds as $exId) {
                                    if (strpos($path, '/' . $exId . '/') !== false || sprintf('/%d', $exId) === substr($path, -strlen((string)$exId) - 1)) {
                                        $isDescendant = true;
                                        break;
                                    }
                                }
                            }

                            if ($isDescendant && !in_array($catId, $allExcludedCatIds, true)) {
                                $allExcludedCatIds[] = $catId;
                                $addedNew = true;
                            }
                        }
                    }
                }
            }

            // 2. Busca cursos específicos dentro de cada categoria excluída
            foreach ($allExcludedCatIds as $catId) {
                $respCourses = $http->post($endpoint, [
                    'wstoken'            => $token,
                    'wsfunction'         => 'core_course_get_courses_by_field',
                    'moodlewsrestformat' => 'json',
                    'field'              => 'category',
                    'value'              => (string)$catId
                ], [], 8);

                if ($respCourses->code === 200) {
                    $catData = json_decode($respCourses->body, true);
                    if (!empty($catData['courses']) && is_array($catData['courses'])) {
                        foreach ($catData['courses'] as $c) {
                            if (isset($c['id'])) {
                                $excludedCourseIds[] = (int)$c['id'];
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $t) {}

        // Fallback via MySQL para garantir integridade caso a API oculta tenha falhado
        try {
            $mysqli = @new \mysqli('127.0.0.1', 'root', '', 'moodle');
            if (!$mysqli->connect_error) {
                $where = [];
                foreach ($allExcludedCatIds as $catId) {
                    $id = (int)$catId;
                    $where[] = "cat.path LIKE '%/$id/%' OR cat.path LIKE '%/$id' OR cat.id = $id";
                }
                if (!empty($where)) {
                    $sql = "SELECT c.id AS courseid FROM mdl_course c JOIN mdl_course_categories cat ON cat.id = c.category WHERE " . implode(' OR ', $where);
                    $res = $mysqli->query($sql);
                    if ($res) {
                        while ($row = $res->fetch_assoc()) {
                            $excludedCourseIds[] = (int)$row['courseid'];
                        }
                    }
                }
                $mysqli->close();
            }
        } catch (\Throwable $e) {}

        return array_unique($excludedCourseIds);
    }

    /**
     * Download cURL da imagem convertida em Base64
     */
    private function fetchImageAsBase64(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $data = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($code === 200 && !empty($data)) {
            if (empty($mime) || strpos($mime, 'image/') === false) {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime  = $finfo->buffer($data);
            }

            if (strpos($mime, 'image/') === 0) {
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        }

        return '';
    }

    private function getWorkloadsFromEvaTable(): array
    {
        $map = [];
        try {
            $mysqli = @new \mysqli('127.0.0.1', 'root', '', 'moodle');
            if ($mysqli->connect_error) {
                return $map;
            }

            $checkTable = $mysqli->query("SHOW TABLES LIKE 'mdl_eva_course_workload'");
            if ($checkTable && $checkTable->num_rows > 0) {
                $res = $mysqli->query("SELECT courseid, workload FROM mdl_eva_course_workload WHERE workload IS NOT NULL AND workload != ''");
                if ($res) {
                    while ($row = $res->fetch_assoc()) {
                        $cid = (int)$row['courseid'];
                        $parsed = $this->normalizeWorkload((string)$row['workload']);
                        if (!empty($parsed)) {
                            $map[$cid] = $parsed;
                        }
                    }
                }
            }

            $sqlBlocks = "SELECT c.instanceid AS courseid, b.configdata 
                          FROM mdl_block_instances b
                          JOIN mdl_context c ON c.id = b.parentcontextid
                          WHERE c.contextlevel = 50 
                            AND b.blockname = 'eva_course_details'
                            AND b.configdata IS NOT NULL";

            $resBlocks = $mysqli->query($sqlBlocks);
            if ($resBlocks) {
                while ($row = $resBlocks->fetch_assoc()) {
                    $cid = (int)$row['courseid'];
                    if (!isset($map[$cid])) {
                        $unserialized = @unserialize(@base64_decode($row['configdata']));
                        if (is_object($unserialized) || is_array($unserialized)) {
                            $blockStr = json_encode($unserialized, JSON_UNESCAPED_UNICODE);
                            $parsed = $this->normalizeWorkload($blockStr);
                            if (!empty($parsed)) {
                                $map[$cid] = $parsed;
                            }
                        }
                    }
                }
            }

            $mysqli->close();
        } catch (\Throwable $e) {}

        return $map;
    }

    private function normalizeWorkload(string $text): string
    {
        if (empty($text)) return '';
        $clean = strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
        $clean = str_replace(['\\', '"', '\'', ':', '-', '_'], ' ', $clean);

        if (preg_match('/(?:Carga\s*hor[aá]ria\s*)?0*(\d+)\s*(?:hrs|hr|horas?)\s*(?:0*(\d+)\s*min)?/i', $clean, $m)) {
            return (int)$m[1] . 'h';
        }
        if (preg_match('/0*(\d+[\.,]?\d*)\s*h\b/i', $clean, $m)) {
            $num = (float)str_replace(',', '.', $m[1]);
            return (int)$num == $num ? (int)$num . 'h' : $num . 'h';
        }
        if (preg_match('/^\s*0*(\d+)\s*$/', $clean, $m)) {
            return (int)$m[1] . 'h';
        }
        return '';
    }
}