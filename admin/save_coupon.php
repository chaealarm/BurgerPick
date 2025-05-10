<?php
// admin/save_coupon.php
session_start();
require_once '../db.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = secure_input($_POST['coupon_name'] ?? '');
    $code = secure_input($_POST['coupon_code'] ?? '');

    if ($name && $code) {
        // 중복 방지를 위해 쿠폰코드 중복 여부 체크
        $stmt = $pdo->prepare("SELECT id FROM coupons WHERE code = :code");
        $stmt->execute([':code' => $code]);

        if ($stmt->fetch()) {
            // 이미 존재 → 업데이트 처리 (옵션)
            $update = $pdo->prepare("UPDATE coupons SET name = :name WHERE code = :code");
            $update->execute([':name' => $name, ':code' => $code]);
        } else {
            // 신규 추가
            $insert = $pdo->prepare("INSERT INTO coupons (name, code) VALUES (:name, :code)");
            $insert->execute([':name' => $name, ':code' => $code]);
        }
    }
}

header('Location: dashboard.php');
exit();
