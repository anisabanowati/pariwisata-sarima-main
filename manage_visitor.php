<?php
session_start();
include('includes/checklogin.php');
check_login();

/* ===== DETEKSI SUPER‑ADMIN ===== */
$aid      = $_SESSION['odmsaid'] ?? 0;     // ID user login
$isAdmin  = false;
if ($aid) {
  $stmt = $dbh->prepare("SELECT AdminName FROM tbladmin WHERE ID = :aid LIMIT 1");
  $stmt->bindParam(':aid', $aid, PDO::PARAM_INT);
  $stmt->execute();
  $isAdmin = ($stmt->fetchColumn() === 'Admin');
}

/* ===== Input filter ===== */
$nama_wisata = $_POST['nama_wisata'] ?? '';
$from_date   = $_POST['from_date']   ?? '';
$to_date     = $_POST['to_date']     ?? '';

/* ===== Pagination ===== */
$page             = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset           = ($page - 1) * $records_per_page;

/* ===== Scope per‑user ===== */
$scopeCondition = $isAdmin ? '' : 'CreatedBy = :uid';
$scopeParams    = $isAdmin ? [] : [':uid' => $aid];

/* ===== Helper: build WHERE ===== */
$conditions = [];
$params     = [];

if ($scopeCondition) {
  $conditions[] = $scopeCondition;
  $params       = array_merge($params, $scopeParams);
}
if ($nama_wisata !== '') {
  $conditions[]           = "NamaWisata = :nama_wisata";
  $params[':nama_wisata'] = $nama_wisata;
}
if ($from_date !== '' && $to_date !== '') {
  $conditions[]   = "Tanggal BETWEEN :from AND :to";
  $params[':from'] = $from_date;
  $params[':to']   = $to_date;
}
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

/* ===== Hitung total baris ===== */
$countSql = "SELECT COUNT(*) FROM tourism_data $where";
$countStmt = $dbh->prepare($countSql);
foreach ($params as $k => $v) $countStmt->bindValue($k, $v);
$countStmt->execute();
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages   = ceil($totalRecords / $records_per_page);

/* ===== Data page aktif ===== */
$sqlData = "SELECT * FROM tourism_data $where ORDER BY ID DESC LIMIT :lim OFFSET :off";
$stmtData = $dbh->prepare($sqlData);
foreach ($params as $k => $v) $stmtData->bindValue($k, $v);
$stmtData->bindValue(':lim', $records_per_page, PDO::PARAM_INT);
$stmtData->bindValue(':off', $offset, PDO::PARAM_INT);
$stmtData->execute();
$pageData = $stmtData->fetchAll(PDO::FETCH_OBJ);

/* ===== SEMUA data (untuk export) ===== */
$sqlAll = "SELECT * FROM tourism_data $where ORDER BY ID DESC";
$stmtAll = $dbh->prepare($sqlAll);
foreach ($params as $k => $v) $stmtAll->bindValue($k, $v);
$stmtAll->execute();
$allExportData = $stmtAll->fetchAll(PDO::FETCH_OBJ);

/* ===== Data grafik ===== */
$chartSql = "
  SELECT MONTH(Tanggal) AS Bulan, SUM(JumlahPengunjung) AS Total
  FROM tourism_data
  $where
  GROUP BY Bulan ORDER BY Bulan";
$stmtChart = $dbh->prepare($chartSql);
foreach ($params as $k => $v) $stmtChart->bindValue($k, $v);
$stmtChart->execute();
$chartData = [];
foreach ($stmtChart->fetchAll(PDO::FETCH_ASSOC) as $r)
  $chartData[(int)$r['Bulan']] = (int)$r['Total'];
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
                  <h4 class="card-title">Grafik Jumlah Pengunjung</h4>

                  <!-- Filter Nama Wisata -->
                  <form method="post" class="form-inline mb-3" action="?page=1">
                    <label class="mr-2">Nama Wisata:</label>
                    <select name="nama_wisata" class="form-control mr-2">
                      <option value="">Semua</option>
                      <?php
                      $sqlDrop = "SELECT DISTINCT NamaWisata FROM tourism_data";
                      if (!$isAdmin) $sqlDrop .= " WHERE CreatedBy = :uid";
                      $sqlDrop .= " ORDER BY NamaWisata";
                      $stmtDrop = $dbh->prepare($sqlDrop);
                      if (!$isAdmin) $stmtDrop->bindValue(':uid', $aid, PDO::PARAM_INT);
                      $stmtDrop->execute();
                      foreach ($stmtDrop->fetchAll(PDO::FETCH_OBJ) as $w) {
                        $sel = ($nama_wisata == $w->NamaWisata) ? 'selected' : '';
                        echo "<option value='" . htmlentities($w->NamaWisata) . "' $sel>" . htmlentities($w->NamaWisata) . "</option>";
                      } ?>
                    </select>
                    <button type="submit" class="btn btn-primary" name="filter">Tampilkan</button>
                  </form>

                  <canvas id="chartVisitor" height="100"></canvas>

                  <!-- Filter tanggal -->
                  <form method="post" class="form-inline my-3" action="?page=1">
                    <input type="hidden" name="nama_wisata" value="<?= htmlentities($nama_wisata) ?>">
                    <label class="mr-2">Rentang Tanggal:</label>
                    <input type="date" name="from_date" class="form-control mr-2" value="<?= htmlentities($from_date) ?>">
                    <input type="date" name="to_date" class="form-control mr-2" value="<?= htmlentities($to_date) ?>">
                    <button type="submit" class="btn btn-primary" name="filter">Filter</button>
                    <a href="manage_visitor.php" class="btn btn-secondary ml-2">Reset</a>
                  </form>

                  <!-- Export -->
                  <div class="mb-3">
                    <button id="exportPDF" class="btn btn-danger mr-2"><i class="mdi mdi-file-pdf"></i> Export PDF</button>
                    <button id="exportExcel" class="btn btn-success"><i class="mdi mdi-file-excel"></i> Export Excel</button>
                  </div>

                  <p class="text-muted">
                    Menampilkan <?= min($offset + 1, $totalRecords) ?>‑<?= min($offset + $records_per_page, $totalRecords) ?>
                    dari <?= $totalRecords ?> data
                  </p>

                  <!-- TABLE page aktif -->
                  <table class="table table-hover table-bordered" id="dataTableHover">
                    <thead>
                      <tr>
                        <th class="text-center">No</th>
                        <th>Nama Wisata</th>
                        <th>Jumlah Pengunjung</th>
                        <th>Pendapatan</th>
                        <th>Tanggal</th>
                        <th class="text-center">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $no = $offset + 1;
                      foreach ($pageData as $row): ?>
                        <tr>
                          <td class="text-center"><?= $no++ ?></td>
                          <td><?= htmlentities($row->NamaWisata) ?></td>
                          <td class="text-center"><?= htmlentities($row->JumlahPengunjung) ?></td>
                          <td class="text-right">Rp <?= number_format($row->Pendapatan, 0, ',', '.') ?></td>
                          <td class="text-center"><?= date('d-m-Y', strtotime($row->Tanggal)) ?></td>
                          <td class="text-center">
                            <!-- Aksi (detail / edit / delete) -->
                            <a href="#" class="edit_data5" id="<?= $row->ID ?>"><i class="mdi mdi-eye"></i></a>
                            <a href="edit_tourism.php?id=<?= $row->ID ?>" class="ml-2" style="color:orange"><i class="mdi mdi-pencil"></i></a>
                            <a href="delete_tourism.php?id=<?= $row->ID ?>" class="ml-2" style="color:red"
                              onclick="return confirm('Hapus data?');"><i class="mdi mdi-delete"></i></a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                      <?php if (!$pageData): ?>
                        <tr>
                          <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>

                  <!-- ===== TABEL SEMUA DATA (hidden) ===== -->
                  <table id="fullExportTable" style="display:none">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Wisata</th>
                        <th>Jumlah Pengunjung</th>
                        <th>Pendapatan</th>
                        <th>Tanggal</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $n = 1;
                      foreach ($allExportData as $row): ?>
                        <tr>
                          <td><?= $n++ ?></td>
                          <td><?= htmlentities($row->NamaWisata) ?></td>
                          <td><?= htmlentities($row->JumlahPengunjung) ?></td>
                          <td>Rp <?= number_format($row->Pendapatan, 0, ',', '.') ?></td>
                          <td><?= date('d-m-Y', strtotime($row->Tanggal)) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>

                  <br><br>

                  <!-- Pagination -->
                  <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page nav">
                      <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                          <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">«</a></li>
                        <?php endif; ?>
                        <?php $s = max(1, $page - 2);
                        $e = min($totalPages, $page + 2);
                        for ($i = $s; $i <= $e; $i++): ?>
                          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                          </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                          <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">»</a></li>
                        <?php endif; ?>
                      </ul>
                    </nav>
                  <?php endif; ?>

                </div><!-- /.card-body -->
              </div><!-- /.card -->
            </div>
          </div>
        </div><!-- /.content-wrapper -->

        <?php @include("includes/footer.php"); ?>
        <?php @include("includes/foot.php"); ?>
      </div><!-- /.main-panel -->
    </div><!-- /.page-body-wrapper -->
  </div><!-- /.container-scroller -->

  <!-- LIB JS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <script>
    /* === Grafik === */
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const dataFromPHP = <?= json_encode($chartData) ?>;
    const dataValues = monthLabels.map((_, i) => dataFromPHP[i + 1] ?? 0);

    new Chart(document.getElementById('chartVisitor'), {
      type: 'line',
      data: {
        labels: monthLabels,
        datasets: [{
          label: 'Jumlah Pengunjung',
          data: dataValues,
          borderColor: 'rgba(54, 162, 235, 1)', // Warna biru untuk garis
          backgroundColor: 'rgba(0, 0, 0, 0)', // Transparan (tidak ada warna isian)
          borderWidth: 3, // Lebih tebal
          tension: 0, // Garis lurus tanpa kurva
          fill: false, // Tidak mengisi area bawah garis
          pointBackgroundColor: 'rgba(54, 162, 235, 1)', // Warna titik
          pointRadius: 5, // Ukuran titik
          pointHoverRadius: 7 // Ukuran titik saat hover
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              usePointStyle: true,
              padding: 20
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              drawOnChartArea: false // Hilangkan grid y
            }
          },
          x: {
            grid: {
              display: false // Hilangkan grid x
            }
          }
        },
        elements: {
          line: {
            cubicInterpolationMode: 'monotone' // Garis lebih tajam
          }
        }
      }
    });

    /* === Ambil semua data (tabel hidden) === */
    function getFullExportTable() {
      const tbl = document.getElementById('fullExportTable');
      const head = [...tbl.querySelectorAll('thead th')].map(th => th.textContent.trim());
      const body = [...tbl.querySelectorAll('tbody tr')].map(tr => [...tr.querySelectorAll('td')].map(td => td.textContent.trim()));
      return {
        head,
        body
      };
    }

    /* === Export PDF === */
    document.getElementById('exportPDF').onclick = () => {
      const {
        jsPDF
      } = window.jspdf, doc = new jsPDF('l', 'mm', 'a4');
      const {
        head,
        body
      } = getFullExportTable();
      doc.text('Data Wisata (Export Semua)', 14, 15);
      doc.autoTable({
        head: [head],
        body,
        startY: 22,
        theme: 'grid',
        styles: {
          fontSize: 8
        }
      });
      doc.save('data_wisata.pdf');
    };

    /* === Export Excel === */
    document.getElementById('exportExcel').onclick = () => {
      const {
        head,
        body
      } = getFullExportTable();
      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet([head, ...body]), 'Data Wisata');
      XLSX.writeFile(wb, 'data_wisata.xlsx');
    };
  </script>
</body>

</html>