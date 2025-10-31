<?php
include "config.php";

$limit = intval($_GET['limit'] ?? 10);
$page = max(1, intval($_GET['page'] ?? 1));
$q = $mysqli->real_escape_string($_GET['q'] ?? '');
$category = $mysqli->real_escape_string($_GET['category'] ?? '');
$offset = ($page - 1) * $limit;

$where = "WHERE status IN ('upcoming','ongoing')";
if ($q !== '') {
    $where .= " AND (name LIKE '%$q%' OR description LIKE '%$q%')";
}
if ($category !== '') {
    $where .= " AND category = '$category'";
}

$totalRes = $mysqli->query("SELECT COUNT(*) as cnt FROM events $where");
$total = $totalRes ? intval($totalRes->fetch_assoc()['cnt']) : 0;

$sql = "SELECT * FROM events $where ORDER BY start_date ASC LIMIT $limit OFFSET $offset";
$res = $mysqli->query($sql);

$events = [];
while ($row = $res->fetch_assoc()) {
    $events[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'events' => $events
]);
exit();
?>