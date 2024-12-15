<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT * FROM tb_kategori_menu");
$result = array();

while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}

// <-----------------------------input kategori menu----------------------------------------->
if (isset($_POST['input_menu'])) {
    $jenismenu = $_POST['jenismenu'];
    $katmenu = $_POST['katmenu'];

    // Pengecekan apakah username sudah ada di database
    $checkQuery = "SELECT kategori_menu FROM tb_kategori_menu WHERE kategori_menu = '$katmenu'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Kategori sudah digunakan. Silakan gunakan kategori lain.');
                            window.history.back()</script>";
        exit();
    }

    $query = "INSERT INTO tb_kategori_menu (jenis_menu, kategori_menu) VALUES ('$jenismenu', '$katmenu')";
    $result = mysqli_query($Koneksi, $query);

    if ($result) {
        echo "<script>alert('Data anda telah tersimpan!');
                window.history.back();
                </script>";
        exit();
    } else {
        echo "<script>alert('Terjadi kesalahan dalam menyimpan data.');</script>";
    }
}

// <------------------------------------------edit kategori menu-------------------------------------->
if (isset($_POST['update_menu'])) {
    $id = $_POST['id'];
    $jenismenu = $_POST['level']; // Ubah nama input field sesuai dengan HTML Anda
    $katmenu = $_POST['katmenu'];

    // Pengecekan apakah kategori sudah ada di database
    $checkQuery = "SELECT kategori_menu FROM tb_kategori_menu WHERE kategori_menu = '$katmenu' AND idk != '$id'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Kategori sudah digunakan, silakan gunakan yang lain.');
                window.history.back()</script>";
        exit();
    }

    $query = "UPDATE tb_kategori_menu SET jenis_menu='$jenismenu', kategori_menu='$katmenu' WHERE idk='$id'";
    $result = mysqli_query($Koneksi, $query);

    if ($result) {
        echo "<script>alert('Data anda telah Di Update');
                window.history.back()</script>";
        exit();
    } else {
        echo "<script>alert('Terjadi Kesalahan Saat Memperbarui Data Kategori Menu');
                window.history.back();</script>";
    }

    exit();
}
// <=================================hapus kategori menu====================================>
if (isset($_POST['hapus_kategori_menu'])) {
    $id = $_POST['id'];

    $query = mysqli_query($Koneksi, "DELETE FROM tb_kategori_menu WHERE idk='$id'");
    $checkQuery = "SELECT kategori FROM tb_menu WHERE kategori = '$id'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    if ($query) {
        if (mysqli_num_rows($checkResult)) {
            $message = "<script>alert('Kategori Telah Digunakan Pada Daftar Menu, Kategori Tidak Dapat Dihapus');
                window.history.back()</script>";
        } else {
            echo "<script>alert('Data anda telah Di Hapus');
                window.history.back()</script>";
        }
    } else {
        echo "<script>alert('Terjadi Kesalahan Saat Menghapus Data kategori Menu');
                window.history.back()</script>";
    }

    exit();
}
?>


<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman Kategori Menu
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambahUser">Tambah Kategori+</button>
                </div>
            </div>
            <!----------------------------------- Modal user baru ------------------------>
            <div class="modal fade" id="ModalTambahUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Kategori Menu</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="jenismenu" id="" required>
                                            <option selected hidden value="0">Pilih Level Jenis Menu</option>
                                                <option value="1">Makanan</option>
                                                <option value="2">Minuman</option>
                                            </select>
                                            <label for="floatingInput">Jenis Menu</label>
                                            <div class="invalid-feedback">
                                                Masukkan Jenis Makanan.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="kategori manu" name="katmenu" required>
                                            <label for="floatingInput">Kategori Menu</label>
                                            <div class="invalid-feedback">
                                                Masukkan Kategori Menu.
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
            <!-- akhir user baru -->

            <?php
            if (empty($result)) {
                echo "Data Tidak Ditemukan";
            } else {
            ?>
                <div class="table-responsive mt-2">
                    <table class="table table-hover" id="example">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Jenis Menu</th>
                                <th scope="col">Kategori Menu</th>
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
                                    <td><?php echo ($row['jenis_menu']== 1) ? "Makanan" : "Minuman"?></td></td>
                                    <td><?php echo $row['kategori_menu'] ?></td>
                                    <td class="d-flex">
                                        <?php if ($hasil['level'] == 1 || $hasil['level'] == 2) { ?>
                                            <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['idk'] ?>"><img src="../image/pencil-square.svg"></button>
                                            <button class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['idk'] ?>"><img src="../image/trash.svg"></button>
                                        <?php } else { ?>
                                            <button class="btn btn-warning btn-sm me-1" disabled data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['idk'] ?>"><img src="../image/pencil-square.svg"></button>
                                            <button class="btn btn-danger btn-sm me-1" disabled data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['idk'] ?>"><img src="../image/trash.svg"></button>
                                        <?php } ?>
                                    </td>
                                </tr>

                                <!--======================================= modal Edit =========================================================================-->
                                <div class="modal fade" id="ModalEdit<?php echo $row['idk'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Kategori Menu</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST" class="needs-validation" novalidate>
                                                    <input type="hidden" value="<?php echo $row['idk'];?>" name="id">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <!-- Ubah nama input field sesuai dengan HTML Anda -->
                                                                <select class="form-select" aria-label="Default select example" name="level" id="" required>
                                                                    <?php
                                                                    $data = array("Makanan", "Minuman");
                                                                    foreach ($data as $key => $value) {
                                                                        if ($row['jenis_menu'] == $key + 1) {
                                                                            echo "<option selected value='$key'>$value</option>";
                                                                        } else {
                                                                            echo "<option value='$key'>$value</option>";
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <label for="floatingInput">Jenis Menu</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Jenis Makanan.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="floatingInput" placeholder="kategori menu" name="katmenu" value="<?php echo $row['kategori_menu'] ?>" required>
                                                                <label for="floatingInput">Kategori Menu</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Kategori Menu.
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
                                <!-- akhir edit -->

                                <!--======================================= modal Delete =========================================================================-->
                                <div class="modal fade" id="ModalDelete<?php echo $row['idk'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Data Kategori</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                    <input type="hidden" value="<?php echo $row['idk'];?>" name="id">
                                                    <div class="col-lg-12">
                                                        Apakah Anda Ingin Menghapus Kategori <b><?php echo $row['kategori_menu']?></b>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger" name="hapus_kategori_menu">Hapus</button>
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