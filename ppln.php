<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT * FROM tb_list_order 
                                    LEFT JOIN tb_order ON tb_order.id_odr = tb_list_order.order
                                    LEFT JOIN tb_menu ON tb_menu.idm = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_odr
                                    ORDER BY tb_list_order.status, tb_order.waktu_order DESC");
$result = array();

$select_menu = mysqli_query($Koneksi, "SELECT idm, nama_menu FROM tb_menu");

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}

if (isset($_POST['reset_meja'])) {
    $id_lso = $_POST['id_lso'];
    $meja = $_POST['meja'];
    $id_odr = $_POST['id_odr'];

    // Update status di tb_list_order
    $updateStatusQuery = "UPDATE tb_list_order SET status = 3 WHERE id_lso = $id_lso";
    $resultUpdateStatus = mysqli_query($Koneksi, $updateStatusQuery);

    if ($resultUpdateStatus) {
        // Reset status_meja menjadi 0 saat meja dikosongkan
        $updateStatusMejaQuery = "UPDATE tb_meja SET status_meja = 0 WHERE id_meja = $meja";
        $resultUpdateStatusMeja = mysqli_query($Koneksi, $updateStatusMejaQuery);

        if ($resultUpdateStatusMeja) {
            $message = "<script>alert('Meja Dikosongkan'); window.history.back()</script>";
        } else {
            echo "<script>alert('Gagal Mengubah Status Meja'); window.history.back()</script>";
        }
    } else {
        echo "<script>alert('Gagal Mengubah Status Pesanan'); window.history.back()</script>";
    }
}


// echo $message;
?>

<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Customer
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
                                <th scope="col">Meja</th>
                                <th scope="col">pelanggan</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                                $no = 1;
                                foreach ($result as $row) {
                                    if ($row['status'] != 0 && $row['status'] != 1 && $row['status'] != 3) {
                                ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td><?php echo $row['id_odr'] ?></td>
                                        <td><?php echo $row['waktu_order'] ?></td>
                                        <td><?php echo $row['meja'] ?></td>
                                        <td><?php echo $row['pelanggan'] ?></td>
                                        <td><?php echo $row['nama_menu'] ?></td>
                                        <td><?php echo $row['jumlah'] ?></td>
                                        <td><?php
                                                if ($row['status'] == 1) {
                                                    echo "<span class='badge text-bg-warning'>Masuk Ke Dapur</span>";
                                                } elseif ($row['status'] == 2) {
                                                    echo "<span class='badge text-bg-success'>Siap Saji</span>";
                                                } elseif ($row['status'] == 0) {
                                                    echo "<span class='badge text-bg-primary'>Belum Diterima Dapur</span>";
                                                } elseif ($row['status'] == 3) {
                                                    echo "<span class='badge text-bg-primary'>Selesai</span>";
                                                }
                                        ?></td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="<?php echo ($row['status'] != 1) ? 'btn btn-outline-info btn-sm me-1 text-nowrap' : 'btn btn-outline-info btn-sm me-1 text-nowrap disabled'; ?>" data-bs-toggle="modal" data-bs-target="#terima<?php echo $row['id_lso'] ?>">Meja Kosong</button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!--======================================= modal terima dapur =========================================================================-->
                                    <div class="modal fade" id="terima<?php echo $row['id_lso'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Reset Meja Pelanggan</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="id_lso" value="<?php echo $row['id_lso']; ?>">
                                                        <input type="hidden" name="meja" value="<?php echo $row['meja']; ?>">
                                                        <input type="hidden" name="id_odr" value="<?php echo $row['id_odr']; ?>">
                                                        <div class="col-lg-12">
                                                            Apakah Anda Ingin Mengosongkan Meja Nomor <b><?php echo $row['meja'] ?></b>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger" name="reset_meja">Meja Kosong</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!----------------------------------------- akhir terima ----------------------------------------------------------------------------->

                                    <!-- Pemanggilan fungsi notifikasi untuk setiap pesanan -->
                                    <script>
                                        showNotificationIfReady(<?php echo $row['status']; ?>);
                                    </script>
                                <?php
                                    }
                                }
                                ?>
                                <!-- Tambahkan skrip JavaScript -->
                            <script>
                                function showNotification(message) {
                                    Notification.requestPermission().then(function (permission) {
                                        if (permission === "granted") {
                                            var notification = new Notification("Pesanan Siap Saji", {
                                                body: message
                                            });
                                        }
                                    });
                                }

                                function showNotificationIfReady(status) {
                                    if (status === 2) {
                                        showNotification("Pesanan Anda telah Siap Saji!");
                                    }
                                }
                            </script>
                        </tbody>
                    </table>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<?php echo $message; ?>