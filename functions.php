<?php
define('ENCRYPTION_KEY', 'YOURSECRETKEY');
define('ENCRYPTION_IV', substr(hash('sha256', ENCRYPTION_KEY), 0, 16)); // 정확히 16바이트로 추출

function aes_encrypt($data) {
    return openssl_encrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

function aes_decrypt($data) {
    return openssl_decrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}


function generate_id() {
    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 5);
}

function secure_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
