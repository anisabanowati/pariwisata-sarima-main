<?php
session_start();
include('includes/checklogin.php');
check_login();

// Secure database connection with timeout
try {
    $dbh = new PDO('mysql:host=localhost;dbname=pradict-visitor-wisata', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

// Check admin status and get user info
$uid = $_SESSION['odmsaid'];
$stmt = $dbh->prepare("SELECT AdminName, MobileNumber AS SiteName FROM tbladmin WHERE ID = ? LIMIT 1");
$stmt->execute([$uid]);
$user = $stmt->fetch();
$defaultSite = $user ? $user['SiteName'] : '';

// Fetch tourism sites and years with error handling
try {
    $wisataList = $dbh->query("SELECT DISTINCT NamaWisata FROM tourism_data ORDER BY NamaWisata")->fetchAll(PDO::FETCH_COLUMN);
    $tahunList = $dbh->query("SELECT DISTINCT YEAR(Tanggal) AS tahun FROM tourism_data ORDER BY tahun DESC")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Data fetch failed: ' . $e->getMessage()]));
}

// Sanitize input data
$filters = [
    'nama_wisata' => filter_var($_POST['nama_wisata'] ?? '', FILTER_SANITIZE_STRING),
    'start_year' => filter_var($_POST['start_year'] ?? '', FILTER_VALIDATE_INT),
    'end_year' => filter_var($_POST['end_year'] ?? '', FILTER_VALIDATE_INT)
];

/**
 * Enhanced Python script execution with better error handling
 */
function run_sarima_prediction($data)
{
    $script_path = __DIR__ . '/python/sarima_predict.py';
    $debug_log = __DIR__ . '/python_debug.log';

    // Validate Python script
    if (!file_exists($script_path)) {
        file_put_contents($debug_log, "Python script missing at: $script_path\n", FILE_APPEND);
        return ['status' => 'error', 'message' => 'Prediction engine unavailable'];
    }

    // Create secure temp file
    $temp_file = tempnam(sys_get_temp_dir(), 'sarima_');
    if (!file_put_contents($temp_file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK))) {
        return ['status' => 'error', 'message' => 'Failed to prepare data'];
    }

    // Build OS-appropriate command
    $python = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'python' : 'python3');
    $command = escapeshellcmd($python) . ' ' . escapeshellarg($script_path) . ' ' . escapeshellarg($temp_file) . ' 2>&1';

    // Execute and capture output
    $output = shell_exec($command);
    unlink($temp_file);

    // Enhanced JSON parsing
    $json_start = strpos($output, '{');
    $json_end = strrpos($output, '}');

    if ($json_start !== false && $json_end !== false) {
        $json = substr($output, $json_start, $json_end - $json_start + 1);
        $result = json_decode($json, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }
    }

    // Log full error if debug needed
    file_put_contents($debug_log, "CMD: $command\nOUTPUT: $output\n", FILE_APPEND);
    return ['status' => 'error', 'message' => 'Prediction failed', 'technical' => substr($output, 0, 200)];
}

// Process prediction request if filters exist
$prediction = null;
if (!empty($filters['nama_wisata']) && $filters['start_year'] && $filters['end_year']) {
    try {
        // Build dynamic SQL query
        $sql = "SELECT YEAR(Tanggal) AS tahun, MONTH(Tanggal) AS bulan, 
                SUM(JumlahPengunjung) AS total FROM tourism_data 
                WHERE NamaWisata = :nama_wisata 
                AND YEAR(Tanggal) BETWEEN :start_year AND :end_year
                GROUP BY YEAR(Tanggal), MONTH(Tanggal) 
                ORDER BY tahun, bulan";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':nama_wisata' => $filters['nama_wisata'],
            ':start_year' => $filters['start_year'],
            ':end_year' => $filters['end_year']
        ]);
        $historical_data = $stmt->fetchAll();

        if (count($historical_data) < 12) {
            throw new Exception("Minimum 12 months data required for prediction");
        }

        // Prepare data for Python
        $python_data = [
            'historical_data' => array_map(function ($row) {
                return [
                    'tahun' => (int)$row['tahun'],
                    'bulan' => (int)$row['bulan'],
                    'total' => (int)$row['total']
                ];
            }, $historical_data),
            'forecast_periods' => 12,
            'metadata' => [
                'tourism_place' => $filters['nama_wisata'],
                'time_range' => "{$filters['start_year']}-{$filters['end_year']}"
            ]
        ];

        $prediction = run_sarima_prediction($python_data);
    } catch (Exception $e) {
        $prediction = ['status' => 'error', 'message' => $e->getMessage()];
    }
}

// Determine prediction year for display
$lastYear = $filters['end_year'] ?? max($tahunList);
$predYear = $lastYear ? $lastYear + 1 : date('Y') + 1;

// Month names for display
$months = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember"
];
?>

<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php"); ?>

<body>
    <div class="container-scroller">
        <?php @include("includes/header.php"); ?>
        <div class="container-fluid page-body-wrapper">
            <?php @include("includes/sidebar.php"); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-lg-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Prediksi Jumlah Pengunjung</h4>

                                    <!-- Filter Form -->
                                    <form method="post" class="forms-sample">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label>Tempat Wisata</label>
                                                <select name="nama_wisata" class="form-control" required>
                                                    <option value="">Pilih Tempat Wisata</option>
                                                    <?php foreach ($wisataList as $wisata): ?>
                                                        <option value="<?= htmlspecialchars($wisata) ?>"
                                                            <?= ($filters['nama_wisata'] == $wisata) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($wisata) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label>Tahun Awal</label>
                                                <select name="start_year" class="form-control" required>
                                                    <option value="">Pilih Tahun</option>
                                                    <?php foreach ($tahunList as $tahun): ?>
                                                        <option value="<?= $tahun ?>"
                                                            <?= ($filters['start_year'] == $tahun) ? 'selected' : '' ?>>
                                                            <?= $tahun ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label>Tahun Akhir</label>
                                                <select name="end_year" class="form-control" required>
                                                    <option value="">Pilih Tahun</option>
                                                    <?php foreach ($tahunList as $tahun): ?>
                                                        <option value="<?= $tahun ?>"
                                                            <?= ($filters['end_year'] == $tahun) ? 'selected' : '' ?>>
                                                            <?= $tahun ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-graph-up"></i> Prediksi
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Results Section -->
                                    <?php if (isset($prediction)): ?>
                                        <?php if ($prediction['status'] == 'success'): ?>
                                            <div class="mt-4">
                                                <div class="card-header bg-primary text-white">
                                                    <h4 class="mb-0">
                                                        <i class="bi bi-bar-chart-line"></i>
                                                        Prediksi SARIMA untuk <?= htmlspecialchars($filters['nama_wisata']) ?> - Tahun <?= $predYear ?>
                                                    </h4>
                                                </div>

                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Bulan</th>
                                                                <th>Prediksi Pengunjung</th>
                                                                <th>Rentang Keyakinan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($prediction['forecast'] as $month => $value): ?>
                                                                <tr>
                                                                    <td><?= $months[$month - 1] ?? $month ?></td>
                                                                    <td><?= number_format($value) ?></td>
                                                                    <td>
                                                                        <?= number_format($prediction['confidence_intervals'][$month][0]) ?> -
                                                                        <?= number_format($prediction['confidence_intervals'][$month][1]) ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            <tr class="table-success">
                                                                <td><strong>Total</strong></td>
                                                                <td colspan="2"><strong><?= number_format(array_sum($prediction['forecast'])) ?></strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="chart-container mt-4" style="height: 400px;">
                                                    <canvas id="forecastChart"></canvas>
                                                </div>

                                                <div class="model-info mt-4">
                                                    <h5><i class="bi bi-gear"></i> Diagnostik Model</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="list-group">
                                                                <li class="list-group-item d-flex justify-content-between">
                                                                    <span>Skor AIC:</span>
                                                                    <span><?= round($prediction['model_stats']['aic'], 2) ?></span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between">
                                                                    <span>Skor BIC:</span>
                                                                    <span><?= round($prediction['model_stats']['bic'], 2) ?></span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="list-group">
                                                                <li class="list-group-item d-flex justify-content-between">
                                                                    <span>MAE:</span>
                                                                    <span><?= round($prediction['model_stats']['mae'], 2) ?></span>
                                                                </li>
                                                                <li class="list-group-item d-flex justify-content-between">
                                                                    <span>Stasioner:</span>
                                                                    <span><?= $prediction['model_stats']['is_stationary'] ? 'Ya' : 'Tidak' ?></span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <h5 class="mt-3">Parameter Model</h5>
                                                    <pre><?= json_encode($prediction['model_stats'], JSON_PRETTY_PRINT) ?></pre>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-danger mt-4">
                                                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Prediksi Gagal</h4>
                                                <p><?= htmlspecialchars($prediction['message']) ?></p>
                                                <?php if (isset($prediction['trace'])): ?>
                                                    <div class="error-trace mt-3">
                                                        <h6>Detail Teknis:</h6>
                                                        <pre><?= htmlspecialchars($prediction['trace']) ?></pre>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info mt-4">
                                            <h4 class="alert-heading"><i class="bi bi-info-circle"></i> Catatan</h4>
                                            <p>Silakan pilih tempat wisata dan rentang tahun untuk melakukan prediksi pengunjung.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php @include("includes/footer.php"); ?>
            </div>
        </div>
    </div>
    <?php @include("includes/foot.php"); ?>

    <?php if (isset($prediction) && $prediction['status'] == 'success'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('forecastChart').getContext('2d');
                const labels = <?= json_encode(array_map(fn($m) => substr($m, 0, 3), $months)) ?>;
                const data = <?= json_encode(array_values($prediction['forecast'])) ?>;
                const ci = <?= json_encode(array_values($prediction['confidence_intervals'])) ?>;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Prediksi Pengunjung',
                                data: data,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Rentang Keyakinan (Atas)',
                                data: ci.map(item => item[1]),
                                borderColor: 'rgba(255, 99, 132, 0.5)',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                borderWidth: 1,
                                borderDash: [5, 5]
                            },
                            {
                                label: 'Rentang Keyakinan (Bawah)',
                                data: ci.map(item => item[0]),
                                borderColor: 'rgba(75, 192, 192, 0.5)',
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                borderWidth: 1,
                                borderDash: [5, 5]
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Jumlah Pengunjung'
                                },
                                ticks: {
                                    callback: value => value.toLocaleString()
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Bulan'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        let label = ctx.dataset.label || '';
                                        if (label) label += ': ';
                                        if (ctx.parsed.y !== null) {
                                            label += ctx.parsed.y.toLocaleString();
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    <?php endif; ?>
</body>

</html>