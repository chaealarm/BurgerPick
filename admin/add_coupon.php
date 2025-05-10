<?php
// admin/add_coupon.php
session_start();
require_once '../db.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_name = secure_input($_POST['coupon_name'] ?? '');
    $coupon_code = secure_input($_POST['coupon_code'] ?? '');

    if ($coupon_name && $coupon_code) {
        $stmt = $pdo->prepare("INSERT INTO coupons (name, code) VALUES (:name, :code)");
        $stmt->execute([
            ':name' => $coupon_name,
            ':code' => $coupon_code
        ]);
        $success = "쿠폰이 성공적으로 등록되었습니다.";
    } else {
        $error = "모든 필드를 참가해주세요.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 - 쿠퓰 등록</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">쿠퓰 등록</h2>

        <?php if (!empty(\$success)): ?>
            <p class="text-green-500 text-sm mb-4"><?php echo \$success; ?></p>
        <?php elseif (!empty(\$error)): ?>
            <p class="text-red-500 text-sm mb-4"><?php echo \$error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label class="block mb-4">
                <span class="text-gray-700">쿠폰 이름</span>
                <input type="text" name="coupon_name" required class="mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            </label>

            <label class="block mb-6">
                <span class="text-gray-700">쿠폰 번호</span>
                <input type="text" name="coupon_code" required class="mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            </label>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition">등록하기</button>
        </form>
    </div>
</body>
</html>
