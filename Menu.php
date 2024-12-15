<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT * FROM tb_menu LEFT JOIN tb_kategori_menu ON tb_kategori_menu.idk = tb_menu.kategori");
$result = array();

$select_kat_menu = mysqli_query($Koneksi, "SELECT idk, kategori_menu FROM tb_kategori_menu");

$koderandom = rand(10000, 99999) . "-";
$target_dir = "../foto menu/";
$target_file = $target_dir . $koderandom . basename($_FILES['foto']['name']);
$fototype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));



while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}


// <-----------------------------input Menu----------------------------------------->

if (isset($_POST['input_menu'])) {
    $nama_menu = $_POST['namamenu'];
    $keterangan = $_POST['keterangan'];
    $katmenu = $_POST['katmenu'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // cek apakah foto atau bukan
    $cekfoto = getimagesize($_FILES['foto']['tmp_name']);
    if ($cekfoto === false) {
        echo "ini bukan file gambar";
        $statusupload = 0;
    } else {
        $statusupload = 1;
        if (file_exists($target_file)) {
            echo "Maaf file yang dimasukkan telah ada";
            $statusupload = 0;
        } else {
            if ($_FILES['foto']['size'] > 5242880) { // 5MB
                echo "File Yang di Upload Terlalu Besar";
                $statusupload = 0;
            } else {
                if (!in_array($fototype, array("jpg", "png", "jpeg", "gif", "webp", "svg"))) {
                    echo "Maaf, hanya diperbolehkan gambar yang memiliki format JPG, PNG, JPEG, GIF, WEBP, SVG";
                    $statusupload = 0;
                }
            }
        }
    }

    if ($statusupload == 0) {
        echo "<script>alert('Gambar Tidak Dapat Diupload'); window.history.back()</script>";
    } else {
        $select = mysqli_query($Koneksi, "SELECT idm FROM tb_menu WHERE nama_menu = '$nama_menu'");
        if (mysqli_num_rows($select) > 0) {
            echo "<script>alert('Nama Menu yang Dimasukkan Telah Ada'); window.history.back()</script>";
        } else {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $query = "INSERT INTO tb_menu (foto_menu, nama_menu, keterangan, kategori, harga, stok) VALUES ('$target_file', '$nama_menu', '$keterangan', '$katmenu', '$harga', '$stok')";
                $result = mysqli_query($Koneksi, $query);
                if ($result) {
                    echo "<script>alert('Data anda telah tersimpan!'); window.history.back()</script>";
                } else {
                    echo "<script>alert('Data Anda Gagal Tersimpan!'); window.history.back()</script>";
                }
            } else {
                echo "<script>alert('Maaf, Terjadi Kesalahan File'); window.history.back()</script>";
            }
        }
    }
}


// <========================================== Akhir Input Menu=============================================>

// <------------------------------------------update menu-------------------------------------->
if (isset($_POST['update_menu'])) {
    $nama_menu = $_POST['namamenu'];
    $keterangan = $_POST['keterangan'];
    $katmenu = $_POST['katmenu'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Query update menu
    $query = "UPDATE tb_menu SET nama_menu='$nama_menu', keterangan='$keterangan', kategori='$katmenu', harga='$harga', stok='$stok' WHERE idm=" . $_POST['id'];

    $result = mysqli_query($Koneksi, $query);

    if ($result) {
        $message = "<script>alert('Data anda telah tersimpan!');
                    window.history.back()</script>";
    } else {
        $message = "<script>alert('Data Anda Gagal Tersimpan!');
                    window.history.back()</script>";
    }
}
// <------------------------------------------update foto-------------------------------------->
if (isset($_POST['update_foto'])) {
    $nama_menu = $_POST['namamenu'];
    $keterangan = $_POST['keterangan'];
    $katmenu = $_POST['katmenu'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];


    // Cek apakah file gambar atau bukan
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if($check !== false) {
        $statusupload = 1;
    } else {
        $message = "<script>alert('File bukan gambar.');
        window.history.back()</script>";
        $statusupload = 0;
    }

    // Cek jika file sudah ada
    if (file_exists($target_file)) {
        $message = "<script>alert('Maaf, file sudah ada.');
        window.history.back()</script>";
        $statusupload = 0;
    }

    // Batasi ukuran file
    if ($_FILES["foto"]["size"] > 5242880) { // 5MB
        $message = "<script>alert('Maaf, file terlalu besar.');
        window.history.back()</script>";
        $statusupload = 0;
    }

    // Hanya izinkan format gambar tertentu
    if($fototype != "jpg" && $fototype != "png" && $fototype != "jpeg"
    && $fototype != "gif" && $fototype != "webp" && $fototype != "svg" ) {
        $message = "<script>alert('Maaf, hanya format JPG, JPEG, PNG, GIF, WEBP, SVG yang diperbolehkan.');
        window.history.back()</script>";
        $statusupload = 0;
    }

    // Jika semua kondisi terpenuhi, coba upload file
    if ($statusupload == 0) {
        $message = "<script>alert('Maaf, file tidak terupload.');
        window.history.back()</script>";
    } else {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $query = "UPDATE tb_menu SET foto_menu = '" . $koderandom . $_FILES['foto']['name'] . "' WHERE id='idm'";
            $result = mysqli_query($Koneksi, $query);
            if ($result) {
                $message = "<script>alert('Data telah tersimpan!');
                window.history.back()</script>";
            } else {
                $message = "<script>alert('Gagal menyimpan data!');
                window.history.back()</script>";
            }
        } else {
            $message = "<script>alert('Maaf, terjadi kesalahan saat mengupload file.');
            window.history.back()</script>";
        }
    }
}


/// <=================================hapus menu====================================>

if (isset($_POST['hapus_menu'])) {
    $id = $_POST['idm'];
    $foto = $_POST['foto_menu'];

    $query = mysqli_query($Koneksi, "DELETE FROM tb_menu WHERE idm='$id'");

    if ($query) {
        // Hapus foto dari folder
        unlink("../foto menu/$foto");
        echo "<script>alert('Data anda telah di hapus');
                window.history.back()</script>";
    } else {
        echo "<script>alert('Terjadi Kesalahan Saat Menghapus Data Menu');
                window.location='../Menu'</script>";
    }
    exit();
}


echo $message;
?>


<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman menu
        </div>
        <div class="card-body">
            <div class="row">
            <?php if ($hasil['level'] == 1 || $hasil['level'] == 2 || $hasil['level'] == 4){?>
                <div class="col d-flex justify-content-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambahMenu">Tambah Menu+</button>
                </div>
            <?php } ?>
            </div>
            <!--============================= Modal tambah menu baru =========================================-->
            <div class="modal fade" id="ModalTambahMenu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Menu Makanan Dan Minuman</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control py-3" id="uploadfoto" placeholder="Nama" name="foto" required>
                                            <label class="input-group-text" for="uploadfoto">Upload Foto Menu</label>
                                            <div class="invalid-feedback">
                                                Masukkan Foto Menu.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="Username" name="namamenu" required>
                                            <label for="floatingInput">Nama Menu</label>
                                            <div class="invalid-feedback">
                                                Masukkan Nama Menu.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="keterangan" name="keterangan" required>
                                            <label for="floatingPassword">Keterangan</label>
                                            <div class="invalid-feedback">
                                                Masukkan Keterangan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" aria-label="Default select example" name="katmenu" required>
                                                <option selected hidden value="">Pilih Kategori Makanan</option>
                                                <?php
                                                foreach ($select_kat_menu as $value) {
                                                    echo "<option value='" . $value['idk'] . "'>" . $value['kategori_menu'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                            <label for="floatingInput">Kategori Makanan dan minuman</label>
                                            <div class="invalid-feedback">
                                                Masukkan Kategori Makanan.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="floatingInput" placeholder="harga" name="harga" required>
                                            <label for="floatingInput">Harga</label>
                                            <div class="invalid-feedback">
                                                Masukkan Harga.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="floatingInput" placeholder="stok" name="stok" required>
                                            <label for="floatingInput">Stok</label>
                                            <div class="invalid-feedback">
                                                Masukkan Stok Makanan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="input_menu">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--------------=========================== akhir tambah menu baru ---------------------------------------------->

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
                                <th scope="col">Foto Menu</th>
                                <th scope="col">Nama Menu</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Jenis Menu</th>
                                <th scope="col">Kategori</th>
                                <th scope="col">Harga</th>
                                <th scope="col">Stok</th>
                                <?php if ($hasil['level'] == 1 || $hasil['level'] == 2 || $hasil['level'] == 4){?>
                                <th scope="col">Aksi</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $no = 1;
                            foreach ($result as $row) {
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $no++ ?></th>
                                    <td><img src="../foto menu/<?php echo $row['foto_menu'] ?>" class="rounded mx-auto d-block" alt="..." width="160"></td>
                                    <td><?php echo $row['nama_menu'] ?></td>
                                    <td><?php echo $row['keterangan'] ?></td></td>
                                    <td><?php echo ($row['jenis_menu']== 1) ? "Makanan" : "Minuman"?></td></td>
                                    <td><?php echo $row['kategori_menu'] ?></td>
                                    <td><?php echo number_format($row['harga'],0,',','.') ?></td>
                                    <td><?php if ($row['stok'] == 0) { echo '<div class="alert alert-danger fw-bold text-center" role="alert">Habis</div>'; } else { echo '<div class="alert alert-success fw-bold text-center" role="alert">Tersedia</div>'; }?></td>
                                    <?php if ($hasil['level'] == 1 || $hasil['level'] == 2 || $hasil['level'] == 4){?>
                                    <td>
                                        <div class="d-flex mb-2">
                                            <button class="btn btn-info btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalView<?php echo $row['idm'] ?>"><img src="../image/eye.svg"></button>
                                            <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['idm'] ?>"><img src="../image/pencil-square.svg"></button>
                                            <button class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['idm'] ?>"><img src="../image/trash.svg"></button>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ModalEditfoto<?php echo $row['idm'] ?>">Update foto</button>
                                        </div>
                                    </td>
                                    <?php } ?>
                                </tr>
                                <!--------------- Modal View -------------------->
                                <div class="modal fade" id="ModalView<?php echo $row['idm'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">View Menu Makanan Dan Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" value="<?php echo $row['idm'] ?>" name="id">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-floating mb-3">
                                                                <input disabled type="text" class="form-control" id="floatingInput" value="<?php echo $row['nama_menu'] ?>">
                                                                <label for="floatingInput">Nama Menu</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-floating mb-3">
                                                                <input disabled type="text" class="form-control" id="floatingInput" value="<?php echo $row['keterangan'] ?>">
                                                                <label for="floatingPassword">Keterangan</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="form-floating mb-3">
                                                                <select disabled class="form-select" aria-label="Default select example" name="katmenu">
                                                                    <option selected hidden value="">Pilih Kategori Makanan</option>
                                                                    <?php
                                                                    foreach ($select_kat_menu as $value) {
                                                                        if($row['kategori']==$value['idk']){
                                                                            echo "<option selected value='" . $value['idk'] . "'>" . $value['kategori_menu'] . "</option>";
                                                                        }else{
                                                                            echo "<option value='" . $value['idk'] . "'>" . $value['kategori_menu'] . "</option>";
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="floatingInput">Kategori Makanan dan minuman</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-floating">
                                                                <input disabled type="number" class="form-control" id="floatingInput" value="<?php echo $row['harga'] ?>">
                                                                <label for="floatingInput">Harga</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-floating">
                                                                <input disabled type="number" class="form-control" id="floatingInput" value="<?php echo $row['stok'] ?>">
                                                                <label for="floatingInput">Stok</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <!-- <button type="submit" class="btn btn-primary" name="input_menu">Save changes</button> -->
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--======================================= akhir modal View ===================================================================-->
                                <!--======================================= modal Edit =========================================================================-->
                                <div class="modal fade" id="ModalEdit<?php echo $row['idm'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Menu Makanan Dan Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                                    <input type="hidden" value="<?php echo $row['idm'] ?>" name="id">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="floatingInput" placeholder="Username" name="namamenu" value="<?php echo $row['nama_menu'] ?>" required>
                                                                <label for="floatingInput">Nama Menu</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Nama Menu.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                            <select class="form-select" aria-label="Default select example" name="katmenu" required>
                                                                    <option selected hidden value="">Pilih Kategori Makanan</option>
                                                                    <?php
                                                                    foreach ($select_kat_menu as $value) {
                                                                        if($row['kategori']==$value['idk']){
                                                                            echo "<option selected value='" . $value['idk'] . "'>" . $value['kategori_menu'] . "</option>";
                                                                        }else{
                                                                            echo "<option value='" . $value['idk'] . "'>" . $value['kategori_menu'] . "</option>";
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="floatingInput">Kategori Makanan dan minuman</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Kategori Makanan.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="floatingInput" placeholder="keterangan" name="keterangan" value="<?php echo $row['keterangan'] ?>" required>
                                                                <label for="floatingPassword">Keterangan</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Keterangan Makanan.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="number" class="form-control" id="floatingInput" placeholder="harga" name="harga" value="<?php echo $row['harga'] ?>" required>
                                                                <label for="floatingInput">Harga</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Harga.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="number" class="form-control" id="floatingInput" placeholder="stok" name="stok" value="<?php echo $row['stok'] ?>" required>
                                                                <label for="floatingInput">Stok</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Stok.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary" name="update_menu">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!----------------------------------------- akhir edit ----------------------------------------------------------------------------->
                                <!--======================================= modal Edit foto =========================================================================-->
                                <div class="modal fade" id="ModalEditfoto<?php echo $row['idm'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Foto Menu</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                                    <input type="hidden" value="<?php echo $row['idm'] ?>" name="id">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="input-group mb-3">
                                                                <input id="input" type="file" class="form-control py-3" id="uploadfoto" placeholder="Nama" name="foto" value="<?php echo $row['foto_menu'] ?>" required>
                                                                <label class="input-group-text" for="uploadfoto">Upload Foto Menu</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Foto Menu.
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="input-group mb-3">
                                                                        <img id="img" src="../image/Choose.png" class="rounded mx-auto d-block" height="200">
                                                                    </div>
                                                                        <script>
                                                                            let img = document.getElementById('img');
                                                                            let input = document.getElementById('input');
        
                                                                            input.onchange = (e) => {
                                                                                if (input.files[0])
                                                                                    img.src = URL.createObjectURL(input.files[0]);
                                                                            };
                                                                        </script>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary" name="update_foto">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!----------------------------------------- akhir edit foto ----------------------------------------------------------------------------->

                                <!--======================================= modal Delete =========================================================================-->
                                <div class="modal fade" id="ModalDelete<?php echo $row['idm'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Makanan Atau Minuman</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                    <input type="hidden" value="<?php echo $row['idm'];?>" name="idm">
                                                    <input type="hidden" value="<?php echo $row['foto_menu'];?>" name="foto_menu">
                                                    <div class="col-lg-12">
                                                        Apakah Anda Ingin Menghapus Menu <b><?php echo $row['nama_menu']?></b>
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