<?php
include('includes/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php"); ?>

<head>
  <title>Tourism Dashboard - Bantul</title>
  <style>
    .dashboard-card {
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
      margin-bottom: 20px;
      border-left: 4px solid #3c8dbc;
      min-height: 150px;
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card-icon {
      font-size: 2.5rem;
      opacity: 0.7;
    }

    .card-value {
      font-size: 1rem;
      font-weight: bold;
    }

    .card-title {
      color: #6c757d;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .chart-container {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .welcome-banner {
      background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%);
      color: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .recent-activity {
      list-style: none;
      padding-left: 0;
    }

    .recent-activity li {
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }

    .recent-activity li:last-child {
      border-bottom: none;
    }

    .activity-time {
      font-size: 0.8rem;
      color: #6c757d;
    }

    .top-destinations img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 50%;
      margin-right: 15px;
    }

    .destination-item {
      display: flex;
      align-items: center;
      padding: 10px 0;
    }

    .destination-info {
      flex-grow: 1;
    }

    .destination-visitors {
      font-weight: bold;
      color: #3c8dbc;
    }

    /* New styles for simplified trend table */
    .trend-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .trend-table th,
    .trend-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .trend-table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #495057;
    }

    .trend-table tr:hover {
      background-color: #f8f9fa;
    }

    .trend-change {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .trend-up {
      background-color: #d4edda;
      color: #155724;
    }

    .trend-down {
      background-color: #f8d7da;
      color: #721c24;
    }

    .trend-neutral {
      background-color: #e2e3e5;
      color: #383d41;
    }

    .visitor-count {
      font-weight: 600;
    }

    .trend-arrow {
      margin-right: 3px;
    }

    /* Animation for counting numbers */
    .count-up {
      display: inline-block;
    }
  </style>
</head>

<body>
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <?php @include("includes/header.php"); ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.html -->
      <?php @include("includes/sidebar.php"); ?>

      <!-- Main Content -->
      <div class="main-panel">
        <div class="content-wrapper">
          <!-- Welcome Banner -->
          <div class="welcome-banner">
            <div class="row">
              <div class="col-md-8">
                <h3>Selamat Datang di Sistem Informasi Pariwisata Bantul</h3>
                <p class="mb-0">Pantau dan kelola data pariwisata Kabupaten Bantul secara real-time</p>
              </div>
              <div class="col-md-4 text-right">
                <i class="fas fa-map-marked-alt fa-3x" style="opacity: 0.3;"></i>
              </div>
            </div>
          </div>

          <!-- Summary Cards -->
          <div class="row">
            <div class="col-md-3">
              <div class="dashboard-card card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <h6 class="card-title">Total Pengunjung</h6>
                      <div class="card-value">
                        <?php
                        // Query to get total visitors
                        $total_visitors = 0;
                        $sql = "SELECT SUM(JumlahPengunjung) as total FROM tourism_data";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $result = $query->fetch(PDO::FETCH_OBJ);
                        if ($result) {
                          $total_visitors = $result->total;
                          echo '<span class="count-up" data-target="' . $total_visitors . '">0</span>';
                        } else {
                          echo '0';
                        }
                        ?>
                      </div>
                    </div>
                    <div class="col-4 text-right">
                      <i class="fas fa-users card-icon text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="dashboard-card card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <h6 class="card-title">Total Pendapatan</h6>
                      <div class="card-value">
                        <?php
                        // Query to get total income
                        $total_income = 0;
                        $sql = "SELECT SUM(Pendapatan) as total FROM tourism_data";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $result = $query->fetch(PDO::FETCH_OBJ);
                        if ($result) {
                          $total_income = $result->total;
                          echo 'Rp <span class="count-up" data-target="' . $total_income . '">0</span>';
                        } else {
                          echo 'Rp 0';
                        }
                        ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="dashboard-card card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12">
                      <h6 class="card-title">Destinasi Wisata</h6>
                      <div class="card-value">
                        <?php
                        // Query to count distinct destinations
                        $destinations_count = 0;
                        $sql = "SELECT COUNT(DISTINCT NamaWisata) as total FROM tourism_data";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $result = $query->fetch(PDO::FETCH_OBJ);
                        if ($result) {
                          $destinations_count = $result->total;
                          echo '<span class="count-up" data-target="' . $destinations_count . '">0</span>';
                        } else {
                          echo '0';
                        }
                        ?>
                      </div>
                    </div>
                    <div class="col-4 text-right">
                      <i class="fas fa-map-marker-alt card-icon text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-3">
              <div class="dashboard-card card">
                <div class="card-body">
                  <div class="row">
                    <div class="col-12 ">
                      <h6 class="card-title">Data Bulanan</h6>
                      <div class="card-value">
                        <?php
                        // Query to count monthly records
                        $monthly_data = 0;
                        $sql = "SELECT COUNT(*) as total FROM tourism_data";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $result = $query->fetch(PDO::FETCH_OBJ);
                        if ($result) {
                          $monthly_data = $result->total;
                          echo '<span class="count-up" data-target="' . $monthly_data . '">0</span>';
                        } else {
                          echo '0';
                        }
                        ?>
                      </div>
                    </div>
                    <div class="col-4 text-right">
                      <i class="fas fa-calendar-alt card-icon text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Simplified Trend Table -->
          <div class="row">
            <div class="col-md-12">
              <div class="chart-container">
                <h5 class="mb-4">Trend Kunjungan Tahunan</h5>
                <p style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">
                  Data ini menunjukkan total pengunjung per tahun berdasarkan <code>SUM(JumlahPengunjung)</code> dari tabel <strong>tourism_data</strong>.
                  Perubahan dihitung berdasarkan selisih dengan tahun sebelumnya menggunakan fungsi SQL <code>LAG()</code>.
                  <span style="color: #155724; font-weight: bold;">Hijau (↑)</span> menandakan peningkatan,
                  <span style="color: #721c24; font-weight: bold;">Merah (↓)</span> menunjukkan penurunan,
                  dan <span style="color: #383d41; font-weight: bold;">Abu-abu (=)</span> berarti tidak ada perubahan.
                </p>

                <div class="table-responsive">
                  <table class="trend-table">
                    <thead>
                      <tr>
                        <th>Tahun</th>
                        <th>Total Pengunjung</th>
                        <th>Perubahan</th>
                        <th>Persentase</th>
                      </tr>
                    </thead>
                    <tbody>
<?php
$sql = "SELECT YEAR(Tanggal) as year, SUM(JumlahPengunjung) as total 
        FROM tourism_data 
        GROUP BY YEAR(Tanggal) 
        ORDER BY YEAR(Tanggal) ASC";
$query = $dbh->prepare($sql);
$query->execute();
$yearly_data = $query->fetchAll(PDO::FETCH_OBJ);

// Hitung perubahan antar tahun (dari bawah ke atas)
$processed_data = [];
$previous_total = null;

foreach ($yearly_data as $data) {
    $year = $data->year;
    $total = $data->total;
    $change = 0;
    $percentage = 0;
    $trend_class = 'trend-neutral';
    $trend_icon = '';

    if ($previous_total !== null) {
        $change = $total - $previous_total;
        $percentage = ($change / $previous_total) * 100;

        if ($change > 0) {
            $trend_class = 'trend-up';
            $trend_icon = '<i class="fas fa-arrow-up trend-arrow"></i>';
        } elseif ($change < 0) {
            $trend_class = 'trend-down';
            $trend_icon = '<i class="fas fa-arrow-down trend-arrow"></i>';
        }
    }

    $processed_data[] = [
        'year' => $year,
        'total' => $total,
        'change' => $change,
        'percentage' => $percentage,
        'trend_class' => $trend_class,
        'trend_icon' => $trend_icon
    ];

    $previous_total = $total;
}

// Balik data agar tahun terbaru tampil di atas
$processed_data = array_reverse($processed_data);

foreach ($processed_data as $item) {
    echo '<tr>
            <td>' . htmlspecialchars($item['year']) . '</td>
            <td class="visitor-count">' . number_format($item['total']) . '</td>
            <td><span class="trend-change ' . $item['trend_class'] . '">' . $item['trend_icon'] . number_format(abs($item['change'])) . '</span></td>
            <td><span class="trend-change ' . $item['trend_class'] . '">' . $item['trend_icon'] . round(abs($item['percentage']), 1) . '%</span></td>
          </tr>';
}
?>
</tbody>


                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Second Row -->
          <div class="row">
            <div class="col-md-6">
              <div class="chart-container">
                <h5 class="mb-4">Top 5 Destinasi Wisata</h5>
                <div class="top-destinations">
                  <?php
                  // Query to get top 5 destinations by visitors
                  $sql = "SELECT NamaWisata, SUM(JumlahPengunjung) as total 
                          FROM tourism_data 
                          GROUP BY NamaWisata 
                          ORDER BY total DESC 
                          LIMIT 5";
                  $query = $dbh->prepare($sql);
                  $query->execute();
                  $destinations = $query->fetchAll(PDO::FETCH_OBJ);

                  foreach ($destinations as $destination) {
                    $image = strtolower(str_replace(' ', '-', $destination->NamaWisata)) . '.jpg';
                    echo '<div class="destination-item">
                            <div class="destination-info">
                                <h6>' . $destination->NamaWisata . '</h6>
                                <small class="text-muted">Kabupaten Bantul</small>
                            </div>
                            <div class="destination-visitors"><span class="count-up" data-target="' . $destination->total . '">0</span></div>
                          </div>';
                  }
                  ?>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="chart-container">
                <h5 class="mb-4">Aktivitas Terkini</h5>
                <ul class="recent-activity">
                  <?php
                  // Query to get recent activities
                  $sql = "SELECT NamaWisata, JumlahPengunjung, Tanggal 
                          FROM tourism_data 
                          ORDER BY Tanggal DESC 
                          LIMIT 5";
                  $query = $dbh->prepare($sql);
                  $query->execute();
                  $activities = $query->fetchAll(PDO::FETCH_OBJ);

                  foreach ($activities as $activity) {
                    $date = date('d M Y', strtotime($activity->Tanggal));
                    echo '<li>
                            <div class="d-flex justify-content-between">
                                <strong>' . $activity->NamaWisata . '</strong>
                                <span class="destination-visitors"><span class="count-up" data-target="' . $activity->JumlahPengunjung . '">0</span></span>
                            </div>
                            <div class="activity-time">' . $date . '</div>
                          </li>';
                  }
                  ?>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <?php @include("includes/footer.php"); ?>
      </div>
    </div>
  </div>

  <?php @include("includes/foot.php"); ?>

  <script>
    // CountUp Animation Function
    function animateCountUp() {
      const countUpElements = document.querySelectorAll('.count-up');

      countUpElements.forEach(element => {
        const target = parseInt(element.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const start = 0;
        const increment = target / (duration / 16); // 60fps

        let current = start;
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            clearInterval(timer);
            current = target;
          }

          // Format number with thousand separators
          if (element.parentElement.textContent.includes('Rp')) {
            // For currency
            element.textContent = Math.floor(current).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
          } else if (element.parentElement.classList.contains('trend-change') && element.textContent.includes('%')) {
            // For percentages (divided by 10 because we multiplied by 10 earlier)
            element.textContent = (Math.floor(current) / 10).toFixed(1);
          } else {
            // For regular numbers
            element.textContent = Math.floor(current).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
          }
        }, 16);
      });
    }

    // Initialize animation when page loads
    document.addEventListener('DOMContentLoaded', animateCountUp);
  </script>
</body>

</html>