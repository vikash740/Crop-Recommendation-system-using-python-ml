<?php
require_once "db.php";
$pdo = getPDO();
$stmt = $pdo->query("SELECT predicted_crop, COUNT(*) as c FROM predictions GROUP BY predicted_crop ORDER BY c DESC");
$labels = []; $counts = [];
foreach($stmt->fetchAll() as $row){ $labels[] = $row['predicted_crop']; $counts[] = (int)$row['c']; }
$stmt = $pdo->query("SELECT DATE(created_at) as d, COUNT(*) as c FROM predictions GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC");
$times = []; $time_counts = [];
foreach($stmt->fetchAll() as $row){ $times[] = $row['d']; $time_counts[] = (int)$row['c']; }
header('Content-Type: application/json');
echo json_encode(['labels'=>$labels,'counts'=>$counts,'times'=>$times,'time_counts'=>$time_counts]);
?>