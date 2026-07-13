// จุดตั้งค่ากลางของ Base URL สำหรับ Node.js API
// ต้องโหลดไฟล์นี้ก่อน dept-api.js / dept-context.js / dept-banner.js / general-content.js เสมอ
// เวลา deploy ขึ้นโดเมนจริง: ถ้า Node ถูก reverse-proxy ไว้ที่ /api บนโดเมนเดียวกัน ไม่ต้องแก้อะไรเลย (auto-detect)
// ถ้า Node อยู่คนละโฮสต์/พอร์ต ให้แก้ที่ PROD_API_BASE บรรทัดเดียวด้านล่างนี้ที่เดียว
(function () {
    const PROD_API_BASE = null; // เช่น 'https://api.example.com/api' — ใส่ค่าถ้า Node ไม่ได้อยู่ใต้ /api ของโดเมนเดียวกัน
    const isLocal = ['localhost', '127.0.0.1'].includes(location.hostname);

    window.API_BASE = isLocal
        ? 'http://localhost:3000/api'
        : (PROD_API_BASE || `${location.protocol}//${location.hostname}/api`);
})();
