<?php
session_start();
include "../Dikoneksi.php";

// Ganti dengan sesuai dengan kunci session yang benar
$idPelanggan = $_SESSION['id_lgn'];

$result = array();
$error_message = "";

// Proses pencarian
if(isset($_POST['search'])) {
    $search = $_POST['search'];
    $query = mysqli_query($Koneksi, "SELECT tb_order.*, tb_bayar.*, nama, SUM(harga*jumlah) AS harganya, tb_list_order.status AS status_list_order
                                FROM tb_order 
                                LEFT JOIN user_login ON user_login.id = tb_order.pelayan
                                LEFT JOIN tb_list_order ON tb_list_order.order = tb_order.id_odr
                                LEFT JOIN tb_menu ON tb_menu.idm = tb_list_order.menu
                                LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_odr
                                WHERE tb_order.id_odr = '$search'
                                GROUP BY id_odr ORDER BY waktu_order DESC");

    if(mysqli_num_rows($query) > 0) {
        while ($record = mysqli_fetch_array($query)) {
            $result[] = $record;
        }
    } else {
        $error_message = "pesanan tidak ditemukan atau Kode order salah.";
    }
}

?>

<!-- <meta http-equiv="refresh" content="30"> -->
<div class= "col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Customer
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="search" class="form-label">Search Kode Order</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan Kode Order">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <?php if(isset($_POST['search'])): ?>
            <div class="table-responsive mt-2">
                <table class="table table-hover text-center">
                    <thead>
                        <tr class="text-nowrap">
                            <th scope="col">No.</th>
                            <th scope="col">Kode Order</th>
                            <th scope="col">Waktu Order</th>
                            <th scope="col">Waktu Bayar</th>
                            <th scope="col">Status Pembayaran</th>
                            <th scope="col">Status List Order</th>
                            <th scope="col">Meja</th>
                            <th scope="col">Pelayan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel_pesanan">
                        <?php
                        echo "$error_message";
                        $no = 1;
                        foreach ($result as $row) {
                            
                        ?>
                            <tr class="pesanan-row">
                                <th scope="row"><?php echo $no++ ?></th>
                                <td><?php echo $row['id_odr'] ?></td>
                                <td><?php echo $row['waktu_order'] ?></td>
                                <td><?php echo (!empty($row['waktu_bayar'])) ? $row['waktu_bayar'] : '-'; ?></td>
                                <td><?php echo (!empty($row['id_bayar'])) ? '<span class="badge text-bg-success">Dibayar</span>' : '<span class="badge text-bg-danger">Belum Dibayar</span>'; ?></td>
                                <td><?php
                                        if ($row['status_list_order'] == 1) {
                                            echo "<span class='badge text-bg-warning'>Masuk Ke Dapur</span>";
                                        } elseif ($row['status_list_order'] == 2) {
                                            echo "<span class='badge text-bg-success'>Siap Saji</span>";
                                        } elseif ($row['status_list_order'] == 0) {
                                            echo "<span class='badge text-bg-primary'>Belum Diterima Dapur</span>";
                                        } elseif ($row['status_list_order'] == 3) {
                                            echo "<span class='badge text-bg-danger'>Selesai</span>";
                                        }
                                        ?>
                                </td>
                                <td><?php echo $row['meja'] ?></td>
                                <td><?php echo $row['nama'] ?></td>
                                <td>
                                    <div class="d-flex">
                                        <a class="btn btn-info btn-sm me-1" href="./?x=viewitemplgn&order=<?php echo $row['id_odr']; ?>&meja=<?php echo $row['meja']; ?>&pelanggan=<?php echo $row['pelanggan']; ?>"><img src="../image/eye.svg"></a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>