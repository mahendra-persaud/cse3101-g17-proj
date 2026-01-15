<?php
require_once __DIR__ . '/config/database.php';
$pdo = getDBConnection();

function dumpTable($pdo, $table) {
    echo "\n--- Table: $table ---\n";
    $stmt = $pdo->query("DESCRIBE $table");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $col) {
        echo "{$col['Field']} | {$col['Type']} | Null: {$col['Null']} | Key: {$col['Key']} | Default: {$col['Default']} | Extra: {$col['Extra']}\n";
    }
}

dumpTable($pdo, 'school_years');
dumpTable($pdo, 'terms');
