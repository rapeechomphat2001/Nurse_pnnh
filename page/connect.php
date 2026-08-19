<?php
require_once __DIR__ . '/../assets/version.php';
require_once __DIR__ . '/security.php';

// อ่านค่าตั้งค่า DB จากไฟล์ .env ที่ root ของโปรเจกต์ (ไฟล์เดียวกับที่ฝั่ง Node.js ใช้)
// เวลาขึ้น server จริง แค่แก้ค่าใน .env ที่ server ไม่ต้องแก้โค้ดไฟล์นี้
$envPath = __DIR__ . '/../.env';
$env = [];
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $value = trim($value);
        if (strlen($value) >= 2 && (
            ($value[0] === '"' && substr($value, -1) === '"') ||
            ($value[0] === "'" && substr($value, -1) === "'")
        )) {
            $value = substr($value, 1, -1);
        }
        $env[trim($key)] = $value;
    }
}

$host     = $env['DB_HOST'] ?? 'localhost';
$port     = $env['DB_PORT'] ?? '3306';
$username = $env['DB_USER'] ?? 'root';       // ค่าเริ่มต้นของ Laragon
$password = $env['DB_PASSWORD'] ?? '';       // ค่าเริ่มต้นของ Laragon ว่างเปล่า
$dbname   = $env['DB_NAME'] ?? 'db_hospital';

try {
    // เชื่อมต่อฐานข้อมูลด้วย PDO พร้อมตั้งค่าให้รองรับภาษาไทย utf8mb4
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);

    // ตั้งค่าโหมดตรวจจับข้อผิดพลาด (Error Mode)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    // ถ้าเชื่อมต่อไม่ได้ ให้แสดงข้อความแจ้งเตือน
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
}
?>