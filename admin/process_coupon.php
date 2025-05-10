<?php
// admin/process_coupon.php
session_start();
require_once '../db.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claim_id = (int) ($_POST['claim_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($claim_id > 0 && in_array($action, ['approve', 'reject'])) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE claims SET status = 'approved' WHERE id = :id");
            $stmt->execute([':id' => $claim_id]);
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("DELETE FROM claims WHERE id = :id");
            $stmt->execute([':id' => $claim_id]);
        }
    }
}

header('Location: dashboard.php');
exit();
