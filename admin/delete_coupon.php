<?php
// admin/delete_coupon.php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_id = (int) ($_POST['coupon_id'] ?? 0);

    if ($coupon_id > 0) {
        // 먼저 해당 쿠폰과 연관된 claims 삭제
        $stmt1 = $pdo->prepare("DELETE FROM claims WHERE coupon_id = :coupon_id");
        $stmt1->execute([':coupon_id' => $coupon_id]);

        // 쿠폰 삭제
        $stmt2 = $pdo->prepare("DELETE FROM coupons WHERE id = :id");
        $stmt2->execute([':id' => $coupon_id]);
    }
}

header('Location: dashboard.php');
exit();
