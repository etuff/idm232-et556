<?php
// import_csv.php
require_once 'db.php';

$csvFile = __DIR__ . '/newexcel.csv';
if (!file_exists($csvFile)) {
    die("CSV file not found at $csvFile");
}

$handle = fopen($csvFile, 'r');
if ($handle === false) {
    die("Failed to open CSV file.");
}

// Read header to allow CSVs with header row
$header = fgetcsv($handle);
if ($header === false) {
    die("CSV is empty.");
}

// Prepare statements: check, insert, update
$checkStmt = $conn->prepare("SELECT id FROM recipes WHERE name = ?");
$insertStmt = $conn->prepare("INSERT INTO recipes (name, subtitle, description, ingredients, tools, tool_description, steps, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$updateStmt = $conn->prepare("UPDATE recipes SET subtitle = ?, description = ?, ingredients = ?, tools = ?, tool_description = ?, steps = ?, images = ?, updated_at = NOW() WHERE name = ?");

$rowsAdded = 0;
$rowsUpdated = 0;
$line = 1;

while (($row = fgetcsv($handle)) !== false) {
    $line++;
    // Ensure at least 1 column (name)
    $name = trim($row[0] ?? '');
    if ($name === '') continue;

    $subtitle = $row[1] ?? '';
    $description = $row[2] ?? '';
    $ingredients = $row[3] ?? '';
    $tools = $row[4] ?? '';
    $tool_description = $row[5] ?? '';
    $steps = $row[6] ?? '';
    $images = $row[7] ?? '';

    // Check existence
    $checkStmt->bind_param('s', $name);
    $checkStmt->execute();
    $res = $checkStmt->get_result();

    if ($res && $res->num_rows > 0) {
        // update
        $updateStmt->bind_param('ssssssss', $subtitle, $description, $ingredients, $tools, $tool_description, $steps, $images, $name);
        if ($updateStmt->execute()) $rowsUpdated++;
    } else {
        // insert
        $insertStmt->bind_param('ssssssss', $name, $subtitle, $description, $ingredients, $tools, $tool_description, $steps, $images);
        if ($insertStmt->execute()) $rowsAdded++;
    }
}

fclose($handle);

echo "Import complete. Added: $rowsAdded. Updated: $rowsUpdated.";
