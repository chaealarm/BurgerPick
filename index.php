<?php
// index.php
require_once 'db.php';
require_once 'functions.php';

$step = 'password';
$error = '';
$success = '';
$coupon = null;
$has_existing_claim = false;
$approved_and_valid = false;

$link_token = $_GET['token'] ?? ($_POST['token'] ?? '');

// 쿠폰 ID 추출
$token_parts = explode('_', $link_token);
$coupon_id = (int)($token_parts[0] ?? 0);

// 유효한 쿠폰인지 확인
$stmt = $pdo->prepare("SELECT * FROM coupons WHERE id = :id");
$stmt->execute([':id' => $coupon_id]);
$coupon_data = $stmt->fetch();

if (!$coupon_data) {
    die('유효하지 않은 쿠폰입니다.');
}

$stmt = $pdo->prepare("SELECT * FROM claims WHERE token = :token");
$stmt->execute([':token' => $link_token]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);
$has_existing_claim = $existing ? true : false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_password = secure_input($_POST['password'] ?? '');

    if (preg_match('/^\d{4}$/', $input_password)) {
        $encrypted = aes_encrypt($input_password);

        if ($existing) {
            if ($existing['password'] === $encrypted) {
                if ($existing['status'] === 'approved') {
                    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE id = :id");
                    $stmt->execute([':id' => $existing['coupon_id']]);
                    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
                    $step = 'coupon';
                } else {
                    $success = '암호가 확인되었습니다. 관리자 승인을 기다려주세요.';
                    $step = 'wait';
                }
            } else {
                $error = '비밀번호가 일치하지 않습니다.';
            }
        } else {
            $random_id = generate_id();
            $stmt = $pdo->prepare("INSERT INTO claims (coupon_id, password, random_id, token, status, created_at) VALUES (:coupon_id, :password, :random_id, :token, 'pending', NOW())");
            $stmt->execute([
                ':coupon_id' => $coupon_id,
                ':password' => $encrypted,
                ':random_id' => $random_id,
                ':token' => $link_token
            ]);
            $success = "랜덤 ID는 <strong>$random_id</strong> 입니다. 이 ID를 커뮤니티에 남겨주세요.";
            $step = 'wait';
        }
    } else {
        $error = '비밀번호는 4자리 숫자여야 합니다.';
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>쿠폰 확인</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-yellow-50 min-h-screen flex items-center justify-center px-4">
    <div class="bg-white shadow-xl rounded-lg p-8 max-w-md w-full">
        <?php if ($step === 'password'): ?>
            <h2 class="text-2xl font-bold mb-2">쿠폰 확인</h2>
            <p class="text-gray-600 mb-1">쿠폰명: <strong><?php echo htmlspecialchars($coupon_data['name']); ?></strong></p>
            <?php if ($error): ?><p class="text-red-500 mb-3"><?php echo $error; ?></p><?php endif; ?>
            <p class="mb-4 text-gray-700">
                <?php if ($has_existing_claim): ?>
                    이미 설정한 비밀번호를 입력해주세요.
                <?php else: ?>
                    처음 비밀번호를 입력하신다면, 원하는 4자리 숫자를 새로 설정해주세요.
                <?php endif; ?>
            </p>
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($link_token); ?>">
                <label class="block mb-4">
                    <span class="text-gray-700">비밀번호 (숫자 4자리)</span>
                    <input type="password" name="password" required class="mt-1 block w-full border px-4 py-2 rounded-md">
                </label>
                <button type="submit" class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600 transition">확인</button>
            </form>
        <?php elseif ($step === 'wait'): ?>
            <h2 class="text-xl font-bold mb-4">등록 완료</h2>
            <p class="text-green-600 mb-2"><?php echo $success; ?></p>
            <p class="text-gray-700">관리자의 승인을 기다려주세요.</p>
        <?php elseif ($step === 'coupon' && $coupon): ?>
            <h2 class="text-2xl font-bold mb-4">쿠폰 확인</h2>
            <p class="mb-2 font-semibold">쿠폰명: <?php echo htmlspecialchars($coupon['name']); ?></p>
            <p class="mb-2 font-mono">쿠폰번호: <?php echo htmlspecialchars($coupon['code']); ?></p>
            <img src="barcode.php?code=<?php echo urlencode($coupon['code']); ?>" alt="Barcode" class="mt-4">
        <?php endif; ?>
    </div>
</body>
</html>
