<?php
// =====================================================================
//  กุมารเวช — หน้าหอผู้ป่วย/หน่วยงาน (เวอร์ชันปรับขนาดมีเดียและเมนูตามหน้าเดโมจริง)
//  ดึงข้อมูลจากตาราง department_contents โดยอ้างอิง department_id = 1
// =====================================================================
require_once 'connect.php';

$stmt_depts = $conn->query("SELECT * FROM departments ORDER BY id ASC");
$dept_list = $stmt_depts->fetchAll(PDO::FETCH_ASSOC);
$DEPT_ID = isset($_GET['id']) ? (int)$_GET['id'] : null;

function dateToThaiFull($dateStr) {
    if (empty($dateStr) || $dateStr == '0000-00-00') return 'ไม่ระบุวันที่';
    $time = strtotime($dateStr);
    if (!$time) return htmlspecialchars($dateStr);
    $d = date('j', $time);
    $m = date('n', $time);
    $y = date('Y', $time) + 543;
    $months = ["", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
    return "$d {$months[$m]} $y";
}

function parseFileNames($fileData) {
    if (empty($fileData)) return [];
    $decoded = json_decode($fileData, true);
    if (is_array($decoded)) return $decoded;
    if (is_string($fileData) && !empty($fileData)) return [$fileData];
    return [];
}

// ---------- ข้อมูลแผนกนี้ ----------
$stmt = $conn->prepare("SELECT * FROM departments WHERE id = :id");
$stmt->execute([':id' => $DEPT_ID]);
$dept = $stmt->fetch(PDO::FETCH_ASSOC);
if ($DEPT_ID !== null) {
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = :id");
    $stmt->execute([':id' => $DEPT_ID]);
    $dept = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $dept = null;
}

// ---------- Banner ของแผนก ----------
$stmt = $conn->prepare("
    SELECT *
    FROM banners
    WHERE department_id = :dept_id
      AND is_active = 1
    ORDER BY sort_order ASC, id ASC
");

$stmt->execute([
    ':dept_id' => $DEPT_ID
]);

$slides = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- เนื้อหาของแผนกนี้ (จัดกลุ่มตาม section) ----------
if ($DEPT_ID !== null) {
    $stmt = $conn->prepare("SELECT * FROM department_contents WHERE department_id = :id ORDER BY section ASC, sort_order ASC, id ASC");
    $stmt->execute([':id' => $DEPT_ID]);
} else {
    $stmt = $conn->query("SELECT * FROM department_contents WHERE department_id IS NULL ORDER BY section ASC, sort_order ASC, id ASC");
}
$content_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$bySection = [];
foreach ($content_rows as $row) { $bySection[$row['section']][] = $row; }

$item_id = isset($_GET['item']) ? (int)$_GET['item'] : 0;
$detailRow = null;
if ($item_id > 0 && !empty($bySection['meeting_reports'])) {
    foreach ($bySection['meeting_reports'] as $r) {
        if ((int)$r['id'] === $item_id) { $detailRow = $r; break; }
    }
}

$sectionLabels = [
    'structure'       => 'โครงสร้างการบริหารงาน',
    'personnel'       => 'ทำเนียบบุคลากร',
    'service'         => 'การให้บริการต่างๆ',
    'service_profile' => 'Service Profile',
    'indicator'       => 'ตัวชี้วัด',
    'academic'        => 'ผลงานวิจัย',
    'wi'              => 'WI / SP',
    'knowledge'       => 'ข่าวประชาสัมพันธ์ / เกร็ดความรู้',
];

// ---------- เมนูแนวนอน 5 หมวด ----------
$menuGroups = [
    ['label' => 'ข่าวประชาสัมพันธ์ / เกร็ดความรู้',  'icon' => 'bi-lightbulb-fill',         'sections' => ['knowledge']],
    ['label' => 'โครงสร้างการบริหารงาน',           'icon' => 'bi-diagram-3-fill',         'sections' => ['structure', 'personnel', 'service']],
    ['label' => 'Service Profile',                  'icon' => 'bi-clipboard2-pulse-fill',  'sections' => ['service_profile', 'indicator']],
    ['label' => 'ผลงานวิจัย / วิชาการ',             'icon' => 'bi-journal-text',           'sections' => ['academic']],
    ['label' => 'WI, SP',                           'icon' => 'bi-file-earmark-medical-fill','sections' => ['wi']],
];

// ---------- ฟังก์ชันแสดงไฟล์แนบ ----------
function renderAttachments($row) {
    $files = parseFileNames($row['file_name'] ?? '');
    $html  = '';
    foreach ($files as $fname) {
        if (empty($fname) || $fname === 'default.jpg') continue;
        $path = '../uploads/' . $fname;
        $ext  = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        $safe = htmlspecialchars($path);

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $html .= '<div class="text-center">
            <img src="' . $safe . '" class="dc-img lightbox-trigger shadow-sm border" alt="" onerror="this.style.display=\'none\'">
            <small class="text-muted d-block mt-2">คลิกที่รูปภาพเพื่อขยาย</small>
          </div>';
        } elseif ($ext === 'pdf') {
            // PDF: เอาตัวอักษรทับซ้อนและ overlay ออกไปทั้งหมดตามคำสั่ง
            $html .= '<div class="text-center">
            <div class="dc-pdf-wrap pdf-lightbox-trigger shadow-sm" data-src="' . $safe . '">
                <embed src="' . $safe . '" type="application/pdf" class="dc-pdf">
            </div>
            <small class="text-muted d-block mt-2">คลิกที่ไฟล์ PDF เพื่อขยาย</small>
          </div>';
        } elseif (in_array($ext, ['mp4', 'webm', 'ogg'])) {
            $html .= '<video class="dc-video shadow-sm" controls preload="metadata"><source src="' . $safe . '"></video>';
            
        } else {
            $icon = 'bi-file-earmark-arrow-down'; $label = 'ไฟล์เอกสาร';
            if (in_array($ext, ['doc', 'docx'])) { $icon = 'bi-file-earmark-word-fill'; $label = 'ไฟล์ Word'; }
            elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) { $icon = 'bi-file-earmark-excel-fill'; $label = 'ไฟล์ Excel'; }
            elseif (in_array($ext, ['ppt', 'pptx'])) { $icon = 'bi-file-earmark-slides-fill'; $label = 'ไฟล์ PowerPoint'; }
            $html .= '<a href="' . $safe . '" target="_blank" class="dc-file-tile mx-auto">
                        <i class="bi ' . $icon . '"></i>
                        <div class="dc-file-tile-label">' . $label . '<small>คลิกเพื่อเปิด/ดาวน์โหลด</small></div>
                      </a>';
        }
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการประชุม - กลุ่มงานการพยาบาล โรงพยาบาลปากช่องนานา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= av('../assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= av('../assets/css/news_detail.css') ?>">
    <link rel="stylesheet" href="<?= av('../assets/css/all_news.css') ?>">
    <link rel="stylesheet" href="<?= av('../assets/css/department.css') ?>">
</head>
<body>

<div class="top-bar">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div><i class="bi bi-telephone-fill"></i> สายด่วน: 044-316-999 ต่อ 4400 &nbsp;|&nbsp; <i class="bi bi-envelope-fill"></i> nursing@pkc.go.th</div>
        <a href="login.php" class="btn btn-sm btn-outline-light">เข้าสู่ระบบ</a>
    </div>
</div>

<div class="header-banner">
    <div class="container d-flex align-items-center">
        <div class="me-3">
            <img src="../uploads/logo.png" alt="Logo" style="width: 65px; height: 70px; object-fit: contain;">
        </div>
        <div>
            <h2 class="mb-0 fw-bold">กลุ่มงานการพยาบาล <span class="fw-normal opacity-90"><?= $dept ? htmlspecialchars($dept['name']) : '' ?></span></h2>
            <div class="small opacity-90">โรงพยาบาลปากช่องนานา | Nursing Department, Pakchong Nana Hospital</div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-xl main-nav p-0 shadow-sm" id="mainNav">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="เปิดเมนู">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <div class="navbar-nav"><div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-building me-1"></i>เกี่ยวกับกลุ่มงาน
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                                                <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'executives.php?id=' . (int)$DEPT_ID : 'executives.php' ?>"><i class="bi bi-person-badge-fill me-2"></i> ทำเนียบหัวหน้ากลุ่มงาน</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'ward_heads.php?id=' . (int)$DEPT_ID : 'ward_heads.php' ?>"><i class="bi bi-person-lines-fill me-2"></i> ทำเนียบหัวหน้างาน</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'personnel_gallery.php?id=' . (int)$DEPT_ID : 'personnel_gallery.php' ?>"><i class="bi bi-people-fill me-2 "></i>รูปบุคลากร</a></li>
                    </ul>
                </div>

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-briefcase-fill me-1"></i>งานบริหาร
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'org_structure.php?id=' . (int)$DEPT_ID : 'org_structure.php' ?>"><i class="bi bi-diagram-3-fill me-2"></i> โครงสร้างบริหาร</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'risk_management.php?id=' . (int)$DEPT_ID : 'risk_management.php' ?>"><i class="bi bi-shield-exclamation me-2"></i> บริหารความเสี่ยง</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'nursing_ethics.php?id=' . (int)$DEPT_ID : 'nursing_ethics.php' ?>"><i class="bi bi-patch-check-fill me-2"></i> จริยธรรมการพยาบาล</a></li>
                    </ul>
                </div>

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="serviceDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-heart-pulse-fill me-1"></i>งานบริการ</a>
                    <ul class="dropdown-menu" aria-labelledby="serviceDropdown">
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'supervision_results.php?id=' . (int)$DEPT_ID : 'supervision_results_index.php' ?>"><i class="bi bi-clipboard2-check-fill me-2"></i> ผลการนิเทศ</a></li>
                        <li><a class="dropdown-item" href="patient_safety.php<?= $DEPT_ID ? '?id='.(int)$DEPT_ID : '' ?>"><i class="bi bi-shield-check me-2"></i> ความปลอดภัยผู้ป่วย</a></li>
                    </ul>
                </div>
                
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="academicDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-mortarboard-fill me-1"></i>งานวิชาการ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="academicDropdown">
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'dataset.php?id=' . (int)$DEPT_ID : 'dataset.php' ?>"><i class="bi bi-database-fill me-2"></i> Data set</a></li>
                    </ul>
                </div>

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="qualityDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-star-fill me-1"></i>คุณภาพการพยาบาล
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="qualityDropdown">
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'kpi.php?id=' . (int)$DEPT_ID : 'kpi.php' ?>"><i class="bi bi-bar-chart-fill me-2"></i> ตัวชี้วัดคุณภาพ</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'service_profile.php?id=' . (int)$DEPT_ID : 'service_profile.php' ?>"><i class="bi bi-file-earmark-person-fill me-2"></i> Service profile</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'cpg.php?id=' . (int)$DEPT_ID : 'cpg.php' ?>"><i class="bi bi-clipboard2-pulse-fill me-2"></i> CNPG</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'wi.php?id=' . (int)$DEPT_ID : 'wi.php' ?>"><i class="bi bi-file-earmark-text-fill me-2"></i> WI</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'research.php?id=' . (int)$DEPT_ID : 'research.php' ?>"><i class="bi bi-search me-2"></i> วิจัย</a></li>
                    </ul>
                </div>

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="infoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-lightbulb-fill me-1"></i>งานสารสนเทศ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="infoDropdown">
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'staffing.php?id=' . (int)$DEPT_ID : 'staffing.php' ?>"><i class="bi bi-diagram-2-fill me-2"></i> อัตรากำลัง</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'workload.php?id=' . (int)$DEPT_ID : 'workload.php' ?>"><i class="bi bi-speedometer2 me-2"></i> ภาระงาน</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'dashboard.php?id=' . (int)$DEPT_ID : 'dashboard.php' ?>"><i class="bi bi-speedometer2 me-2"></i> Dashbord</a></li>
                    </ul>
                </div>

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="newsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill me-1"></i>ข่าวสารประชาสัมพันธ์
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="newsDropdown">
                        <li><a class="dropdown-item" href="dept_news.php?id=<?= (int)$DEPT_ID ?>"><i class="bi bi-megaphone-fill me-2"></i> ข่าวสารของแผนก</a></li>
                        <li><a class="dropdown-item" href="<?= isset($DEPT_ID) ? 'meeting_reports.php?id=' . (int)$DEPT_ID : 'meeting_reports.php' ?>"><i class="bi bi-journal-text me-2"></i> รายงานการประชุม</a></li>
                    </ul>
                </div>
                <a href="index.php" class="btn-back nav-btn-back ms-auto"><i class="bi bi-arrow-left-circle-fill"></i> กลับหน้าหลัก</a>
            </div>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1><i class="bi bi-journal-text me-2"></i>รายงานการประชุม</h1>
    </div>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <?php if ($detailRow): ?>
                <a href="<?= basename($_SERVER['PHP_SELF']) . ($DEPT_ID !== null ? '?id=' . (int)$DEPT_ID : '') ?>" class="btn-back mb-4 d-inline-flex"><i class="bi bi-arrow-left-circle-fill"></i> กลับไปหน้ารายการ</a>
                <div class="detail-card">
                    <div class="detail-body">
                        <h1 class="detail-title"><?= htmlspecialchars($detailRow['title']) ?></h1>
                        <div class="detail-meta">
                            <span class="badge-date"><i class="bi bi-calendar-event me-1"></i><?= dateToThaiFull($detailRow['created_at']) ?></span>
                        </div>
                        <hr class="detail-divider">
                        <?php if (!empty($detailRow['content'])): ?>
                            <div class="detail-content"><?= nl2br(htmlspecialchars($detailRow['content'])) ?></div>
                        <?php else: ?>
                            <div class="detail-content-empty"><i class="bi bi-newspaper mb-2 d-block" style="font-size:32px;color:#ddd;"></i>ไม่มีรายละเอียดเพิ่มเติม</div>
                        <?php endif; ?>
                        <?php if (!empty($detailRow['link_url'])): ?>
                            <div class="mb-3 mt-3"><a href="<?= htmlspecialchars($detailRow['link_url']) ?>" target="_blank"><i class="bi bi-link-45deg"></i> <?= htmlspecialchars($detailRow['link_url']) ?></a></div>
                        <?php endif; ?>
                        <div class="dc-attachments mt-3"><?= renderAttachments($detailRow) ?></div>
                    </div>
                </div>
            <?php elseif (!empty($bySection['meeting_reports'])): ?>
                <div class="section-title"><i class="bi bi-journal-text me-1"></i> รายการรายงานการประชุม</div>
                <div class="news-list-card">
                    <?php foreach ($bySection['meeting_reports'] as $row): ?>
                        <a href="?<?= $DEPT_ID !== null ? 'id=' . (int)$DEPT_ID . '&' : '' ?>item=<?= (int)$row['id'] ?>" class="news-row">
                            <div class="news-row-left">
                                <i class="bi bi-chevron-right small"></i>
                                <div class="news-row-title"><?= htmlspecialchars($row['title']) ?></div>
                            </div>
                            <div class="news-row-date"><?= dateToThaiFull($row['created_at']) ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
            <div class="empty-state">
        <i class="bi bi-cone-striped"></i>
        <p>หน้า "รายงานการประชุม" อยู่ระหว่างการจัดทำ</p>
        <p class="small">ขออภัยในความไม่สะดวก กรุณากลับมาตรวจสอบใหม่อีกครั้งในภายหลัง</p>
    </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="main-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><i class="bi bi-building"></i> กลุ่มงานการพยาบาล</h5>
                <p class="small opacity-80 mt-2">โรงพยาบาลปากช่องนานา<br>มุ่งมั่นในการพัฒนาคุณภาพการพยาบาล เพื่อผู้ป่วยและผู้รับบริการทุกคน</p>
            </div>
            <div class="col-md-4">
                <h5><i class="bi bi-geo-alt-fill"></i> ติดต่อเรา</h5>
                <ul class="small opacity-80">
                    <li><i class="bi bi-map"></i> 123 ถ.มิตรภาพ อ.ปากช่อง จ.นครราชสีมา 30130</li>
                    <li><i class="bi bi-telephone"></i> 044-316-999 ต่อ 4400</li>
                    <li><i class="bi bi-envelope"></i> nursing@pkc.go.th</li>
                    <li><i class="bi bi-clock"></i> เปิดให้บริการ 24 ชั่วโมง</li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5><i class="bi bi-link-45deg"></i> ลิงก์ที่เกี่ยวข้อง</h5>
                <ul class="small opacity-80">
                    <li><i class="bi bi-chevron-right"></i> <a href="#">กระทรวงสาธารณสุข</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="#">สภาการพยาบาล</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="#">กรมการแพทย์</a></li>
                    <li><i class="bi bi-chevron-right"></i> <a href="#">สรพ. (HA)</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-copyright text-center mt-4">
        <div class="container">
            © 2569 กลุ่มงานการพยาบาล โรงพยาบาลปากช่องนานา — สงวนลิขสิทธิ์ทั้งหมด
        </div>
    </div>
</footer>

<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered p-0" style="background:rgba(0,0,0,0.92);">
        <div class="modal-content border-0" style="background:transparent;">
            <div class="modal-body d-flex align-items-center justify-content-center p-2 position-relative" style="min-height:100vh;">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="font-size:1.4rem; z-index:10;"></button>
                <img id="lightboxImg" src="" alt="" style="max-width:100%; max-height:95vh; object-fit:contain; border-radius:6px; display:none;">
                <embed id="lightboxPdf" src="" type="application/pdf" style="width:96vw; height:94vh; border-radius:6px; display:none;">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= av('../assets/js/api-config.js') ?>"></script>
<script src="<?= av('../assets/js/dept-context.js') ?>"></script>
<script>
(function () {
    const modalEl = document.getElementById('lightboxModal');
    const imgEl   = document.getElementById('lightboxImg');
    const pdfEl   = document.getElementById('lightboxPdf');
    let bsModal   = null;

    function openLightbox(type, src) {
        if (type === 'img') {
            imgEl.src = src; imgEl.style.display = 'block';
            pdfEl.style.display = 'none'; pdfEl.src = '';
        } else if (type === 'pdf') {
            pdfEl.src = src; pdfEl.style.display = 'block';
            imgEl.style.display = 'none'; imgEl.src = '';
        }
        if (!bsModal) bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
    }

    modalEl.addEventListener('hidden.bs.modal', function () {
        imgEl.src = ''; pdfEl.src = '';
    });

    document.addEventListener('click', function (e) {
        const img = e.target.closest('.lightbox-trigger');
        if (img) { e.preventDefault(); openLightbox('img', img.src); return; }
        const pdfBox = e.target.closest('.pdf-lightbox-trigger');
        if (pdfBox) { e.preventDefault(); openLightbox('pdf', pdfBox.dataset.src); return; }
    });
})();
</script>
</body>
</html>
