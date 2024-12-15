<!-- 
            <div class="col-lg-3">
                <nav class="navbar navbar-expand-lg bg-body-tertiary rounded border mt-2">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" style="width:240px">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MAIN NAVIGATION</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                                <ul class="navbar-nav nav-pills flex-column justify-content-end flex-grow-1">
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Dashboard') ? 'active link-light text-light' : 'link-dark'; ?>" aria-current="page" href="Dashboard"><i class="bi bi-house-door"></i> Dashboard</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Menu') ? 'active link-light text-light' : 'link-dark'; ?>" href="Menu"><i class="bi bi-menu-button-wide"></i> Daftar Menu</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Katmenu') ? 'active link-light text-light' : 'link-dark'; ?>" href="Katmenu"><i class="bi bi-tags"></i> Kategori Menu</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Order') ? 'active link-light text-light' : 'link-dark'; ?>" href="Order"><i class="bi bi-cart4"></i> Order</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Dapur') ? 'active link-light text-light' : 'link-dark'; ?>" href="Dapur"><i class="bi bi-fire"></i> Dapur</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='User') ? 'active link-light text-light' : 'link-dark'; ?>" href="User"><i class="bi bi-person-fill"></i> User</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Report') ? 'active link-light text-light' : 'link-dark'; ?>" href="Report"><i class="bi bi-file-earmark-bar-graph"></i> Report</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='pelanggan') ? 'active link-light text-light' : 'link-dark'; ?>" href="pelanggan"><i class="bi bi-person-badge"></i></i> Customer</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='pelayan') ? 'active link-light text-light' : 'link-dark'; ?>" href="pelayan"><i class="bi bi-person-badge-fill"></i></i> Pelayan</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div> -->


<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    // Pengguna belum login, redirect ke halaman login
    header('Location: ../login');
    exit;
}

include '../Dikoneksi.php';

// Perhatikan penulisan query dan pengambilan hasilnya
$username = $_SESSION['user']; // Ganti 'username' dengan kunci sesi yang benar
$query = mysqli_query($Koneksi, "SELECT * FROM user_login WHERE username = '$username'");
$hasil = mysqli_fetch_array($query);

?>

<div class="col-lg-3">
    <nav class="navbar navbar-expand-lg bg-body-tertiary rounded border mt-2">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel" style="width:240px">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">MAIN NAVIGATION</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav nav-pills flex-column justify-content-end flex-grow-1">
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x'] == 'Dashboard') ? 'active link-light text-light' : 'link-dark'; ?>" aria-current="page" href="Dashboard"><i class="bi bi-house-door"></i> Dashboard</a>
                        </li>

                        <?php if ($hasil['level'] == 1 || $hasil['level'] == 2 || $hasil['level'] == 3 || $hasil['level'] == 4 || $hasil['level'] == 5){ ?>
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Menu') ? 'active link-light text-light' : 'link-dark'; ?>" href="Menu"><i class="bi bi-menu-button-wide"></i> Daftar Menu</a>
                        </li>
                        <?php } ?>

                        <?php if ($hasil['level'] == 1){ ?>
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Katmenu') ? 'active link-light text-light' : 'link-dark'; ?>" href="Katmenu"><i class="bi bi-tags"></i> Kategori Menu</a>
                        </li>
                        <?php } ?>

                        <?php if ($hasil['level'] == 1 || $hasil['level'] == 2 || $hasil['level'] == 3){ ?>
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Order') ? 'active link-light text-light' : 'link-dark'; ?>" href="Order"><i class="bi bi-cart4"></i> Order</a>
                        </li>
                        <?php } ?>

                        <?php if ($hasil['level'] == 1 || $hasil['level'] == 4){ ?>
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Dapur') ? 'active link-light text-light' : 'link-dark'; ?>" href="Dapur"><i class="bi bi-fire"></i> Dapur</a>
                        </li>
                        <?php } ?>

                        <?php if ($hasil['level'] == 1){ ?>
                        <li class="nav-item">
                                        <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='User') ? 'active link-light text-light' : 'link-dark'; ?>" href="User"><i class="bi bi-person-fill"></i> User</a>
                        </li>
                        <?php } if ($hasil['level'] == 1 || $hasil['level'] == 2){ ?>
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='Report') ? 'active link-light text-light' : 'link-dark'; ?>" href="Report"><i class="bi bi-file-earmark-bar-graph"></i> Report</a>
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='GPenjualan') ? 'active link-light text-light' : 'link-dark'; ?>" href="GPenjualan"><i class="bi bi-graph-up-arrow"></i> Grafik Penjualan</a>
                        </li>
                        <?php } ?>

                        <?php if ($hasil['level'] == 1 || $hasil['level'] == 5){ ?>
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='pelanggan') ? 'active link-light text-light' : 'link-dark'; ?>" href="pelanggan"><i class="bi bi-person-badge"></i></i> Customer</a>
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='ODRpelanggan') ? 'active link-light text-light' : 'link-dark'; ?>" href="ODRpelanggan"><i class="bi bi-cart4"></i> Order</a>
                        </li>
                        <?php } ?>
                        <?php if ($hasil['level'] == 1 || $hasil['level'] == 3){ ?>
                        <li class="nav-item">
                            <a class="nav-link ps-2 <?php echo (isset($_GET['x']) && $_GET['x']=='pelayan') ? 'active link-light text-light' : 'link-dark'; ?>" href="pelayan"><i class="bi bi-person-badge-fill"></i></i> Pelayan</a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>
