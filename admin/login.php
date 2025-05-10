<?php
// admin/login.php
session_start();
require_once '../db.php';
require_once '../functions.php';

// Hardcoded credentials
const ADMIN_ID = 'Kimchi';
const ADMIN_PW = 'Danmuji';

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = secure_input($_POST['admin_id'] ?? '');
    $pw = secure_input($_POST['admin_pw'] ?? '');

    if ($id === ADMIN_ID && $pw === ADMIN_PW) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "이름 또는 비밀번호가 불일치합니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 로그인</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-yellow-200 to-red-200 min-h-screen flex items-center justify-center">
    <form method="POST" class="bg-white shadow-xl rounded-xl p-8 w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center">부거 관리자 로그인</h2>

        <?php if (!empty($error)): ?>
            <p class="text-red-500 text-sm mb-4"><?php echo $error; ?></p>
        <?php endif; ?>

        <label class="block mb-4">
            <span class="text-gray-700">ID</span>
            <input type="text" name="admin_id" required class="mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </label>

        <label class="block mb-6">
            <span class="text-gray-700">비밀번호</span>
            <input type="password" name="admin_pw" required class="mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </label>

        <button type="submit" class="w-full bg-yellow-500 text-white py-2 px-4 rounded-md hover:bg-yellow-600 transition">Login</button>
    </form>
</body>
</html>
