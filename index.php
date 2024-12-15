<?php
session_start();
// Cek apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    // Pengguna belum login, redirect ke halaman login
    header('Location: ../login');
    exit;
}
include '../Dikoneksi.php';

?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pemesanan</title>
        <script type="text/javascript" src="https://app.stg.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-m2n6kBqd8rsKrRST"></script>
        <link rel="icon" href="..//assets/images/pngkey.com-food-network-logo-png-430444.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    </head>
    

<body>
    <!-- header -->
    <?php
        include "header.php";
    ?>
    <!-- stop header -->
    <div class="container-lg" style="max-width: 1300px;">
        <div class="row width-2 mb-5">
        <!-- sidebar -->
        <?php
            include "sidebar.php";
        ?>
        <!-- stop sidebar -->

            <!-- conten -->
            <?php
            if(isset($_GET['x']) && $_GET['x']=='Dashboard'){
                include "Dashboard.php";
            }elseif (isset($_GET['x']) && $_GET['x'] == 'Order') {
                if ($hasil['level'] == 1 || $hasil['level'] == 2 || $hasil['level'] == 3) {
                    include "Order.php";
                } else {
                    include "Dashboard.php";
                }            
            }elseif(isset($_GET['x']) && $_GET['x']=='Dapur'){
                if ($hasil['level'] == 1 || $hasil['level'] == 4){
                include "Dapur.php";
                }else{
                    include "Dashboard.php";
                }
            }else if(isset($_GET['x']) && $_GET['x']=='User'){
                if ($hasil['level'] == 1){
                include "User.php";
                }else{
                    include "Dashboard.php";
                }
            }else if(isset($_GET['x']) && $_GET['x']=='Report'){
                if ($hasil['level'] == 1 || $hasil['level'] == 2){
                include "Report.php";
                }else{
                    include "Dashboard.php";
                }
            }else if(isset($_GET['x']) && $_GET['x']=='GPenjualan'){
                if ($hasil['level'] == 1 || $hasil['level'] == 2){
                include "GPenjualan.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='Menu'){
                if ($hasil['level'] == 1 || $hasil['level'] == 2 || $hasil['level'] == 3 || $hasil['level'] == 4 || $hasil['level'] == 5){
                include "Menu.php";
                }else{
                    include "Dashboard.php";;
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='login'){
                include "../login.php";
            }elseif(isset($_GET['x']) && $_GET['x']=='Katmenu'){
                if ($hasil['level'] == 1){
                include "katmenu.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='orderitem'){
                if ($hasil['level'] == 1 || $hasil['level'] == 5 || $hasil['level'] == 2 || $hasil['level'] == 3){
                include "order_item.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='viewitem'){
                if ($hasil['level'] == 1 || $hasil['level'] == 5 || $hasil['level'] == 2 || $hasil['level'] == 3){
                include "view_item.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='viewitemplgn'){
                if ($hasil['level'] == 1 || $hasil['level'] == 5 || $hasil['level'] == 2 || $hasil['level'] == 3){
                include "viewitemplgn.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='pelanggan'){
                if ($hasil['level'] == 5 || $hasil['level']==1){
                include "pelanggan.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='pelayan'){
                if ($hasil['level'] == 3 || $hasil['level']==1){
                include "pelayan.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='ODRpelanggan'){
                if ($hasil['level'] == 1 || $hasil['level']==5){
                include "orderpelanggan.php";
                }else{
                    include "Dashboard.php";
                }
            }elseif(isset($_GET['x']) && $_GET['x']=='ppln'){
                if ($hasil['level'] == 1 || $hasil['level']==5){
                include "ppln.php";
                }else{
                    include "Dashboard.php";
                }
            }


        // // Cek apakah parameter 'x' ada pada URL
        // if (isset($_GET['x'])) {
        //     // Dapatkan level pengguna dari hasil query
        //     $userLevel = $hasil['level'];

        //     // Definisikan daftar halaman dan level yang diizinkan
        //     $allowedPages = [
        //         'Dashboard' => [1, 2, 3, 4, 5],
        //         'Order' => [1, 2, 3],
        //         'Dapur' => [1, 4],
        //         'User' => [1],
        //         'Report' => [1],
        //         'Menu' => [1, 3, 4],
        //         'login' => [],  // Halaman login bisa diakses oleh semua orang
        //         'Katmenu' => [1],
        //         'orderitem' => [1, 2, 3, 5],
        //         'viewitem' => [1, 2, 3, 5],
        //         'pelanggan' => [1, 5],
        //         'pelayan' => [1, 3],
        //     ];

        //     // Periksa apakah halaman yang diminta ada di dalam daftar
        //     if (array_key_exists($_GET['x'], $allowedPages)) {
        //         // Periksa apakah level pengguna diizinkan untuk mengakses halaman tersebut
        //         if (in_array($userLevel, $allowedPages[$_GET['x']])) {
        //             // Sertakan halaman yang diminta
        //             include $_GET['x'] . '.php';
        //         } else {
        //             // Jika level pengguna tidak diizinkan, arahkan ke Dashboard
        //             include "Dashboard.php";
        //         }
        //     } else {
        //         // Jika halaman tidak ada di dalam daftar, arahkan ke Dashboard
        //         include "Dashboard.php";
        //     }
        // } else {
        //     // Jika parameter 'x' tidak ada, arahkan ke Dashboard
        //     include "Dashboard.php";
        // }
            ?>
            <!-- stop conten -->
        </div>
    </div>
    <div class="text-center py-2 bg-light">
        @copyright <?php echo date('Y') ?> Segoe doedoe
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script>
        $(document).ready( function () {
            $('#example').DataTable();
        } );
    </script>
</body>
</html>

<style>
    .not_allowed{
        cursor: not-allowed;
    }
</style>
<?php 
    // Perhatikan penulisan query dan pengambilan hasilnya
    $username = $_SESSION['user']; // Ganti 'username' dengan kunci sesi yang benar
    $query = mysqli_query($Koneksi, "SELECT * FROM user_login WHERE username = $username");
    $hasil = mysqli_fetch_array($query);
    
?>
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (() => {
      'use strict';

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      const forms = document.querySelectorAll('.needs-validation');

      // Loop over them and prevent submission
      Array.from(forms).forEach((form) => {
        form.addEventListener('submit', (event) => {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }

          form.classList.add('was-validated');
        }, false);
      });
    })();
</script>
