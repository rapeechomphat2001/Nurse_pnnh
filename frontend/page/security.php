<?php
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

function escape_html($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function safe_url($value) {
    if (!is_scalar($value)) return '';

    $url = trim((string)$value);
    if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url)) return '';
    if (substr($url, 0, 2) === '//' || strpos($url, '\\') !== false) return '';

    $parts = parse_url($url);
    if ($parts === false) return '';

    if (isset($parts['scheme'])) {
        $scheme = strtolower($parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true) || empty($parts['host'])) return '';
    }

    return $url;
}

function safe_upload_url($fileName, $prefix = '../uploads/') {
    if (!is_scalar($fileName)) return '';

    $fileName = trim((string)$fileName);
    if ($fileName === '' || preg_match('/[\x00-\x1F\x7F\/\\\\]/', $fileName)) return '';

    return $prefix . rawurlencode($fileName);
}
