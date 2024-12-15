<?php
session_start();
include "../Dikoneksi.php";


// Ambil data penjualan dari tabel tb_bayar
$queryPenjualanBulanan = mysqli_query($Koneksi, "SELECT MONTH(waktu_bayar) AS bulan, SUM(total_bayar) AS total_penjualan FROM tb_bayar GROUP BY MONTH(waktu_bayar) ORDER BY bulan ASC");
$resultPenjualanBulanan = array();

while ($recordPenjualanBulanan = mysqli_fetch_array($queryPenjualanBulanan)) {
    $resultPenjualanBulanan[] = $recordPenjualanBulanan;
}

$penghasilanBulanan = array_fill(1, 12, 0);

foreach ($resultPenjualanBulanan as $recordPenjualanBulanan) {
    $bulan = $recordPenjualanBulanan['bulan'];
    $totalPenjualan = $recordPenjualanBulanan['total_penjualan'];

    $penghasilanBulanan[$bulan] = $totalPenjualan;
}

// Ambil data penjualan tahunan dari tabel tb_bayar
$queryPenjualanTahunan = mysqli_query($Koneksi, "SELECT YEAR(waktu_bayar) AS tahun, SUM(total_bayar) AS total_penjualan FROM tb_bayar GROUP BY YEAR(waktu_bayar) ORDER BY tahun ASC");

$resultPenjualanTahunan = array();

while ($recordPenjualanTahunan = mysqli_fetch_array($queryPenjualanTahunan)) {
    $resultPenjualanTahunan[] = $recordPenjualanTahunan;
}

$penghasilanTahunan = array_fill(date('Y') - 4, 5, 0);

foreach ($resultPenjualanTahunan as $recordPenjualanTahunan) {
    $tahun = $recordPenjualanTahunan['tahun'];
    $totalPenjualanTahunan = $recordPenjualanTahunan['total_penjualan'];

    $penghasilanTahunan[$tahun] = $totalPenjualanTahunan;
}

// Ubah array penghasilanBulanan dan penghasilanTahunan menjadi string untuk digunakan dalam grafik
$stringPenghasilanBulanan = implode(',', $penghasilanBulanan);
$stringPenghasilanTahunan = implode(',', $penghasilanTahunan);

echo $message;
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Data Penjualan Bulanan
        </div>
        <!--/////////////////////////////grafik card Penjualan Bulanan///////////////////////-->
        <div class="card mt-4 border-0 bg-light">
            <div class="card-body text-center">
                <div>
                    <canvas id="myChartBulanan"></canvas>
                </div>
                <script>
                    const ctxBulanan = document.getElementById('myChartBulanan');

                    new Chart(ctxBulanan, {
                        type: 'bar',
                        data: {
                            labels: [
                                'Januari', 'Februari', 'Maret',
                                'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September',
                                'Oktober', 'November', 'Desember'
                            ],
                            datasets: [{
                                label: 'Penghasilan Bulanan',
                                data: [<?php echo $stringPenghasilanBulanan; ?>],
                                borderWidth: 1,
                                backgroundColor: 'rgba(255, 99, 132, 0.8)' // Warna cerah untuk bulanan
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value, index, values) {
                                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div><br><br>
        <!--///////////////////////////// Akhir grafik card Penjualan Bulanan /////////////////////////-->

        <!--/////////////////////////////grafik card Penjualan Tahunan///////////////////////-->
        <div class="card mt-4 border-0 bg-light">
            <div class="card-header">
                Data Penjualan Tahunan
            </div>
            <div class="card-body text-center">
                <div>
                    <canvas id="myChartTahunan"></canvas>
                </div>
                <script>
                    const ctxTahunan = document.getElementById('myChartTahunan');

                    new Chart(ctxTahunan, {
                        type: 'bar',
                        data: {
                            labels: [
                                <?php
                                $tahunSekarang = date('Y');
                                for ($i = $tahunSekarang - 4; $i <= $tahunSekarang; $i++) {
                                    echo "'$i',";                                
                                }
                                ?>
                            ],
                            datasets: [{
                                label: 'Penghasilan Tahunan',
                                data: [<?php echo $stringPenghasilanTahunan; ?>],
                                borderWidth: 1,
                                backgroundColor: 'rgba(54, 162, 235, 0.8)' // Warna cerah untuk tahunan
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value, index, values) {
                                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>
        <!--///////////////////////////// Akhir grafik card Penjualan Tahunan /////////////////////////-->
    </div>
</div>
