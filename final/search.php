<?php
require_once 'db.php';
$q = trim($_GET['q'] ?? '');
if ($q === '') header('Location: index.php');

$like = '%' . $q . '%';
$stmt = $conn->prepare("SELECT id FROM recipes WHERE name LIKE ? OR description LIKE ? OR ingredients LIKE ? OR tools LIKE ?");
$stmt->bind_param('ssss', $like, $like, $like, $like);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header('Location: results.html');
    exit;
}
header('Location: recipes.php?q=' . urlencode($q));
exit;
