<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT *, SUM(harga*jumlah) AS harganya FROM tb_list_order
                                    -- LEFT JOIN user_login ON user_login.id = tb_order.pelayan
                                    LEFT JOIN tb_order ON tb_order.id_odr = tb_list_order.order
                                    LEFT JOIN tb_menu ON tb_menu.idm = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_odr
                                    GROUP BY id_lso
                                    HAVING tb_list_order.order = $_GET[order]");
$result = array();

$select_menu = mysqli_query($Koneksi, "SELECT idm, nama_menu FROM tb_menu");

$kode = $_GET['order'];
$meja = $_GET['meja'];
$pelanggan = $_GET['pelanggan'];

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}
echo $message;
?>


<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman view Order Item
        </div>
        <div class="card-body">
        <a class="btn btn-primary mb-3" href="javascript:void(0);" onclick="window.history.back();"><i class="bi bi-box-arrow-left"></i></a>
            <div class="row">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-floating mb-3">
                            <input disabled type="text" class="form-control" id="floatingInput" placeholder="password" name="order" value="<?php echo $kode;?>">
                            <label for="floatingInput">Kode Order</label>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-floating mb-3">
                            <input disabled type="text" class="form-control" id="floatingInput" placeholder="Username" name="meja" value="<?php echo $meja;?>">
                            <label for="floatingInput">Meja</label>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-floating mb-3">
                            <input disabled type="text" class="form-control" id="floatingInput" placeholder="Username" name="meja" value="<?php echo $pelanggan;?>">
                            <label for="floatingInput">Pelanggan</label>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if (empty($result)) {
                echo "Buat Order Makanan Terlebih Dahulu";
            } else {
            ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col">Menu</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Pelanggan</th>
                                <th scope="col">Catatan</th>
                                <th scope="col">total</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $total = 0;
                            $kembalian = 0;
                            $nominal_uang = 0;
                            foreach ($result as $row) {
                                $kembalian =$row['kembalian'];
                                $nominal_uang =$row['nominal_uang'];
                            ?>
                                <tr>
                                    <td><?php echo $row['nama_menu'] ?></td>
                                    <td><?php echo number_format($row['harga'],0,',','.') ?></td>
                                    <td><?php echo $row['jumlah']?></td></td>
                                    <td><?php echo $row['pelanggan'] ?></td>
                                    <td class="text-center"><?php echo (!empty($row['catatan'])) ? $row['catatan'] : '-'; ?></td>
                                    <td><?php echo number_format($row['harganya'],0,',','.') ?></td>
                                </tr>
                            <?php
                            $total += $row['harganya'];
                            }
                            ?>
                            <tr>
                            <td colspan="4"></td>
                                <td class="fw-bold">Total harga: </td>
                                <td colspan="3" class="fw-bold"><?php echo number_format($total,2,',','.') ?></td>
                            </tr>
                            <tr>
                            <td colspan="4"></td>
                                <td class="fw-bold">Nominal Uang: </td>
                                <td colspan="3" class="fw-bold"><?php echo number_format($nominal_uang,2,',','.') ?></td>
                            </tr>
                            <tr>
                            <td colspan="4"></td>
                                <td class="fw-bold">Kembalian: </td>
                                <td colspan="3" class="fw-bold"><?php echo number_format($kembalian,2,',','.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>