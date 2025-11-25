<?php

require 'db.php';

$csvPath = __DIR__ . '/data/recipe.csv';

if (!file_exists($csvPath)) {
    die("CSV not found at $csvPath");
}

// Open CSV with proper encoding handling
$raw = file_get_contents($csvPath);

// Try to detect encoding and convert to UTF-8 (best-effort)
$encoding = mb_detect_encoding($raw, ['UTF-8','Windows-1252','ISO-8859-1','ASCII'], true);
if ($encoding !== 'UTF-8') {
    $raw = mb_convert_encoding($raw, 'UTF-8', $encoding);
}

// Use PHP's temp stream to parse CSV with fgetcsv (handles multiline)
$stream = fopen('php://memory','r+');
fwrite($stream, $raw);
rewind($stream);

$header = fgetcsv($stream); // expect: name,name_pt2,description,ingredients,tools,tool_description,steps,images
if (!$header) {
    die("CSV header not found or couldn't parse file.");
}

// normalize header names to keys
$header = array_map(function($h){ return trim($h); }, $header);

$insertStmt = $pdo->prepare("
    INSERT INTO recipes
    (name, subtitle, description, ingredients, tools, tool_description, steps, images)
    VALUES (:name, :subtitle, :description, :ingredients, :tools, :tool_description, :steps, :images)
");

$counter = 0;
while (($row = fgetcsv($stream)) !== false) {
    // Make sure row length matches header length by filling missing with null
    if (count($row) < count($header)) {
        $row = array_pad($row, count($header), '');
    }

    $data = array_combine($header, $row);

    // Some CSVs include stray '*', trim that
    $name = trim($data['name'] ?? '');
    $subtitle = trim($data['name_pt2'] ?? '');
    $description = trim($data['description'] ?? '');
    $ingredients = trim($data['ingredients'] ?? '');
    $tools = trim($data['tools'] ?? '');
    $tool_description = trim($data['tool_description'] ?? '');
    $steps = trim($data['steps'] ?? '');
    $images = trim($data['images'] ?? '');

    if ($name === '') continue; // skip blank lines

    $insertStmt->execute([
        ':name' => $name,
        ':subtitle' => $subtitle,
        ':description' => $description,
        ':ingredients' => $ingredients,
        ':tools' => $tools,
        ':tool_description' => $tool_description,
        ':steps' => $steps,
        ':images' => $images
    ]);
    $counter++;
}

fclose($stream);

echo "Imported $counter recipes.\n";
