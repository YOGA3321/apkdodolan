<?php
session_start();
include "../Dikoneksi.php";
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
// var_dump($_POST);

$query = mysqli_query($Koneksi, "SELECT *, SUM(tb_menu.harga * tb_list_order.jumlah) AS harganya, tb_order.waktu_order, tb_bayar.kembalian AS kembalian 
                                    FROM tb_list_order 
                                    LEFT JOIN tb_order ON tb_order.id_odr = tb_list_order.order
                                    LEFT JOIN tb_menu ON tb_menu.idm = tb_list_order.menu
                                    LEFT JOIN tb_bayar ON tb_bayar.id_bayar = tb_order.id_odr
                                    WHERE tb_list_order.order = '" . mysqli_real_escape_string($Koneksi, $_GET['order']) . "'
                                    GROUP BY tb_list_order.id_lso
                                    ORDER BY tb_list_order.id_lso ASC");

$result = array();


$select_menu = mysqli_query($Koneksi, "SELECT idm, nama_menu FROM tb_menu");

$kode = $_GET['order'];
$meja = $_GET['meja'];
$pelanggan = $_GET['pelanggan'];
// $kembalian = 0; // tambahkan deklarasi awal

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}



// <----------------------------- Input Menu ----------------------------------------->
if (isset($_POST['input_order_item'])) {
    $kode_order = $_POST['kode_order'];
    $pelanggan = $_POST['pelanggan'];
    $meja = $_POST['meja'];
    $catatan = $_POST['keterangan'];
    $menu = $_POST['menu'];
    $jumlah = $_POST['jumlah'];
    // $ids = $_POST['ids'];

    // Periksa apakah menu sudah ada di dalam order
    $checkQuery = "SELECT * FROM tb_list_order WHERE menu = '$menu' AND `order` = '$kode_order'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    // Periksa stok makanan
    $checkStokQuery = "SELECT stok FROM tb_menu WHERE idm = $menu";
    $checkStokResult = mysqli_query($Koneksi, $checkStokQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $message = "<script>alert('Menu yang dimasukkan telah ada dalam order ini');
                        window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
    } elseif ($checkStokResult) {
        $stokData = mysqli_fetch_assoc($checkStokResult);
        $availableStok = $stokData['stok'];

        if ($availableStok >= $jumlah) {
            // Query INSERT
            $query = "INSERT INTO tb_list_order (`menu`, `order`, `jumlah`, `catatan`) VALUES ('$menu', '$kode_order', '$jumlah', '$catatan')";
            $result = mysqli_query($Koneksi, $query);

            if ($result) {
                // Pengurangan stok
                $updateStokQuery = "UPDATE tb_menu SET stok = stok - $jumlah WHERE idm = $menu";
                $updateStokResult = mysqli_query($Koneksi, $updateStokQuery);

                if ($updateStokResult) {
                    $message = "<script>alert('Data anda telah tersimpan!');
                                    window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
                } else {
                    $message = "<script>alert('Terjadi kesalahan dalam menyimpan data.');
                                    window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
                }
            } else {
                $message = "<script>alert('Terjadi kesalahan dalam menyimpan data.');
                                window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
            }
        } else {
            // Notifikasi stok habis
            $message = "<script>alert('Stok makanan tidak mencukupi!');
                            window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
        }
    } else {
        $message = "<script>alert('Terjadi kesalahan dalam menyimpan data.');
                        window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
    }
}


// <========================================== Akhir Input Menu=============================================>
// <------------------------------------------edit menu-------------------------------------->
if (isset($_POST['update_order_item'])) {
    $kode_order = $_POST['kode_order'];
    $pelanggan = $_POST['pelanggan'];
    $meja = $_POST['meja'];
    $catatan = $_POST['keterangan'];
    $menu = $_POST['menu'];
    $jumlah = $_POST['jumlah'];
    $ids = $_POST['ids'];

    // Ambil jumlah pesanan sebelumnya dari database
    $getPreviousOrderQuery = "SELECT jumlah FROM tb_list_order WHERE id_lso = $ids";
    $getPreviousOrderResult = mysqli_query($Koneksi, $getPreviousOrderQuery);

    if ($getPreviousOrderResult && mysqli_num_rows($getPreviousOrderResult) > 0) {
        $previousOrderData = mysqli_fetch_assoc($getPreviousOrderResult);
        $previousQuantity = $previousOrderData['jumlah'];

        // Perbedaan antara jumlah pesanan baru dan pesanan sebelumnya
        $quantityDifference = $jumlah - $previousQuantity;

        // Update pesanan
        $updateOrderQuery = "UPDATE tb_list_order SET menu='$menu', jumlah='$jumlah', catatan='$catatan' WHERE id_lso=$ids";
        $updateOrderResult = mysqli_query($Koneksi, $updateOrderQuery);

        if ($updateOrderResult) {
            // Update stok makanan
            $updateStockQuery = "UPDATE tb_menu SET stok = stok - $quantityDifference WHERE idm = $menu";
            $updateStockResult = mysqli_query($Koneksi, $updateStockQuery);

            if ($updateStockResult) {
                $message = "<script>alert('Data telah diperbarui!');
                                window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
            } else {
                $message = "<script>alert('Terjadi kesalahan dalam memperbarui stok.');
                                window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
            }
        } else {
            $message = "<script>alert('Terjadi kesalahan dalam memperbarui pesanan.');
                            window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
        }
    } else {
        $message = "<script>alert('Pesanan tidak ditemukan.');
                        window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
    }
}
// <========================================== Akhir edit Menu=============================================>
/// <=================================hapus item menu====================================>

if (isset($_POST['hapus_list_menu'])) {
    $id = $_POST['ids'];

    $query = "DELETE FROM tb_list_order WHERE id_lso='$id'";
    $result = mysqli_query($Koneksi, $query);

    if ($result) {
        $message = "<script>alert('Data anda telah Di Hapus');
                        window.history.back()</script>";
    } else {
        $message = "<script>alert('Terjadi Kesalahan Saat Menghapus Data Order: " . mysqli_error($Koneksi) . "');
                        window.history.back()</script>";
    }
}


// // <----------------------------- Bayar ----------------------------------------->
// if (isset($_POST['bayar'])) {
//     $kode_order = $_POST['kode_order'];
//     $pelanggan = $_POST['pelanggan'];
//     $meja = $_POST['meja'];
//     $ids = $_POST['ids'];
//     $total = $_POST['total'];
//     $uang = $_POST['uang'];
//     $kembalian = $uang - $total;

//     // Periksa apakah menu sudah ada di dalam order
//     $checkQuery = "SELECT * FROM tb_list_order WHERE menu = '$menu' AND `order` = '$kode_order'";
//     $checkResult = mysqli_query($Koneksi, $checkQuery);

//     if ($kembalian < 0) {
//         $message = "<script>alert('NOMINAL UANG TIDAK MENCUKUPI');
//                         window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
//     }else{
//             $query = "INSERT INTO tb_bayar (id_bayar, nominal_uang, total_bayar, kembalian) VALUES ('$kode_order', '$uang', '$total', '$kembalian')";
//             $result = mysqli_query($Koneksi, $query);

//             if ($result) {
//                 $message = "<script>alert('Pembayaran Berhasil');
//                                 window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
//             } else {
//                 $message = "<script>alert('Pembayaran Gagal');
//                                 window.location.href='?x=orderitem&order=$kode_order&meja=$meja&pelanggan=$pelanggan';</script>";
//             }
        
//     }
// }
// // <========================================== Akhir Bayar=============================================>
// <========================================== Midtrans=============================================>
// require_once dirname(__FILE__) . '../../midtrans/Midtrans.php';
require_once dirname(__FILE__) . '../../vendor/midtrans/midtrans-php/Midtrans.php';
// require_once '../midtrans/Midtrans.php';
// require_once '../vendor/autoload.php';
//SAMPLE REQUEST START HERE

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'SB-Mid-server-p0J5Kw0tX_JHY_HoYJOQzYXQ';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
\Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;

$kode_order = $_GET['order'] ?? '';  // Menggunakan null coalescing untuk mencegah undefined key
$meja = $_GET['meja'] ?? '';
$pelanggan = $_GET['pelanggan'] ?? '';
$id_menu = $_GET['id_menu'] ?? '';
$idS = $_GET['idS'] ?? '';
$total = isset($_POST['total']) ? $_POST['total'] : 0;
$uang = isset($_POST['uang']) ? $_POST['uang'] : 0;
$kembalian = $uang - $total;

if (!empty($pelanggan)) {
    $query_user = mysqli_query($Koneksi, "SELECT * FROM user_login WHERE nama = '" . mysqli_real_escape_string($Koneksi, $pelanggan) . "'");
    if ($row_user = mysqli_fetch_assoc($query_user)) {
        $email = $row_user['email'];
        $NoTlp = $row_user['NoTlp'];
    }    
}

$item_details = array();
foreach ($result as $row) {
    $item_details[] = array(
        'price' => $row['harga'],
        'quantity' => $row['jumlah'],
        'name' => $row['nama_menu']
    );
}

$params = array(
    'transaction_details' => array(
        'order_id' => $kode_order,
        'gross_amount' => $total, // Total pembayaran
    ),
    'item_details' => $item_details, // Menambahkan detail barang yang dibeli
    'customer_details' => array(
        'first_name' => $pelanggan,
        'email' => $email,
        'phone' => $NoTlp,
    ),
);

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    $snapToken; // Mengembalikan token untuk digunakan di frontend
} catch (Exception $e) {
    json_encode(['error' => $e->getMessage()]);
}
// exit;
// echo '<pre>';
// print_r($params);
// echo '</pre>';

// echo "ini: $snapToken";
echo $message;  
// echo "token: $snapToken\n";
?>
<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Order Item
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
            <!--============================= Modal tambah item baru =========================================-->
            <div class="modal fade" id="ModalItem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Item Menu Makanan Dan Minuman</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                                <input type="hidden" name="meja" value="<?php echo $meja ?>">
                                <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="menu" id="" required>
                                                <option selected hidden value="">Pilih Menu</option>
                                                <?php
                                                    foreach($select_menu as $value){
                                                        echo "<option value=$value[idm]>$value[nama_menu]</option>";
                                                    }
                                                ?>
                                            </select>
                                            <label for="menu">Menu Makanan / Minuman</label>
                                            <div class="invalid-feedback">
                                                Pilih Menu Makanan / Minuman.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-floating mb-">
                                            <input type="number" class="form-control" id="floatingInput" placeholder="jumlah" name="jumlah" required>
                                            <label for="floatingInput">Jumlah Porsi</label>
                                            <div class="invalid-feedback">
                                                Masukkan Jumlah Porsi.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="keterangan" name="keterangan">
                                            <label for="floatingPassword">Keterangan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="input_order_item">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--------------=========================== akhir tambah item baru ---------------------------------------------->
            <?php
            if (empty($result)) {
                echo "Buat Order Makanan Anda";
            } else {
            ?>
                <div class="table-responsive">
                <table class="table table-hover">
                        <thead>
                            <tr class="text-nowrap">
                                <th scope="col">Menu</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Qty</th>
                                <th scope="col">Status</th>
                                <th scope="col">Catatan</th>
                                <th scope="col">total</th>
                                <th scope="col">Aksi</th>
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
                                    <td><?php echo (!empty($row['id_bayar'])) ? '<span class="badge text-bg-success">Dibayar</span>' : '<span class="badge text-bg-danger">Belum Dibayar</span>'; ?></td>
                                    <td><?php echo $row['catatan']?></td></td>
                                    <td><?php echo number_format($row['harganya'],0,',','.') ?></td>
                                    <td>
                                    <div class="d-flex">
                                        <?php
                                            // Periksa status pesanan
                                            if (in_array($row['status'], [1, 2, 3, 4]) || !empty($row['id_bayar'])) {
                                                // Jika status adalah 1, 2, atau 3, atau pesanan sudah dibayar, atur tombol menjadi disabled
                                                echo '<button class="btn btn-secondary btn-sm me-1 disabled" data-bs-toggle="modal" data-bs-target="#ModalEdit'.$row['id_lso'].'"><img src="../image/pencil-square.svg"></button>';
                                                echo '<button class="btn btn-secondary btn-sm me-1 disabled" data-bs-toggle="modal" data-bs-target="#ModalDelete'.$row['id_lso'].'"><img src="../image/trash.svg"></button>';
                                            } else {
                                                // Jika tidak, biarkan tombol tetap aktif
                                                echo '<button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalEdit'.$row['id_lso'].'"><img src="../image/pencil-square.svg"></button>';
                                                echo '<button class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalDelete'.$row['id_lso'].'"><img src="../image/trash.svg"></button>';
                                            }
                                        ?>
                                    </div>
                                </td>
                                </tr>

                                <!----- ya itu ----->
                                <!--======================================= modal Edit =========================================================================-->
                                <div class="modal fade" id="ModalEdit<?php echo $row['id_lso'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Menu Makanan Dan Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST" class="needs-validation" novalidate>
                                                    <input type="hidden" name="ids" value="<?php echo $row['id_lso'] ?>">
                                                    <input type="hidden" name="kode_order" value="<?php echo $kode ?>">
                                                    <input type="hidden" name="meja" value="<?php echo $meja ?>">
                                                    <input type="hidden" name="pelanggan" value="<?php echo $pelanggan ?>">
                                                    <div class="row">
                                                        <div class="col-lg-8">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-select" name="menu" id="" required>
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
                                                                <div class="invalid-feedback">
                                                                    Pilih Menu Makanan / Minuman.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-floating mb-">
                                                                <input type="number" class="form-control" id="floatingInput" placeholder="jumlah" name="jumlah" value="<?php echo $row['jumlah']?>" required>
                                                                <label for="floatingInput">Jumlah Porsi</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Jumlah Porsi.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="floatingInput" placeholder="keterangan" name="keterangan" value="<?php echo $row['catatan']?>">
                                                                <label for="floatingPassword">Keterangan</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary" name="update_order_item">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!----------------------------------------- akhir edit ----------------------------------------------------------------------------->

                                <!--======================================= modal Delete =========================================================================-->
                                <div class="modal fade" id="ModalDelete<?php echo $row['id_lso'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Makanan Atau Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                    <input type="hidden" value="<?php echo $row['id_lso'];?>" name="ids">
                                                    <div class="col-lg-12">
                                                        Apakah Anda Ingin Menghapus Menu <b><?php echo $row['nama_menu']?></b>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger" name="hapus_list_menu">Hapus</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- akhir Delete -->
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
            <div class="mb-3">
                <button class="<?php echo (!empty($row['id_bayar'])) ? 'btn btn-secondary disabled' : 'btn btn-success'; ?>" data-bs-toggle="modal" data-bs-target="#ModalItem">
                    <i class="bi bi-plus-circle-fill"></i> Item
                </button>
                <!-- <button class="<?php echo (!empty($row['id_bayar'])) ? 'btn btn-secondary disabled' : 'btn btn-primary'; ?>" data-bs-toggle="modal" data-bs-target="#paymentForm">
                    <i class="bi bi-cash-coin"></i> Checkout
                </button> -->
                <button id="pay-button" class="btn btn-primary"><i class="bi bi-cash-coin"></i> Checkout !</button>
                <button onclick="printstruk()" class="btn btn-info">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<div id="strukcontent" class="d-none">
    <style>
        #struk {
            font-family: "Merchant Copy Font", sans-serif;
            font-size: 16px;
            max-width: 400px;
            width: 58mm;
            margin-bottom: 20px;
            text-align: center;
        }
        #struk h2 {
            font-family: "Merchant Copy Font", sans-serif;
            color: #333;
        }
        #struk p, #struk span.info {
            font-family: "Merchant Copy Font", sans-serif;
            margin: 5px 0;
            font-size: 16px;
            text-align: left;
        }
        #struk table {
            font-size: 14px;
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse; /* Ubah menjadi collapse */
        }
        #struk th, #struk td {
            border: none; /* Menghapus border pada sel tabel */
            padding: 8px;
            text-align: left;
        }
        #struk .dashed-line {
            border: 1px dashed red;
        }
    </style>

    <div id="struk">
        <h2>Struk Pembayaran WAROENG MODERN BITES</h2><br>
        <h text-align="center">Jl. Punduttrate, Desa Pundut, Kecamatan Benjeng, Kabupaten Gresik, 61172</h>
        <hr class="dashed-line">
        <p>Kode Order: <?php echo $kode ?></p>
        <p>Meja: <?php echo $meja ?></p>
        <p>Pelanggan: <?php echo $pelanggan ?></p>
        <p>Waktu Order: <?php echo date('d/m/Y H:i:s', strtotime($result[0]['waktu_order'])) ?></p>
        <hr class="dashed-line">
        <table>
            <?php
            $total = 0;
            foreach ($result as $row) {
                $total += $row['harganya'];
            ?>
                <tr>
                    <td><?php echo $row['nama_menu'] ?></td>
                    <td><?php echo $row['jumlah'] ?></td>
                    <td><?php echo number_format($row['harga'], 0, ',', ',') ?></td>
                    <td><?php echo number_format($row['harganya'], 0, ',', ',') ?></td>
                </tr>
            <?php } ?>
        </table>
        <!-- Bagian baru -->
        <hr class="dashed-line">
        <table>
            <tr>
                <td class="fw-bold">TOTAL: </td>
                <td colspan="6"></td>
                <td colspan="6"></td>
                <td colspan="9"></td>
                <td class="fw-bold"><?php echo number_format($total,0,',','.') ?></td>
            </tr>
            <tr>
                <td class="fw-bold">TUNAI: </td>
                <td colspan="6"></td>
                <td colspan="6"></td>
                <td colspan="9"></td>
                <td  class="fw-bold"><?php echo number_format($nominal_uang,0,',','.') ?></td>
            </tr>
            <tr>
                <td class="fw-bold">KEMBALIAN: </td>
                <td colspan="6"></td>
                <td colspan="6"></td>
                <td colspan="9"></td>
                <td class="fw-bold"><?php echo number_format($kembalian,0,',','.') ?></td>
            </tr>
        </table><br><br><br>
        <hr class="dashed-line"><br>
    </div>
</div>

<script>
    function printstruk() {
        var strukcontent = document.getElementById("strukcontent").innerHTML;

        var printFrame = document.createElement('iframe');
        printFrame.style.display = 'none';
        document.body.appendChild(printFrame);
        printFrame.contentDocument.write(strukcontent);
        printFrame.contentWindow.print();
        document.body.removeChild(printFrame);
    }

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
<script type="text/javascript">
    // For example trigger on button clicked, or any time you need
    var snapToken = '<?php echo $snapToken; ?>'; 
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
      // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token.
      // Also, use the embedId that you defined in the div above, here.
      window.snap.pay('snapToken');
    });
  </script>
