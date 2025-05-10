<?php
// admin/dashboard.php
session_start();
require_once '../db.php';
require_once '../functions.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'];
}

$stmt = $pdo->query("SELECT claims.*, coupons.name AS coupon_name FROM claims JOIN coupons ON claims.coupon_id = coupons.id ORDER BY claims.created_at DESC");
$claims = $stmt->fetchAll(PDO::FETCH_ASSOC);

$couponStmt = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC");
$coupons = $couponStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 대시보드</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-6xl mx-auto space-y-10">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold">관리자 대시보드</h1>
            <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">로그아웃</a>
        </div>

        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">쿠폰 관리</h2>
            <form method="POST" action="save_coupon.php" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="coupon_name" placeholder="쿠폰명" required class="border px-4 py-2 rounded">
                <input type="text" name="coupon_code" placeholder="쿠폰코드" required class="border px-4 py-2 rounded">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">쿠폰 추가</button>
            </form>

            <table class="min-w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 text-left">쿠폰명</th>
                        <th class="py-2 px-4 text-left">쿠폰코드</th>
                        <th class="py-2 px-4 text-left">링크</th>
                        <th class="py-2 px-4 text-left">바코드</th>
                        <th class="py-2 px-4 text-left">삭제</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $c): ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($c['name']); ?></td>
                        <td class="py-2 px-4 font-mono"><?php echo htmlspecialchars($c['code']); ?></td>
                        <td class="py-2 px-4 text-blue-600 underline">
                            <?php
                                $token = $c['id'] . '_' . substr(md5($c['code']), 0, 8);
                                echo getBaseUrl() . "/burgerpick/index.php?token=" . $token;
                            ?>
                        </td>
                        <td class="py-2 px-4">
                            <img src="../barcode.php?code=<?php echo urlencode($c['code']); ?>" alt="barcode" class="h-8">
                        </td>
                        <td class="py-2 px-4">
                            <form method="POST" action="delete_coupon.php" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                                <input type="hidden" name="coupon_id" value="<?php echo $c['id']; ?>">
                                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">삭제</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded shadow p-6">
            <h2 class="text-xl font-semibold mb-4">쿠폰 요청 목록</h2>
            <table class="min-w-full">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left">쿠폰명</th>
                        <th class="py-3 px-4 text-left">암호(ID)</th>
                        <th class="py-3 px-4 text-left">상태</th>
                        <th class="py-3 px-4 text-left">시간</th>
                        <th class="py-3 px-4 text-left">조치</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($claims as $claim): ?>
                    <tr class="border-b">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($claim['coupon_name']); ?></td>
                        <td class="py-2 px-4 font-mono"><?php echo htmlspecialchars($claim['random_id']); ?></td>
                        <td class="py-2 px-4">
                            <?php echo $claim['status'] === 'approved' ? '승인됨' : ($claim['status'] === 'rejected' ? '거절됨' : '대기 중'); ?>
                        </td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($claim['created_at']); ?></td>
                        <td class="py-2 px-4">
                            <?php if ($claim['status'] === 'pending'): ?>
                            <form method="POST" action="process_coupon.php" class="inline">
                                <input type="hidden" name="claim_id" value="<?php echo $claim['id']; ?>">
                                <button name="action" value="approve" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">승인</button>
                                <button name="action" value="reject" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 ml-2">거절</button>
                            </form>
                            <?php else: ?>
                            <span class="text-gray-500">처리 완료</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
