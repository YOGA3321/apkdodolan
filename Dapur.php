<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT * FROM tb_list_order 
                                    LEFT JOIN tb_order ON tb_order.id_odr = tb_list_order.order
                                    LEFT JOIN tb_menu ON tb_menu.idm = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_odr
                                    ORDER BY waktu_order ASC");
$result = array();

$select_menu = mysqli_query($Koneksi, "SELECT idm, nama_menu FROM tb_menu");

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}


// <----------------------------- Terima ----------------------------------------->
if (isset($_POST['terima'])) {
    $ids = $_POST['ids'];
    $catatan = $_POST['keterangan'];


        $query = "UPDATE tb_list_order SET catatan='$catatan', status= 1 WHERE id_lso=$ids";
        $result = mysqli_query($Koneksi, $query);

        if ($result) {
            $message = "<script>alert('Berhasil Terima Order Oleh Dapur');
                            window.history.back()</script>";
        } else {
            echo "<script>alert('Gagal Terima Order Oleh Dapur');
                        window.history.back()</script>";
        }
    
}


// <========================================== Akhir Terima=============================================>

// <----------------------------- siap saji ----------------------------------------->
if (isset($_POST['siapsaji'])) {
    $ids = $_POST['ids'];
    $catatan = $_POST['keterangan'];


        $query = "UPDATE tb_list_order SET catatan='$catatan', status= 2 WHERE id_lso=$ids";
        $result = mysqli_query($Koneksi, $query);

        if ($result) {
            $message = "<script>alert('Order Siap Disajikan');
                            window.history.back()</script>";
        } else {
            echo "<script>alert('Gagal Proses Data');
                        window.history.back()</script>";
        }
    
}


// <========================================== Akhir Siap Saji=============================================>

echo $message;
?>

<meta http-equiv="refresh" content="30">
<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Dapur
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
                                <th scope="col">Menu</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Catatan</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;
                        foreach ($result as $row) {
                            if ($row['status'] != 2 && $row['status'] != 3) {
                        ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo $row['id_odr'] ?></td>
                                    <td><?php echo $row['waktu_order'] ?></td>
                                    <td><?php echo $row['nama_menu'] ?></td>
                                    <td><?php echo $row['jumlah'] ?></td>
                                    <td><?php echo $row['catatan'] ?></td>
                                    <td><?php
                                            if ($row['status'] == 1) {
                                                echo "<span class='badge text-bg-warning'>Masuk Ke Dapur</span>";
                                            } elseif ($row['status'] == 2) {
                                                echo "<span class='badge text-bg-success'>Siap Saji</span>";
                                            } elseif ($row['status'] == 0) {
                                                echo "<span class='badge text-bg-primary'>Belum Diterima Dapur</span>";
                                            } elseif ($row['status'] == 3) {
                                                echo "<span class='badge text-bg-danger'>Selesai</span>";
                                            }
                                            ?>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="<?php echo (!empty($row['status'] != 0) && ($row['status']) !=3) ? 'btn btn-secondary btn-sm me-1 disabled' : 'btn btn-primary btn-sm me-1'; ?>" data-bs-toggle="modal" data-bs-target="#terima<?php echo $row['id_lso'] ?>">Terima</button>
                                            <button class="<?php echo (empty($row['status'] != 0) && ($row['status'] !=3)) ? 'btn btn-secondary btn-sm me-1 text-nowrap disabled' : 'btn btn-success btn-sm me-1 text-nowrap'; ?>" data-bs-toggle="modal" data-bs-target="#siapsaji<?php echo $row['id_lso'] ?>">Siap Saji</button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!--======================================= modal terima dapur =========================================================================-->
                                <div class="modal fade" id="terima<?php echo $row['id_lso'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Menu Makanan Dan Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                    <input type="hidden" name="ids" value="<?php echo $row['id_lso'] ?>">
                                                    <div class="row">
                                                        <div class="col-lg-8">
                                                            <div class="form-floating mb-3">
                                                                <select disabled class="form-select" name="menu" id="">
                                                                    <option selected hidden value="">Pilih Menu</option>
                                                                    <?php
                                                                        foreach($select_menu as $value){
                                                                            if($row['menu'] == $value['idm']){
                                                                                echo "<option selected value=$value[idm]>$value[nama_menu]</option>";
                                                                            }else{
                                                                            echo "<option value=$value[idm]>$value[nama_menu]</option>";
                                                                            }
                                                                        }
                                                                    ?>
                                                                </select>
                                                                <label for="menu">Menu Makanan / Minuman</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-floating mb-">
                                                                <input disabled type="number" class="form-control" id="floatingInput" placeholder="jumlah" name="jumlah" value="<?php echo $row['jumlah']?>">
                                                                <label for="floatingInput">Jumlah Porsi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="floatingInput" placeholder="keterangan" name="keterangan" value="<?php echo $row['catatan']?>" readonly>
                                                                <label for="floatingPassword">Keterangan</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary" name="terima">Terima</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!----------------------------------------- akhir terima ----------------------------------------------------------------------------->


                                <!--======================================= modal siap saji =========================================================================-->
                                <div class="modal fade" id="siapsaji<?php echo $row['id_lso'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Menu Makanan Dan Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                    <input type="hidden" name="ids" value="<?php echo $row['id_lso'] ?>">
                                                    <div class="row">
                                                        <div class="col-lg-8">
                                                            <div class="form-floating mb-3">
                                                                <select disabled class="form-select" name="menu" id="">
                                                                    <option selected hidden value="">Pilih Menu</option>
                                                                    <?php
                                                                        foreach($select_menu as $value){
                                                                            if($row['menu'] == $value['idm']){
                                                                                echo "<option selected value=$value[idm]>$value[nama_menu]</option>";
                                                                            }else{
                                                                            echo "<option value=$value[idm]>$value[nama_menu]</option>";
                                                                            }
                                                                        }
                                                                    ?>
                                                                </select>
                                                                <label for="menu">Menu Makanan / Minuman</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-floating mb-">
                                                                <input disabled type="number" class="form-control" id="floatingInput" placeholder="jumlah" name="jumlah" value="<?php echo $row['jumlah']?>">
                                                                <label for="floatingInput">Jumlah Porsi</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="floatingInput" placeholder="keterangan" name="keterangan" value="<?php echo $row['catatan']?>" readonly>
                                                                <label for="floatingPassword">Keterangan</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary" name="siapsaji">Siap Saji</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!----------------------------------------- akhir siap saji ----------------------------------------------------------------------------->
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

