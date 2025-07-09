<h4 class="card-title">Grafik Jumlah Pengunjung</h4>
                    <canvas id="chartVisitor" height="100"></canvas>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('chartVisitor').getContext('2d');

          const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
          const dataFromPHP = <?= json_encode($chartData) ?>;
          const dataValues = monthLabels.map((_, i) => dataFromPHP[i + 1] ?? 0);

          new Chart(ctx, {
            type: 'line',
            data: {
              labels: monthLabels,
              datasets: [{
                label: 'Jumlah Pengunjung',
                data: dataValues,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
              }]
            },
            options: {
              responsive: true,
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });

          $(document).on('click', '.edit_data5', function() {
            var edit_id5 = $(this).attr('id');
            $.ajax({
              url: "view_tourism_details.php",
              type: "post",
              data: {
                edit_id5: edit_id5
              },
              success: function(data) {
                $("#info_update5").html(data);
                $("#editData5").modal('show');
              }
            });
          });
        });
      </script>