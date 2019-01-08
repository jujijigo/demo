<?php
require '../common/database/connect.php';
$stmt = $pdo->query('SELECT id FROM ad_msg');
$total = $stmt->rowCount();
$limit = 8;
$totalPages = ceil($total / $limit);
if (isset($_GET['page'])) {
    $currentPage = $_GET['page'];
    if ($currentPage < 1) {
        $currentPage = 1;
    } elseif ($currentPage > $totalPages) {
        $currentPage = $totalPages;
    } else {
        $currentPage = intval($_GET['page']);
    }
} else {
    $currentPage = 1;
}
$offset = ($currentPage - 1) * $limit;
$sql = "select msg.id as id,content,msg.created_at,username from ad_msg as msg,users WHERE user_id=users.id ORDER BY created_at DESC LIMIT $offset,$limit";
$result = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
for ($i = $currentPage - 2; $i <= $currentPage + 2; $i++) {
    if ($i >= 1 && $i <= $totalPages)
        $showPages[] = $i;
}
$prePage = $currentPage == 1 ? $currentPage : $currentPage - 1;
$nextPage = $currentPage >= $totalPages ? $currentPage : $currentPage + 1;
