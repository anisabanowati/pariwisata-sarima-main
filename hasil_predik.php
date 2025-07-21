<?php
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
$stmt = $dbh->prepare("SELECT AdminName, NamaWisata AS SiteName FROM users WHERE ID = ? LIMIT 1");
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

// Check if export request
$export_type = $_GET['export'] ?? '';
if ($export_type && isset($_SESSION['last_prediction_data'])) {
    $prediction = $_SESSION['last_prediction_data'];
    $filters = $_SESSION['last_prediction_filters'];

    if ($export_type === 'pdf') {
        exportToPDF($prediction, $filters);
        exit;
    } elseif ($export_type === 'excel') {
        exportToExcel($prediction, $filters);
        exit;
    }
}

/**
 * Export prediction data to PDF
 */
function exportToPDF($prediction, $filters)
{
    // Include TCPDF library
    require_once('tcpdf/tcpdf.php');

    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('Visitor Prediction System');
    $pdf->SetAuthor('Admin');
    $pdf->SetTitle('Prediksi Pengunjung - ' . $filters['nama_wisata']);
    $pdf->SetSubject('Hasil Prediksi Pengunjung');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'Prediksi Pengunjung', 'Tempat Wisata: ' . $filters['nama_wisata']);

    // Set header and footer fonts
    $pdf->setHeaderFont(array('helvetica', '', 10));
    $pdf->setFooterFont(array('helvetica', '', 8));

    // Set margins
    $pdf->SetMargins(15, 25, 15);
    $pdf->SetHeaderMargin(10);
    $pdf->SetFooterMargin(10);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 25);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'LAPORAN PREDIKSI PENGUNJUNG', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Tempat Wisata: ' . $filters['nama_wisata'], 0, 1);
    $pdf->Cell(0, 10, 'Tahun Prediksi: ' . ($filters['end_year'] + 1), 0, 1);
    $pdf->Cell(0, 10, 'Tanggal Cetak: ' . date('d/m/Y H:i'), 0, 1);
    $pdf->Ln(10);

    // Month names
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

    // Create table header
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(60, 10, 'Bulan', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, 'Prediksi Pengunjung', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, 'Rentang Keyakinan', 1, 1, 'C', 1);

    // Table content
    $pdf->SetFont('helvetica', '', 9);
    $total = 0;

    foreach ($prediction['forecast'] as $month => $value) {
        $monthName = $months[$month - 1] ?? $month;
        $ci = isset($prediction['confidence_intervals'][$month])
            ? number_format($prediction['confidence_intervals'][$month][0]) . ' - ' .
            number_format($prediction['confidence_intervals'][$month][1])
            : 'N/A';

        $pdf->Cell(60, 10, $monthName, 1, 0);
        $pdf->Cell(60, 10, number_format($value), 1, 0, 'R');
        $pdf->Cell(60, 10, $ci, 1, 1, 'R');

        $total += $value;
    }

    // Total row
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 10, 'Total', 1, 0, 'R');
    $pdf->Cell(120, 10, number_format($total), 1, 1, 'R');

    // Model information
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 10, 'Parameter Model: ' . json_encode($prediction['model_stats'], JSON_PRETTY_PRINT));

    // Close and output PDF document
    $pdf->Output('prediksi_pengunjung_' . preg_replace('/[^a-zA-Z0-9]/', '_', $filters['nama_wisata']) . '.pdf', 'D');
}

/**
 * Export prediction data to Excel
 */
function exportToExcel($prediction, $filters)
{
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="prediksi_pengunjung_' . $filters['nama_wisata'] . '.xls"');
    header('Cache-Control: max-age=0');

    $months = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    echo '<table border="1">
        <tr>
            <th colspan="2" style="text-align:center;font-size:16px;background-color:#d9d9d9;">Laporan Prediksi Pengunjung</th>
        </tr>
        <tr>
            <td colspan="2"><strong>Tempat Wisata:</strong> ' . $filters['nama_wisata'] . '</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Tahun Prediksi:</strong> ' . ($filters['end_year'] + 1) . '</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Tanggal Cetak:</strong> ' . date('d/m/Y H:i') . '</td>
        </tr>
        <tr>
            <th style="background-color:#d9d9d9;">Bulan</th>
            <th style="background-color:#d9d9d9;">Prediksi Pengunjung</th>
        </tr>';

    $total = 0;
    foreach ($prediction['forecast'] as $month => $value) {
        $monthName = $months[$month - 1] ?? $month;
        echo '<tr>
            <td>' . $monthName . '</td>
            <td>' . number_format($value) . '</td>
        </tr>';
        $total += $value;
    }

    echo '<tr style="font-weight:bold;">
        <td>Total</td>
        <td>' . number_format($total) . '</td>
    </tr>
    
    </table>';
    exit;
}


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
    $python = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'python' : 'python3';
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

// Function to save predictions to database
function save_predictions_to_db($dbh, $nama_wisata, $predYear, $prediction, $created_by)
{
    try {
        // Begin transaction
        $dbh->beginTransaction();

        // First delete any existing predictions for this wisata and year
        $delete_stmt = $dbh->prepare("DELETE FROM sarima_predictions WHERE nama_wisata = ? AND tahun_prediksi = ?");
        $delete_stmt->execute([$nama_wisata, $predYear]);

        // Prepare insert statement
        $insert_stmt = $dbh->prepare("
            INSERT INTO sarima_predictions 
            (nama_wisata, tahun_prediksi, bulan, prediksi_pengunjung, lower_bound, upper_bound, model_params, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        // Insert each month's prediction
        foreach ($prediction['forecast'] as $month => $value) {
            $insert_stmt->execute([
                $nama_wisata,
                $predYear,
                $month,
                (int)$value,
                isset($prediction['confidence_intervals'][$month][0]) ? (int)$prediction['confidence_intervals'][$month][0] : null,
                isset($prediction['confidence_intervals'][$month][1]) ? (int)$prediction['confidence_intervals'][$month][1] : null,
                json_encode($prediction['model_stats']),
                $created_by
            ]);
        }

        // Commit transaction
        $dbh->commit();
        return true;
    } catch (PDOException $e) {
        $dbh->rollBack();
        error_log("Failed to save predictions: " . $e->getMessage());
        return false;
    }
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

        // If prediction was successful, save to database
        if ($prediction['status'] == 'success') {
            $lastYear = $filters['end_year'] ?? max($tahunList);
            $predYear = $lastYear ? $lastYear + 1 : date('Y') + 1;

            $save_result = save_predictions_to_db($dbh, $filters['nama_wisata'], $predYear, $prediction, $uid);

            if (!$save_result) {
                error_log("Warning: Failed to save predictions to database");
            }

            // Store prediction data in session for export
            $_SESSION['last_prediction_data'] = $prediction;
            $_SESSION['last_prediction_filters'] = $filters;
        }
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

                                                <!-- Export Buttons -->
                                                <div class="text-right mt-3 mb-3">
                                                    <!-- <a href="?export=pdf" class="btn btn-danger mr-2">
                                                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                                                    </a> -->
                                                    <a href="?export=excel" class="btn btn-success">
                                                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                                                    </a>
                                                </div>

                                                <div class="table-responsive mt-3">
                                                    <table class="table table-bordered">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th>Bulan</th>
                                                                <th>Prediksi Pengunjung</th>
                                                                <!-- <th>Rentang Keyakinan</th> -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($prediction['forecast'] as $month => $value): ?>
                                                                <tr>
                                                                    <td><?= $months[$month - 1] ?? $month ?></td>
                                                                    <td><?= number_format($value) ?></td>
                                                                    <!-- <td>
                                                                        <?= isset($prediction['confidence_intervals'][$month]) ?
                                                                            number_format($prediction['confidence_intervals'][$month][0]) . ' - ' .
                                                                            number_format($prediction['confidence_intervals'][$month][1]) :
                                                                            'N/A' ?>
                                                                    </td> -->
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

                                                <div class="alert alert-success mt-3">
                                                    <i class="bi bi-check-circle"></i> Hasil prediksi telah disimpan dalam database.
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-danger mt-4">
                                                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Prediksi Gagal</h4>
                                                <p><?= htmlspecialchars($prediction['message']) ?></p>
                                                <?php if (isset($prediction['technical'])): ?>
                                                    <div class="error-trace mt-3">
                                                        <h6>Detail Teknis:</h6>
                                                        <pre><?= htmlspecialchars($prediction['technical']) ?></pre>
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
                        }]
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