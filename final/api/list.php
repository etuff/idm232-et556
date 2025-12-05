<?php
require_once '../db.php';
header('Content-Type: application/json; charset=utf-8');

$stmt = $conn->prepare("SELECT id, name, subtitle, images FROM recipes ORDER BY created_at DESC");
$stmt->execute();
$res = $stmt->get_result();

$out = [];
while ($r = $res->fetch_assoc()) {
    $r['image'] = explode(',', $r['images'])[0] ?? null;
    unset($r['images']);
    $out[] = $r;
}
echo json_encode($out, JSON_UNESCAPED_UNICODE);
