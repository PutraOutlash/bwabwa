<?php
// File: dashboard.php (Menggunakan Layout Global)

// 1. PANGGIL SECURITY CHECK (session_start(), cek role, dan include db)
include 'layout/security_check.php';

// 2. SET JUDUL HALAMAN
$pageTitle = "Dashboard Overview";
$admin_name = $_SESSION['name'] ?? 'Admin';
$admin_email = $_SESSION['email'] ?? 'admin@bloombelly.com';

// ==============================================================================
// 3. KODE PHP: LOGIKA DATA OVERVIEW & GRAFIK
// ==============================================================================

// A. TOTAL USERS, ARTICLES, FORUM
$total_users = number_format($pdo->query("SELECT COUNT(id_users) AS total_users FROM users WHERE role = 'user'")->fetchColumn());
$total_articles = number_format($pdo->query("SELECT COUNT(id_articles) AS total_articles FROM articles WHERE status = 'published'")->fetchColumn());
$total_topics = number_format($pdo->query("SELECT COUNT(id) AS total_topics FROM forum_topics")->fetchColumn());


// --- Data Grafik Pertumbuhan Pengguna (12 bulan terakhir) ---
$stmt_growth = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS registration_month,
        COUNT(id_users) AS user_count
    FROM 
        users
    WHERE 
        role = 'user' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY 
        registration_month
");
$raw_growth_data = $stmt_growth->fetchAll(PDO::FETCH_KEY_PAIR); // ['YYYY-MM' => count]

// Siapkan array 12 bulan terakhir
$labels = [];
$counts = [];
$date = new DateTime('11 months ago');

for ($i = 0; $i < 12; $i++) {
    $month_year_key = $date->format('Y-m');
    $month_label = $date->format('M');

    $count = $raw_growth_data[$month_year_key] ?? 0;

    $labels[] = $month_label;
    $counts[] = (int)$count;

    $date->modify('+1 month');
}

$json_labels = json_encode($labels);
$json_counts = json_encode($counts);


// --- Logika Persentase Pertumbuhan Bulan Ini vs Bulan Lalu ---
$current_month = $pdo->query("SELECT COUNT(id_users) AS count FROM users 
    WHERE role = 'user' AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')")->fetchColumn();

$last_month = $pdo->query("SELECT COUNT(id_users) AS count FROM users 
    WHERE role = 'user' AND DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m')")->fetchColumn();


$user_growth_percent = 0;
if ($last_month > 0) {
    $user_growth_percent = round((($current_month - $last_month) / $last_month) * 100);
} else if ($current_month > 0) {
    $user_growth_percent = 100;
}

$user_growth_icon = ($user_growth_percent >= 0) ? 'fa-arrow-up' : 'fa-arrow-down';
$user_growth_color = ($user_growth_percent >= 0) ? 'text-success' : 'text-danger';

// Placeholder untuk card lain
$article_growth_percent = 5;
$forum_growth_percent = 8;
?>

<?php include 'layout/header_admin.php'; ?>
<style>
    /* MODIFIKASI CSS UNTUK TAMPILAN LEBIH BERSIH DAN ELEGAN */
    :root {
        --primary-pink: #ff8fab;
        --user-color: #0d6efd;
        --article-color: #28a745;
        --forum-color: #6f42c1;

        /* Warna latar belakang samar */
        --bg-user-samar: #e6f0ff;
        --bg-article-samar: #e8f5e9;
        --bg-forum-samar: #f7f2fb;

        /* Warna untuk ikon kecil di sudut */
        --bg-icon-user: #e3f2fd;
        --bg-icon-article: #e8f5e9;
        --bg-icon-forum: #f3e5f5;

        /* Warna highlight event (Merah muda tua) */
        --event-highlight: #ff6699;
    }

    /* Styling Kartu Dashboard */
    .dashboard-card {
        border-radius: 15px;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        border: none !important;
        position: relative;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .card-title {
        color: #6c757d !important;
        font-size: 0.9rem;
    }

    /* Ikon Kecil di Samping Judul */
    .icon-container {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        float: right;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* Angka Utama */
    .display-5 {
        font-size: 2.8rem;
        font-weight: 800;
        color: #343a40;
    }

    /* Styling untuk Chart Card */
    .chart-card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }

    /* Styling Kalender */
    .calendar-placeholder {
        background-color: #ffffff;
        padding: 15px;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        text-align: center;
    }

    .day-name {
        font-weight: bold;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .day-number {
        padding: 5px 0;
        height: 35px;
        line-height: 25px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    /* Sorotan Hari Ini */
    .is-today {
        background-color: var(--primary-pink);
        color: white;
        border-radius: 50%;
        font-weight: bold;
    }

    /* Sorotan Acara (Ulang Tahun, Natal, dll.) */
    .is-event {
        background-color: #ffe8ee;
        /* Latar belakang event yang sangat samar */
        color: var(--event-highlight);
        /* Warna teks pink tua */
        border: 2px solid var(--event-highlight);
        border-radius: 50%;
        font-weight: 600;
    }

    .empty-day {
        visibility: hidden;
    }

    /* Daftar Acara */
    .event-list {
        list-style: none;
        padding: 0;
        margin-top: 15px;
        border-top: 1px solid #eee;
    }

    .event-list li {
        padding: 8px 0;
        font-size: 0.95rem;
        border-bottom: 1px dotted #f0f0f0;
    }

    .event-list li:last-child {
        border-bottom: none;
    }
</style>

<div class="row mb-4 g-4">
    <div class="col-lg-4 col-md-6">
        <div class="card dashboard-card" style="background-color: var(--bg-user-samar);">
            <div class="card-body">
                <div class="icon-container" style="background-color: var(--bg-icon-user); color: var(--user-color);">
                    <i class="fas fa-users"></i>
                </div>

                <h6 class="card-title text-uppercase mb-2 fw-semibold">Total Users</h6>

                <h1 class="display-5 mb-0 fw-bold"><?php echo $total_users; ?></h1>
                <p class="mt-3 mb-0 text-sm">
                    <span class="<?php echo $user_growth_color; ?> me-2 fw-semibold">
                        <i class="fas <?php echo $user_growth_icon; ?>"></i> <?php echo abs($user_growth_percent); ?>%
                    </span>
                    <span class="text-muted text-nowrap">vs. bulan lalu</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="card dashboard-card" style="background-color: var(--bg-article-samar);">
            <div class="card-body">
                <div class="icon-container" style="background-color: var(--bg-icon-article); color: var(--article-color);">
                    <i class="fas fa-newspaper"></i>
                </div>

                <h6 class="card-title text-uppercase mb-2 fw-semibold">Total Articles</h6>

                <h1 class="display-5 mb-0 fw-bold"><?php echo $total_articles; ?></h1>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-success me-2 fw-semibold"><i class="fas fa-arrow-up"></i> <?php echo $article_growth_percent; ?>%</span>
                    <span class="text-muted text-nowrap">vs. bulan lalu</span>
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12">
        <div class="card dashboard-card" style="background-color: var(--bg-forum-samar);">
            <div class="card-body">
                <div class="icon-container" style="background-color: var(--bg-icon-forum); color: var(--forum-color);">
                    <i class="fas fa-comments"></i>
                </div>

                <h6 class="card-title text-uppercase mb-2 fw-semibold">Total Forum Topics</h6>

                <h1 class="display-5 mb-0 fw-bold"><?php echo $total_topics; ?></h1>
                <p class="mt-3 mb-0 text-sm">
                    <span class="text-success me-2 fw-semibold"><i class="fas fa-arrow-up"></i> <?php echo $forum_growth_percent; ?>%</span>
                    <span class="text-muted text-nowrap">vs. bulan lalu</span>
                </p>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card chart-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="card-title mb-0 fw-semibold"><i class="fas fa-chart-line me-2" style="color: var(--user-color);"></i> Pertumbuhan Pengguna (12 Bulan)</h5>
            </div>
            <div class="card-body pt-0">
                <div style="height: 350px;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-semibold"><i class="fas fa-calendar-alt me-2 text-info"></i> Kalender & Jadwal</h5>

                <div id="calendarContainer" class="calendar-placeholder">
                </div>

                <h6 class="fw-semibold mt-4 mb-2" style="color: var(--primary-pink);"><i class="fas fa-star me-1"></i> Acara Penting</h6>
                <ul class="event-list">
                    <li><span class="badge bg-danger me-2">18 Dec</span> <i class="fas fa-birthday-cake me-1"></i> Ulang Tahun Ibenn</li>
                    <li><span class="badge bg-success me-2">25 Dec</span> <i class="fas fa-gift me-1"></i> Hari Natal</li>
                </ul>

            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. INISIALISASI GRAFIK CHART.JS ---
    const chartLabels = <?php echo $json_labels; ?>;
    const chartCounts = <?php echo $json_counts; ?>;

    if (typeof Chart !== 'undefined' && document.getElementById('userGrowthChart')) {
        const ctx = document.getElementById('userGrowthChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Pengguna Baru',
                    data: chartCounts,
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderColor: '#0d6efd',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItem) {
                                const fullDate = new Date();
                                fullDate.setMonth(fullDate.getMonth() - (12 - tooltipItem[0].dataIndex) + 1);
                                return fullDate.toLocaleString('id-ID', {
                                    month: 'long',
                                    year: 'numeric'
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Pengguna',
                            color: '#495057'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }


    // --- 2. INISIALISASI KALENDER SEDERHANA (DENGAN SOROTAN ACARA) ---

    function renderSimpleCalendar() {
        const today = new Date();
        const currentMonth = today.getMonth(); // 0-11
        const currentYear = today.getFullYear();
        const todayDate = today.getDate();

        // DAFTAR ACARA UNTUK BULAN INI (Desember = 11)
        const events = {};
        if (currentMonth === 11) { // Hanya jika bulan saat ini adalah Desember (11)
            events[18] = 'birthday'; // Ulang Tahun Ibenn
            events[25] = 'christmas'; // Natal
        }

        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

        const firstDay = (new Date(currentYear, currentMonth, 1)).getDay();
        const daysInMonth = (new Date(currentYear, currentMonth + 1, 0)).getDate();

        let calendarHTML = `
            <h6 class="text-center fw-bold mb-3" style="color: var(--primary-pink);">${monthNames[currentMonth]} ${currentYear}</h6>
            <div class="calendar-grid mb-2">
        `;

        // Header Nama Hari
        dayNames.forEach(day => {
            calendarHTML += `<span class="day-name">${day}</span>`;
        });

        // Spasi kosong untuk hari-hari sebelum tanggal 1
        for (let i = 0; i < firstDay; i++) {
            calendarHTML += `<span class="day-number empty-day">0</span>`;
        }

        // Tanggal
        for (let day = 1; day <= daysInMonth; day++) {
            let todayClass = (day === todayDate) ? 'is-today' : '';
            let eventClass = (events[day]) ? 'is-event' : '';

            // Logika: Jika hari ini adalah Hari Ini, utamakan highlight Hari Ini.
            if (todayClass) {
                eventClass = '';
            }

            calendarHTML += `
                <span class="day-number ${todayClass} ${eventClass}">
                    ${day}
                </span>
            `;
        }

        calendarHTML += `</div>`;
        // CATATAN DIHAPUS: HANYA MENYISAKAN DAFTAR EVENT DI BAWAH
        // calendarHTML += `<p class="small text-muted mt-3 text-center">**Catatan** Tanggal hari ini disorot.</p>`;

        // Tampilkan di container
        document.getElementById('calendarContainer').innerHTML = calendarHTML;
    }

    // Panggil fungsi kalender saat halaman dimuat
    if (document.getElementById('calendarContainer')) {
        renderSimpleCalendar();
    }
</script>

<?php include 'layout/footer_admin.php'; ?>