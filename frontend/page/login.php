<?php
require_once 'connect.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$redirectUrl = trim($_GET['redirect'] ?? 'admin.php');
// ป้องกัน open-redirect
if ($redirectUrl === '' || preg_match('/^(https?:)?\/\//i', $redirectUrl) || strpos($redirectUrl, 'login.php') !== false) {
    $redirectUrl = 'admin.php';
}
$loginError = false;
$loginErrorMsg = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง';

// ถ้า login แล้ว → ไปหน้าที่ตั้งใจเข้าไว้ (เช่น หน้าที่เด้งมา login เพราะต้องล็อกอินก่อน)
if (!empty($_SESSION['is_admin_logged_in'])) {
    header('Location: ' . $redirectUrl);
    exit;
}

// ตรวจรูปแบบ username/password แยกข้อความแจ้งเตือนตามสาเหตุ
// $allowThai: true สำหรับ username (อนุญาตภาษาไทย), false สำหรับ password (อังกฤษ/ตัวเลขเท่านั้น)
function validateLoginField($value, $label, $allowThai = false) {
    if ($value === '') return "กรุณากรอก{$label}";
    if (preg_match('/\s/u', $value)) return "{$label}ห้ามเว้นวรรค";
    $pattern = $allowThai ? '/^[A-Za-z0-9\x{0E01}-\x{0E5B}]+$/u' : '/^[A-Za-z0-9]+$/';
    if (!preg_match($pattern, $value)) return "{$label}ห้ามมีอักขระพิเศษ";
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $usernameError = validateLoginField($username, 'ชื่อผู้ใช้', true);
    $passwordError = validateLoginField($password, 'รหัสผ่าน');

    if ($usernameError || $passwordError) {
        $loginError = true;
        $loginErrorMsg = $usernameError ?: $passwordError;
    } else {
        $passwordHash = hash('sha256', $password);

        // ค้นหาผู้ใช้จากตาราง users
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && hash_equals($user['password_hash'], $passwordHash)) {
            // เก็บข้อมูล session
            $_SESSION['is_admin_logged_in'] = true;
            $_SESSION['user_id']         = (int)$user['id'];
            $_SESSION['username']        = $user['username'];
            $_SESSION['role']            = $user['role'];               // 'main' หรือ 'dept'
            $_SESSION['department_id']   = $user['department_id'] ? (int)$user['department_id'] : null;
            $_SESSION['display_name']    = $user['display_name'] ?: $user['username'];

            header('Location: ' . $redirectUrl);
            exit;
        }

        $loginError = true;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= av('../assets/css/admin.css') ?>">
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="card shadow-sm w-100" style="max-width: 420px;">
            <div class="card-body p-4">
                <h3 class="card-title text-center text-hospital mb-3">เข้าสู่ระบบผู้ดูแล</h3>
                <p class="text-center text-muted mb-4">กรุณาเข้าสู่ระบบเพื่อเข้าใช้งานส่วนจัดการเว็บไซต์</p>

                <?php if ($loginError): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($loginErrorMsg) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php<?= !empty($redirectUrl) ? '?redirect=' . urlencode($redirectUrl) : '' ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                                <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:none">
                                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                    <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.708zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-hospital-orange w-100">เข้าสู่ระบบ</button>
                </form>
                <div class="d-grid mt-3">
                    <a href="index.php" class="btn btn-outline-secondary">กลับไปหน้าหลัก</a>
                </div>
            </div>
        </div>
    </div>
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const eyeOn  = document.getElementById('eyeIcon');
    const eyeOff = document.getElementById('eyeOffIcon');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOn.style.display  = 'none';
        eyeOff.style.display = '';
    } else {
        input.type = 'password';
        eyeOn.style.display  = '';
        eyeOff.style.display = 'none';
    }
});
</script>
</body>
</html>