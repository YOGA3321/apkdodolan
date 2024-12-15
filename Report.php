<?php
session_start();
include "../Dikoneksi.php";


$query = mysqli_query($Koneksi, "SELECT tb_order.*,tb_bayar.*,nama, SUM(harga*jumlah) AS harganya FROM tb_order 
                                    LEFT JOIN user_login ON user_login.id = tb_order.pelayan
                                    LEFT JOIN tb_list_order ON tb_list_order.order = tb_order.id_odr
                                    LEFT JOIN tb_menu ON tb_menu.idm = tb_list_order.menu
                                    JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_odr
                                    GROUP BY id_odr ORDER BY waktu_order ASC");
$result = array();

// $select_kat_menu = mysqli_query($Koneksi, "SELECT idk, kategori_menu FROM tb_kategori_menu");

$koderandom = rand(10000, 99999) . "-";
$target_dir = "../foto menu/" . $koderandom;
$target_file = $target_dir . basename($_FILES['foto']['name']);
$fototype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}
echo $message;
?>

<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Report
        </div>
        <div class="card-body">
            
            <?php
            if (empty($result)) {
                echo "Data Tidak Ditemukan";
            } else {
            ?>
                <div class="table-responsive mt-2">
                    <table class="table table-hover" id="example">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col">No.</th>
                                <th scope="col">Kode Order</th>
                                <th scope="col">Waktu Order</th>
                                <th scope="col">Waktu Bayar</th>
                                <th scope="col">Pelanggan</th>
                                <th scope="col">Meja</th>
                                <th scope="col">Total Harga</th>
                                <th scope="col">Pelayan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $no = 1;
                            foreach ($result as $row) {
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $no++ ?></th>
                                    <td><?php echo $row['id_odr'] ?></td>
                                    <td><?php echo $row['waktu_order'] ?></td>
                                    <td><?php echo $row['waktu_bayar'] ?></td>
                                    <td><?php echo $row['pelanggan'] ?></td></td>
                                    <td><?php echo $row['meja']?></td></td>
                                    <td><?php echo number_format($row['harganya'],0,',','.') ?></td>
                                    <td><?php echo $row['nama'] ?></td>
                                    <td>
                                        <div class="d-flex">
                                            <a class="btn btn-info btn-sm me-1" href="./?x=viewitem&order=<?php echo $row['id_odr']; ?>&meja=<?php echo $row['meja']; ?>&pelanggan=<?php echo $row['pelanggan']; ?>"><img src="../image/eye.svg"></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>