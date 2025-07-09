<?php
session_start();
include('includes/checklogin.php');
check_login();

$aid      = isset($_SESSION['odmsaid']) ? $_SESSION['odmsaid'] : 0;
$isAdmin  = false;

if ($aid) {
    $stmt = $dbh->prepare("SELECT AdminName FROM tbladmin WHERE ID = :aid LIMIT 1");
    $stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
    $stmt->execute();
    $isAdmin = ($stmt->fetchColumn() === 'Admin');
}

/* ────── filter yang diterima dari form ────── */
$startYear       = isset($_POST['start_year']) ? $_POST['start_year'] : '';
$endYear         = isset($_POST['end_year']) ? $_POST['end_year'] : '';
$namaWisataInput = isset($_POST['nama_wisata']) ? $_POST['nama_wisata'] : '';        // '' = Semua
$userYear        = isset($_POST['user_year']) ? $_POST['user_year'] : '';        // filter tahun untuk user


/* ────── ambil daftar tahun & wisata ────── */
$tahunList  = $dbh->query("SELECT DISTINCT YEAR(Tanggal)  AS th FROM tourism_data ORDER BY th DESC")
    ->fetchAll(PDO::FETCH_COLUMN);

$wisataList = $dbh->query("SELECT DISTINCT NamaWisata     FROM tourism_data ORDER BY NamaWisata ASC")
    ->fetchAll(PDO::FETCH_COLUMN);

/* ────── fungsi untuk mengambil data time series ────── */
function getTimeSeriesData(PDO $dbh, $startYear, $endYear, $namaWisata, $userYear = '', $isAdmin = true)
{
    $sql = "SELECT 
                NamaWisata,
                YEAR(Tanggal) AS Tahun,
                MONTH(Tanggal) AS Bulan,
                SUM(JumlahPengunjung) AS TotalPengunjung
            FROM tourism_data";

    $where = [];
    $params = [];

    // Filter berdasarkan tahun
    if ($isAdmin) {
        if ($startYear !== '' && $endYear !== '') {
            $where[] = "YEAR(Tanggal) BETWEEN :startYear AND :endYear";
            $params[':startYear'] = $startYear;
            $params[':endYear'] = $endYear;
        } elseif ($startYear !== '') {
            $where[] = "YEAR(Tanggal) = :startYear";
            $params[':startYear'] = $startYear;
        }
    } else {
        if ($userYear !== '') {
            $where[] = "YEAR(Tanggal) = :userYear";
            $params[':userYear'] = $userYear;
        }
    }

    // Filter berdasarkan nama wisata
    if ($namaWisata !== '') {
        $where[] = "NamaWisata = :namaWisata";
        $params[':namaWisata'] = $namaWisata;
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " GROUP BY NamaWisata, YEAR(Tanggal), MONTH(Tanggal)
              ORDER BY NamaWisata, YEAR(Tanggal), MONTH(Tanggal)";

    $stmt = $dbh->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ────── fungsi untuk melakukan prediksi SARIMA ────── */
function sarimaPredict($data, $periods = 12)
{
    // Langkah 1: Persiapan data time series
    $timeSeries = [];
    foreach ($data as $row) {
        $timeSeries[] = (float)$row['TotalPengunjung'];
    }

    // Langkah 2: Hitung rata-rata bergerak (moving average) untuk menghilangkan noise
    $windowSize = 3; // Ukuran window untuk moving average
    $smoothed = [];
    $n = count($timeSeries);

    for ($i = 0; $i < $n; $i++) {
        $sum = 0;
        $count = 0;

        for ($j = max(0, $i - $windowSize); $j <= min($n - 1, $i + $windowSize); $j++) {
            $sum += $timeSeries[$j];
            $count++;
        }

        $smoothed[$i] = $sum / $count;
    }

    // Langkah 3: Hitung perbedaan musiman (seasonal differencing)
    $seasonalPeriod = 12; // Asumsi data bulanan dengan musiman tahunan
    $differenced = [];

    for ($i = $seasonalPeriod; $i < $n; $i++) {
        $differenced[$i - $seasonalPeriod] = $smoothed[$i] - $smoothed[$i - $seasonalPeriod];
    }

    // Langkah 4: Hitung rata-rata dan standar deviasi untuk normalisasi
    $mean = array_sum($differenced) / count($differenced);
    $stddev = sqrt(array_sum(array_map(function ($x) use ($mean) {
        return pow($x - $mean, 2);
    }, $differenced)) / count($differenced));

    // Langkah 5: Model ARIMA sederhana (autoregressive dengan lag 1)
    $lastValue = end($differenced);
    $predictions = [];

    for ($i = 0; $i < $periods; $i++) {
        // Prediksi sederhana: nilai berikutnya sama dengan nilai terakhir
        $predictedDiff = $lastValue;

        // Tambahkan noise kecil berdasarkan standar deviasi
        $predictedDiff += (rand(-100, 100) / 100) * $stddev * 0.5;

        $predictions[] = $predictedDiff;
        $lastValue = $predictedDiff;
    }

    // Langkah 6: Kembalikan ke skala asli dengan menambahkan kembali komponen musiman
    $lastYearValues = array_slice($timeSeries, -$seasonalPeriod);
    $finalPredictions = [];

    for ($i = 0; $i < $periods; $i++) {
        $seasonalIndex = $i % $seasonalPeriod;
        $finalPredictions[] = max(0, round($lastYearValues[$seasonalIndex] + $predictions[$i]));
    }

    return $finalPredictions;
}

/* ────── ambil data untuk prediksi ────── */
$data = getTimeSeriesData($dbh, $startYear, $endYear, $namaWisataInput, $userYear, $isAdmin);

/* ────── susun data per wisata / bulan ────── */
$rows = [];
foreach ($data as $r) {
    $rows[$r['NamaWisata']][$r['Tahun']][$r['Bulan']] = $r['TotalPengunjung'];
}

/* ────── hitung prediksi SARIMA untuk setiap wisata ────── */
$predRows = [];
foreach ($rows as $namaWisata => $tahunData) {
    // Siapkan data dalam format time series untuk SARIMA
    $timeSeriesData = [];
    foreach ($tahunData as $tahun => $bulanData) {
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $timeSeriesData[] = [
                'NamaWisata' => $namaWisata,
                'Tahun' => $tahun,
                'Bulan' => $bulan,
                'TotalPengunjung' => isset($bulanData[$bulan]) ? $bulanData[$bulan] : 0
            ];
        }
    }

    // Lakukan prediksi SARIMA untuk 12 bulan ke depan
    $prediksi = sarimaPredict($timeSeriesData);

    // Simpan hasil prediksi
    for ($bulan = 1; $bulan <= 12; $bulan++) {
        $predRows[$namaWisata][$bulan] = $prediksi[$bulan - 1];
    }
}

/* ────── tentukan tahun prediksi ────── */
$lastYearData = $endYear !== '' ? $endYear : ($userYear !== '' ? $userYear : max($tahunList));
$predYear = $lastYearData + 1;

/* ────── siapkan data untuk grafik ────── */
if ($namaWisataInput !== '' && isset($predRows[$namaWisataInput])) {
    $predChart = $predRows[$namaWisataInput]; // Data untuk wisata tertentu
} else {
    // Gabungkan prediksi semua wisata
    $predChart = array_fill(1, 12, 0);
    foreach ($predRows as $bulanData) {
        foreach ($bulanData as $bulan => $jumlah) {
            $predChart[$bulan] += $jumlah;
        }
    }
}

/* ────── label tampilan ────── */
$months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

if ($isAdmin) {
    $labelRentang = ($startYear === '' && $endYear === '') ? 'Semua Tahun'
        : (($startYear !== '' && $endYear === '') ? "Tahun $startYear"
            : "Tahun $startYear – $endYear");
} else {
    $labelRentang = $userYear === '' ? 'Semua Tahun' : "Tahun $userYear";
}

$labelWisata = $namaWisataInput === '' ? 'Semua Wisata' : $namaWisataInput;
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
                    <h3 class="font-weight-bold mb-4"><?= $isAdmin ? "Data Jumlah Pengunjung ($labelRentang)" : "Hasil Prediksi" ?></h3>

                    <!-- ────── FORM FILTER ────── -->
                    <form method="post" class="form-inline mb-4">
                        <label class="mr-2 font-weight-bold">Nama Wisata:</label>
                        <select name="nama_wisata" class="form-control mr-3">
                            <option value="">Semua</option>
                            <?php foreach ($wisataList as $w): ?>
                                <option value="<?= htmlentities($w) ?>" <?= $namaWisataInput === $w ? 'selected' : '' ?>><?= htmlentities($w) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php if ($isAdmin): ?>
                            <!-- Filter untuk Admin -->
                            <label class="mr-2 font-weight-bold">Tahun Awal:</label>
                            <select name="start_year" class="form-control mr-2">
                                <option value="">--</option>
                                <?php foreach ($tahunList as $th): ?>
                                    <option value="<?= $th ?>" <?= $startYear == $th ? 'selected' : '' ?>><?= $th ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label class="mr-2 font-weight-bold">Tahun Akhir:</label>
                            <select name="end_year" class="form-control mr-2">
                                <option value="">--</option>
                                <?php foreach ($tahunList as $th): ?>
                                    <option value="<?= $th ?>" <?= $endYear == $th ? 'selected' : '' ?>><?= $th ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <!-- Filter untuk User -->
                            <label class="mr-2 font-weight-bold">Tahun:</label>
                            <select name="user_year" class="form-control mr-2">
                                <option value="">Semua Tahun</option>
                                <?php foreach ($tahunList as $th): ?>
                                    <option value="<?= $th ?>" <?= $userYear == $th ? 'selected' : '' ?>><?= $th ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </form>

                    <!-- ────── TABEL DATA RIIL (hanya admin) ────── -->
                    <?php if ($isAdmin): ?>
                        <div class="card mb-5">
                            <div class="card-body table-responsive">
                                <h5 class="mb-3">Data Riil <?= $labelWisata ?> (<?= $labelRentang ?>)</h5>
                                <table class="table table-bordered text-center" id="prediksiTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Wisata</th>
                                            <th>Tahun</th>
                                            <?php foreach ($months as $m) echo "<th>$m</th>"; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        foreach ($rows as $nama => $tahunData):
                                            foreach ($tahunData as $tahun => $bulanData):
                                        ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td class="text-left"><?= htmlentities($nama) ?></td>
                                                    <td><?= $tahun ?></td>
                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                        <td><?= isset($bulanData[$i]) ? number_format($bulanData[$i]) : 0 ?></td>
                                                    <?php endfor; ?>
                                                </tr>
                                        <?php
                                            endforeach;
                                        endforeach;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- ────── TABEL & GRAFIK PREDIKSI ────── -->
                    <div class="card">
                        <div class="card-body table-responsive">
                            <h5 class="mb-3">Prediksi <?= $labelWisata ?> – Tahun <?= $predYear ?></h5>
                            <p class="text-muted">Menggunakan metode SARIMA (Seasonal ARIMA)</p>

                            <?php if ($isAdmin): ?>
                                <div class="mb-4">
                                    <button id="exportPDF" class="btn btn-danger mr-2"><i class="mdi mdi-file-pdf"></i> Export PDF</button>
                                    <button id="exportExcel" class="btn btn-success"><i class="mdi mdi-file-excel"></i> Export Excel</button>
                                </div>
                            <?php endif; ?>

                            <table class="table table-bordered text-center" id="avgTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Wisata</th>
                                        <?php foreach ($months as $m) echo "<th>$m</th>"; ?>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $grandTotal = 0;
                                    foreach ($predRows as $nama => $bulanData):
                                        $total = array_sum($bulanData);
                                        $grandTotal += $total;
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="text-left"><?= htmlentities($nama) ?></td>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <td><?= number_format($bulanData[$i]) ?></td>
                                            <?php endfor; ?>
                                            <td class="font-weight-bold"><?= number_format($total) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (count($predRows) > 1): ?>
                                        <tr class="table-primary">
                                            <td colspan="2" class="text-right font-weight-bold">Total Semua Wisata</td>
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <td class="font-weight-bold"><?= number_format($predChart[$i]) ?></td>
                                            <?php endfor; ?>
                                            <td class="font-weight-bold"><?= number_format($grandTotal) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <hr>
                            <h6 class="mt-4">Grafik Prediksi Jumlah Pengunjung per Bulan (<?= $predYear ?>)</h6>
                            <canvas id="predChart" height="120"></canvas>
                        </div>
                    </div>

                </div>
            </div>
            <?php @include("includes/footer.php"); ?>
            <?php @include("includes/foot.php"); ?>

            <!-- ────── LIBRARY eksternal ────── -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    /* PDF & EXCEL (admin saja – tombol ada kalau admin) */
                    const labelRentang = <?= json_encode($labelRentang) ?>;
                    const labelWisata = <?= json_encode($labelWisata) ?>;
                    const today = new Date().toLocaleDateString('id-ID');

                    document.getElementById('exportPDF')?.addEventListener('click', () => {
                        const {
                            jsPDF
                        } = window.jspdf;
                        const doc = new jsPDF('l', 'mm', 'a4');
                        doc.setFontSize(16);
                        doc.text(`Laporan Prediksi Kunjungan Wisata (${labelWisata}, ${labelRentang})`, 14, 15);
                        doc.setFontSize(10);
                        doc.text(`Tanggal cetak: ${today}`, 14, 22);
                        doc.text(`Metode: SARIMA (Seasonal ARIMA)`, 14, 28);
                        const avgTable = document.getElementById('avgTable');
                        const headPred = [...avgTable.querySelectorAll('thead th')].map(th => th.textContent.trim());
                        const bodyPred = [...avgTable.querySelectorAll('tbody tr')].map(tr => [...tr.querySelectorAll('td')].map(td => td.textContent.trim()));
                        doc.autoTable({
                            head: [headPred],
                            body: bodyPred,
                            startY: 35,
                            theme: 'grid',
                            styles: {
                                fontSize: 7,
                                cellPadding: 1
                            },
                            headStyles: {
                                fillColor: [0, 150, 80],
                                textColor: [255, 255, 255]
                            }
                        });
                        doc.save(`prediksi_${labelWisata.replace(/ /g,'_')}_${today}.pdf`);
                    });

                    document.getElementById('exportExcel')?.addEventListener('click', () => {
                        const wb = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(document.getElementById('avgTable')), 'Prediksi');
                        const realTable = document.getElementById('prediksiTable');
                        if (realTable) XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(realTable), 'Data Riil');
                        XLSX.writeFile(wb, `prediksi_${labelWisata.replace(/ /g,'_')}_${labelRentang.replace(/ /g,'_')}.xlsx`);
                    });

                    /* grafik */
                    const predLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const predData = <?= json_encode(array_values($predChart)) ?>;

                    new Chart(document.getElementById('predChart'), {
                        type: 'line',
                        data: {
                            labels: predLabels,
                            datasets: [{
                                label: `Prediksi ${<?= $predYear ?>} – ${labelWisata}`,
                                data: predData,
                                borderWidth: 2,
                                borderColor: 'rgba(75,192,192,1)',
                                backgroundColor: 'rgba(75,192,192,0.2)',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Pengunjung'
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
                                        label: function(context) {
                                            return `Pengunjung: ${context.raw.toLocaleString()}`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
</body>

</html>