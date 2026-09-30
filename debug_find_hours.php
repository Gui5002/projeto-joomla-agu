<?php
$mysqli = new mysqli('127.0.0.1', 'root', '', 'moodle');

if ($mysqli->connect_error) {
    die("Erro: " . $mysqli->connect_error);
}

echo "<h2>1. Buscando ocorrências de '07 hrs' ou 'hrs 00 min' em tabelas de plugins/temas:</h2>";

// Tabelas prováveis do Cocoon e Moodle
$queries = [
    'mdl_config_plugins' => "SELECT plugin, name, value FROM mdl_config_plugins WHERE value LIKE '%07 hrs%' OR value LIKE '%hrs 00 min%' LIMIT 5",
    'mdl_course_format_options' => "SELECT * FROM mdl_course_format_options WHERE value LIKE '%07 hrs%' OR value LIKE '%hrs%' LIMIT 5",
    'mdl_course_categories' => "SELECT id, name, description FROM mdl_course_categories WHERE description LIKE '%07 hrs%' LIMIT 5",
    'mdl_block_instances' => "SELECT id, blockname, parentcontextid FROM mdl_block_instances WHERE configdata LIKE '%07%hrs%' OR configdata LIKE '%hrs%00%' LIMIT 5"
];

foreach ($queries as $table => $sql) {
    $res = $mysqli->query($sql);
    if ($res && $res->num_rows > 0) {
        echo "<h3>Encontrado na tabela <code>$table</code>:</h3><pre>";
        while ($row = $res->fetch_assoc()) {
            print_r($row);
        }
        echo "</pre>";
    }
}

echo "<h2>2. Buscando em todas as colunas que contenham '07 hrs 00 min':</h2>";
$tables = ['mdl_course', 'mdl_course_sections', 'mdl_course_modules', 'mdl_label', 'mdl_page'];
foreach ($tables as $t) {
    $res = $mysqli->query("SHOW COLUMNS FROM $t");
    if ($res) {
        while ($col = $res->fetch_assoc()) {
            $colName = $col['Field'];
            $searchRes = $mysqli->query("SELECT id, $colName FROM $t WHERE $colName LIKE '%07 hrs%' OR $colName LIKE '%07 hrs 00 min%' LIMIT 3");
            if ($searchRes && $searchRes->num_rows > 0) {
                echo "<h3>Encontrado em <code>$t.$colName</code>:</h3><pre>";
                while ($r = $searchRes->fetch_assoc()) {
                    print_r($r);
                }
                echo "</pre>";
            }
        }
    }
}