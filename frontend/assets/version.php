<?php
// เติม query string ?v=<เวลาที่ไฟล์แก้ไขล่าสุด> ต่อท้าย path ไฟล์ CSS/JS ให้อัตโนมัติ
// เพื่อให้เบราว์เซอร์โหลดไฟล์เวอร์ชันใหม่ทุกครั้งที่ไฟล์ถูกแก้ไข โดยไม่ต้องกดล้างแคชเอง
if (!function_exists('av')) {
    function av($relPath) {
        $full = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . ltrim($relPath, '/');
        $v = @filemtime($full);
        return $relPath . '?v=' . ($v ?: '1');
    }
}
