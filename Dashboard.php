<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT * FROM tb_menu");

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}

$query_chart = mysqli_query($Koneksi, "SELECT nama_menu, tb_menu.idm, SUM(tb_list_order.jumlah) AS total_jumlah FROM tb_menu
                LEFT JOIN tb_list_order ON tb_menu.idm = tb_list_order.menu
                GROUP BY tb_menu.idm
                ORDER BY tb_menu.idm ASC");

$result_chart = array();
while ($record_chart = mysqli_fetch_array($query_chart)) {
    $result_chart[] = $record_chart;
}

$array_menu = array_column($result_chart, 'nama_menu');
$array_menu_quote = array_map(function ($menu) {
    return "'" . $menu . "'";
}, $array_menu);
$string_menu = implode(',', $array_menu_quote);
// echo $string_menu."</br>";

$array_jumlah_pesanan = array_column($result_chart, 'total_jumlah');
$string_jumlah_pesanan = implode(',', $array_jumlah_pesanan);
// echo $string_jumlah_pesanan;
?>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="col-lg-9 mt-2">

<!-- ///////////////////////////// Coursel ////////////////////////////// -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php
            $slide = 0;
            $firstSlideButton = true;
            foreach ($result as $datatombol) {
                ($firstSlideButton) ? $aktif = "active" : $aktif ="";
                $firstSlideButton = false;
            ?>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?php echo $slide ?>" class="<?php echo $aktif ?>" aria-current="true" aria-label="<?php echo $slide + 1 ?>"></button>
            <?php
                $slide++;
            } ?>
        </div>
        <div class="carousel-inner rounded">
            <?php
            $firstSlide = true;
            foreach ($result as $data) {
                ($firstSlide) ? $aktif = "active" : $aktif = "";
                $firstSlide = false;
            ?>
                <div class="carousel-item <?php echo $aktif ?>">
                    <img src="../foto menu/<?php echo $data['foto_menu'] ?>" class="img-fluid" style="height: 250px; width: 1000px; object-fit: cover" alt="...">
                    <div class="carousel-caption d-none d-md-block">
                        <h5><?php echo $data['nama_menu'] ?></h5>
                        <p><?php echo $data['keterangan'] ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
<!--///////////////////////////// Akhir Coursel/////////////////////////-->

<!--///////////////////////////// Judul /////////////////////////-->
    <div class="card mt-4 border-0 bg-light">
        <div class="card-body text-center">
            <h5 class="card-title">WAROENG MODERN BITES - APLIKASI PEMESANAN MAKANAN DAN MINUMAN</h5>
            <p class="card-text">Aplikasi Pemesanan Makanan Dan Minuman Yang Mudah Dan Praktis, 
                Dapat Di Akses Melalui Aplikasi Moblie Maupun Web. 
                Nikmati Beragam Makanan Dan Minuman Favorit Anda Dengan Beberapa Klik. 
                Pesan, Bayar, Dan Lacak Pesanan Anda Dengan Mudah Melalui Aplikasi Ini.</p>
            <a href="Order" class="btn btn-primary">Buat Pesanan</a>
        </div>
    </div>
<!--///////////////////////////// Akhir Judul /////////////////////////-->

<!--/////////////////////////////grafik card /////////////////////////-->
    <div class="card mt-4 border-0 bg-light">
        <div class="card-body text-center">
            <div>
                <canvas id="myChart"></canvas>
            </div>
            <script>
                const ctx = document.getElementById('myChart');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [<?php echo $string_menu ?>],
                        datasets: [{
                        label: 'Jumlah Porsi Terjual',
                        data: [<?php echo $string_jumlah_pesanan ?>],
                        borderWidth: 1,
                        backgroundColor:[
                            'rgba(245, 39, 102, 0.45)',
                            'rgba(245, 39, 102, 0.45)',
                            'rgba(245, 39, 102, 0.45)',
                            'rgba(246, 150, 52, 0.64)',
                            'rgba(246, 150, 52, 0.64)',
                            'rgba(246, 150, 52, 0.64)'
                        ]
                    }]
                    },
                    options: {
                        scales: {
                        y: {
                            beginAtZero: true
                        }
                        }
                    }
                });
            </script>
        </div>
    </div>
<!--///////////////////////////// Akhir grafik card /////////////////////////-->
</div>