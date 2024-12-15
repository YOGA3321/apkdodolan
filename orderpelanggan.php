<?php
session_start();
include "../Dikoneksi.php";

// Simpan level user dalam variabel
$level_user = $_SESSION['level'];
$idPelanggan = $_SESSION['id_lgn'];

$query = mysqli_query($Koneksi, "SELECT tb_order.*,tb_bayar.*, SUM(harga*jumlah) AS harganya, user_login.level, user_login.nama, tb_list_order.status AS status_list_order FROM tb_order
                                    LEFT JOIN user_login ON user_login.id = tb_order.pelayan
                                    LEFT JOIN tb_list_order ON tb_list_order.order = tb_order.id_odr
                                    LEFT JOIN tb_menu ON tb_menu.idm = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_odr
                                    WHERE user_login.id = $idPelanggan
                                    GROUP BY id_odr ORDER BY waktu_order DESC");
$result = array();

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}

$queryUser = mysqli_query($Koneksi, "SELECT * FROM user_login");
$resultT = array();
while ($recordd = mysqli_fetch_array($queryUser)) {
    $resultT[] = $recordd;
}
// var_dump($_SESSION['nama']);
// var_dump($_SESSION['id_lgn']);
$tableQuery = mysqli_query($Koneksi, "SELECT * FROM tb_meja");
$tables = array();

while ($tableRecord = mysqli_fetch_array($tableQuery)) {
    $tables[] = $tableRecord;
}

// <-----------------------------input Order----------------------------------------->
if (isset($_POST['buat_order'])) {
    $kode_order = $_POST['kode_order'];
    $pelanggan = $_POST['pelanggan'];
    $meja = $_POST['meja'];
    
    $checkQuery = "SELECT * FROM tb_order WHERE id_odr = '$kode_order'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Order yang dimasukkan telah ada');
                            window.history.back()</script>";
    } else {
        // Update status_meja menjadi 1
        $updateMejaQuery = "UPDATE tb_meja SET status_meja = 1 WHERE id_meja = '$meja'";
        mysqli_query($Koneksi, $updateMejaQuery);

        // Insert order baru
        $query = "INSERT INTO tb_order (id_odr, meja, pelanggan, pelayan) VALUES ('$kode_order', '$meja', '$pelanggan', '$_SESSION[id_lgn]')";
        $result = mysqli_query($Koneksi, $query);
        if ($result) {
            $message = "<script>alert('Data anda telah tersimpan!');
                    window.location='../Pemesanan/?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan'</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan dalam menyimpan data.');
                    window.location='Order'</script>";
        }
    }
}

// <========================================== Akhir Input Order=============================================>
// <------------------------------------------update order-------------------------------------->
if (isset($_POST['edit_order'])) {
    $kode_order = $_POST['kode_order'];
    $pelanggan = $_POST['pelanggan'];
    $meja_baru = $_POST['meja']; // Nomor meja yang baru dipilih

    // Periksa nomor meja sebelumnya
    $query_nomor_meja_sebelumnya = mysqli_query($Koneksi, "SELECT meja FROM tb_order WHERE id_odr = '$kode_order'");
    $data_nomor_meja_sebelumnya = mysqli_fetch_assoc($query_nomor_meja_sebelumnya);
    $meja_sebelumnya = $data_nomor_meja_sebelumnya['meja'];

    if ($meja_sebelumnya != $meja_baru) {
        // Update status_meja meja lama menjadi 0
        $update_status_meja_lama = mysqli_query($Koneksi, "UPDATE tb_meja SET status_meja = 0 WHERE id_meja = '$meja_sebelumnya'");
        // Update status_meja meja baru menjadi 1
        $update_status_meja_baru = mysqli_query($Koneksi, "UPDATE tb_meja SET status_meja = 1 WHERE id_meja = '$meja_baru'");
    }

    // Update order dengan nomor meja baru
    $query = "UPDATE tb_order SET meja='$meja_baru',pelanggan='$pelanggan' WHERE id_odr = '$kode_order'";
    $result = mysqli_query($Koneksi, $query);
    if ($result) {
        $message = "<script>alert('Data anda telah tersimpan!');
                    window.location='Order'</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan dalam menyimpan data.');
                    window.location='Order'</script>";
    }
}
// <========================================== Akhir update Order=============================================>
/// <=================================hapus order====================================>
if (isset($_REQUEST['hapus_order'])) {
    $kode_order = $_REQUEST['kode_order'];

    // Get the table number of the order to update its status
    $get_table_query = "SELECT meja FROM tb_order WHERE id_odr='$kode_order'";
    $table_result = mysqli_query($Koneksi, $get_table_query);
    $table_row = mysqli_fetch_assoc($table_result);
    $meja = $table_row['meja'];

    // Check if the order has associated items
    $checkQuery = "SELECT * FROM tb_list_order WHERE `order` = '$kode_order'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $Message = "<script>alert('Order Telah Memiliki Item Order, Data Order Tidak Dapat Dihapus');
                    window.history.back()</script>";
    } else {
        $query = "DELETE FROM tb_order WHERE id_odr='$kode_order'";
        $result = mysqli_query($Koneksi, $query);

        if ($result) {
            // Update the status of the table to 0 after deleting the order
            $update_status_query = "UPDATE tb_meja SET status_meja = 0 WHERE id_meja = '$meja'";
            $update_status_result = mysqli_query($Koneksi, $update_status_query);

            if ($update_status_result) {
                $message = "<script>alert('Data anda telah Di Hapus');
                    window.history.back()</script>";
            } else {
                $message = "<script>alert('Terjadi Kesalahan Saat Menghapus Data Order:');
                    window.history.back()</script>";
            }
        } else {
            $message = "<script>alert('Terjadi Kesalahan Saat Menghapus Data Order:');
                window.history.back()</script>";
        }
    }
}



echo $message;
?>

<meta http-equiv="refresh" content="30">
<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Order Pelanggan
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambahOrder">Tambah Pesanan +</button>
                </div>
            </div>
            <!--============================= Modal tambah order baru =========================================-->
            <div class="modal fade" id="ModalTambahOrder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-fullscreen-md-down">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Order Makanan Dan Minuman</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="kode_order" placeholder="Nama" name="kode_order" value="<?php echo date('ymd').rand(100,999) ?>" readonly>
                                            <label for="kode_order">Kode Order</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="meja" name="meja" required>
                                                <?php foreach ($tables as $table) : ?>
                                                    <?php if ($table['status_meja'] == 0) : ?>
                                                        <option value="<?php echo $table['id_meja']; ?>"><?php echo $table['nomor_meja']; ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="meja">Meja</label>
                                            <div class="invalid-feedback">
                                                Pilih No. Meja.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="pelanggan" placeholder="Nama Pelanggan" name="pelanggan" value="<?php echo $_SESSION['nama'] ?>" readonly required>
                                            <label for="pelanggan">Nama Pelanggan</label>
                                            <div class="invalid-feedback">
                                                Masukkan Nama Anda.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="buat_order">Buat Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--------------=========================== akhir tambah order baru ---------------------------------------------->

            <?php
            if (empty($result)) {
                echo "Lakukan Pemesanan";
            } else {
            ?>
                <div class="table-responsive mt-2">
                    <table class="table table-hover text-center" id="example">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col">No.</th>
                                <th scope="col">Kode Order</th>
                                <th scope="col">Pelanggan</th>
                                <th scope="col">Meja</th>
                                <!-- <th scope="col">Total Harga</th> -->
                                <!-- <th scope="col">Pelayan</th> -->
                                <th scope="col">Status List Order</th>
                                <!-- <th scope="col">Status Bayar</th> -->
                                <th scope="col">Waktu Order</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $no = 1;
                            foreach ($result as $row) {
                                if ($row['status_list_order'] != 3) {
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $no++ ?></th>
                                    <td><?php echo $row['id_odr'] ?></td>
                                    <td><?php echo $row['pelanggan'] ?></td></td>
                                    <td><?php echo $row['meja']?></td></td>
                                    <!-- <td><?php echo number_format($row['harganya'],0,',','.') ?></td> -->
                                    <!-- <td><?php echo $row['nama'] ?></td> -->
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
                                    <!-- <td><?php echo (!empty($row['id_bayar'])) ? '<span class="badge text-bg-success">Dibayar</span>' : '<span class="badge text-bg-danger">Belum Dibayar</span>'; ?></td> -->
                                    <td><?php echo $row['waktu_order'] ?></td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn <?php echo ($_SESSION['id_lgn'] == $row['pelayan']) ? 'btn-info' : 'btn-secondary'; ?> btn-sm me-1"
                                            <?php echo ($_SESSION['id_lgn'] == $row['pelayan']) ? '' : 'disabled'; ?> onclick="if(<?php echo ($_SESSION['id_lgn'] == $row['pelayan']) ? 'true' : 'false'; ?>) window.location.href='./?x=orderitem&order=<?php echo $row['id_odr']; ?>&meja=<?php echo $row['meja']; ?>&pelanggan=<?php echo $row['pelanggan']; ?>';"><img src="../image/eye.svg"></button>
                                            <button class="btn <?php echo ($_SESSION['id_lgn'] == $row['pelayan'] && empty($row['id_bayar']) && $row['status_list_order'] != 1 && (strtotime($row['waktu_order']) + 7 * 60) > time()) ? 'btn-warning' : 'btn-secondary'; ?> btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['id_odr'] ?>"
                                                <?php echo ($_SESSION['id_lgn'] == $row['pelayan'] && empty($row['id_bayar']) && $row['status_list_order'] != 1 && (strtotime($row['waktu_order']) + 7 * 60) > time()) ? '' : 'disabled'; ?>>
                                                <img src="../image/pencil-square.svg">
                                            </button>
                                            <button class="btn <?php echo ($_SESSION['id_lgn'] == $row['pelayan'] && empty($row['id_bayar']) && $row['status_list_order'] != 1 && (strtotime($row['waktu_order']) + 7 * 60) > time()) ? 'btn-danger' : 'btn-secondary'; ?> btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['id_odr'] ?>"
                                                <?php echo ($_SESSION['id_lgn'] == $row['pelayan'] && empty($row['id_bayar']) && $row['status_list_order'] != 1 && (strtotime($row['waktu_order']) + 7 * 60) > time()) ? '' : 'disabled'; ?>>
                                                <img src="../image/trash.svg">
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!--======================================= modal Edit =========================================================================-->
                                <div class="modal fade" id="ModalEdit<?php echo $row['id_odr'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Menu Makanan Dan Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST" >
                                                    <div class="row">
                                                        <div class="col-lg-3">
                                                            <div class="form-floating mb-3">
                                                                <input readonly="" type="text" class="form-control" id="kode_order" placeholder="Nama" name="kode_order" value="<?php echo $row['id_odr'] ?>" readonly>
                                                                <label for="kode_order">Kode Order</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-select" id="meja" name="meja" <?php echo $row['meja_updated'] ? 'disabled' : 'required'; ?>>
                                                                    <?php foreach ($tables as $table) : ?>
                                                                        <?php if ($table['status_meja'] == 0 || $table['id_meja'] == $row['meja']) : ?>
                                                                            <option value="<?php echo $table['id_meja']; ?>" <?php echo ($table['id_meja'] == $row['meja']) ? 'selected' : ''; ?>><?php echo $table['nomor_meja']; ?></option>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label for="meja">Meja</label>
                                                                <div class="invalid-feedback">
                                                                    <?php echo $row['meja_updated'] ? 'Meja hanya dapat diupdate sekali.' : 'Pilih No. Meja.'; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-7">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="pelanggan" placeholder="Nama Pelanggan" name="pelanggan" value="<?php echo $_SESSION['nama'] ?>" required>
                                                                <label for="pelanggan">Nama Pelanggan</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Nama Anda.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary" name="edit_order">Save Chage</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!----------------------------------------- akhir edit ----------------------------------------------------------------------------->

                                <!--======================================= modal Delete =========================================================================-->
                                <div class="modal fade" id="ModalDelete<?php echo $row['id_odr'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Data User</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                    <input type="hidden" value="<?php echo $row['id_odr'];?>" name="kode_order">
                                                    <div class="col-lg-12">
                                                        Apakah Anda Ingin Menghapus Orde Atas Nama <b><?php echo $row['pelanggan']?></b> Dengan Nomor Order <b><?php echo $row['id_odr']?></b>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger" name="hapus_menu">Hapus</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- akhir Delete -->
                            <?php
                            }
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

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
    }, false)
  })
})()
</script>