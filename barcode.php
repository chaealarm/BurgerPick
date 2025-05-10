<?php
// barcode.php - Picqer 방식 복원

require_once __DIR__ . '/vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

// 쿠폰 코드 필수 확인
if (!isset($_GET['code']) || empty($_GET['code'])) {
    http_response_code(400);
    echo '쿠폰 코드가 필요합니다.';
    exit();
}

$code = preg_replace('/[^A-Z0-9]/', '', strtoupper($_GET['code']));

// 출력 버퍼 정리
if (ob_get_length()) ob_end_clean();

// 이미지 출력
header('Content-Type: image/png');

$generator = new BarcodeGeneratorPNG();
echo $generator->getBarcode($code, $generator::TYPE_CODE_128);
exit;