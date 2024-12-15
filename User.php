<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT * FROM user_login");
$result = array();



while ($record = mysqli_fetch_array($query)) {
    $result[] = $record;
}

// <-----------------------------input user----------------------------------------->
if (isset($_POST['input_user'])) {
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $NoTlp = $_POST['NoTlp'];
    $emal = $_POST['email'];

    // Pengecekan apakah username sudah ada di database
    $checkQuery = "SELECT * FROM user_login WHERE username = '$username'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Username sudah digunakan. Silakan gunakan username lain.');
                            window.history.back()</script>";
        exit();
    }

    $query = "INSERT INTO user_login (nama, username, email,  password, NoTlp) VALUES ('$nama', '$username', '$email', '" . password_hash($password, PASSWORD_DEFAULT) . "', '$NoTlp')";
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

// <------------------------------------------update user-------------------------------------->
if (isset($_REQUEST['update_user'])) {
    $id = $_REQUEST['id'];
    $username = $_REQUEST['username'];
    $nama = $_REQUEST['nama'];
    $level = $_REQUEST['level'];
    $Telpon = $_REQUEST['NoTlp'];
    $alamat = $_REQUEST['alamat'];
    $email = $_REQUEST['email'];

    // Pengecekan apakah username sudah ada di database
    $checkQuery = "SELECT * FROM user_login WHERE username = '$username' AND id != '$id'";
    $checkResult = mysqli_query($Koneksi, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Username sudah digunakan, Silakan gunakan username lain.');
                            window.history.back()</script>";
        exit();
    }

    $query = "UPDATE user_login SET nama='$nama', username='$username', email='$email', NoTlp='$Telpon', level='$level', alamat='$alamat' WHERE id='$id'";
    $result = mysqli_query($Koneksi, $query);

    if ($result) {
        echo "<script>alert('Data anda telah Di Update');
                window.history.back()</script>";
        exit();
    } else {
        echo "<script>alert('Terjadi Kesalahan Saat Memperbarui Data User');
                window.history.back();</script>";
    }

}


// <=================================hapus user====================================>
// if (isset($_REQUEST['hapus_user'])) {
//     $id = $_REQUEST['id'];


//     $query = mysqli_query($Koneksi, "DELETE FROM user_login WHERE id='$id'");

//     if ($query) {
//         echo "<script>alert('Data anda telah Di Hapus');
//                 window.history.back()</script>";
//     } else {
//         echo "<script>alert('Terjadi Kesalahan Saat Menghapus Data User');
//                 window.history.back()</script>";
//     }

// }
//////////////////////////////////hapus user edit////////////////////////////////////////////
if (isset($_REQUEST['hapus_user'])) {
    $id = $_REQUEST['id'];

    // Mulai dengan menghapus user berdasarkan id
    $deleteQuery = "DELETE FROM user_login WHERE id = ?";
    $statement = $Koneksi->prepare($deleteQuery);
    $statement->bind_param('i', $id);
    
    if ($statement->execute()) {
        // Setelah berhasil menghapus, kita akan memperbarui ID agar berurutan kembali
        $updateQuery = "SET @num := 0; 
                        UPDATE user_login SET id = (@num := @num + 1) ORDER BY id; 
                        ALTER TABLE user_login AUTO_INCREMENT = 1;";
        
        if (mysqli_multi_query($Koneksi, $updateQuery)) {
            echo "<script>alert('Data user telah dihapus dan ID diatur ulang.');
                    window.history.back();</script>";
        } else {
            echo "<script>alert('Pengaturan ulang ID gagal.');
                    window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus data user.');
                window.history.back();</script>";
    }
}


// <================================= reset password ====================================>

session_start();
include('Dikoneksi.php');

if (isset($_POST['reset_pass'])) {
    $id = $_POST['id'];
    $password = 'Password'; // Ganti 'password' dengan kata sandi yang ingin Anda hash

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE user_login SET password = '$hashedPassword' WHERE id = '$id'";
    $result = mysqli_query($Koneksi, $query);

    if ($result) {
        echo "<script>alert('Password berhasil direset');
                window.history.back()</script>";
    } else {
        echo "<script>alert('Password gagal direset');
                window.history.back()</script>";
    }

}

?>


<div class="col-lg-9 mt-2">
    <div class="card">
        <div class="card-header">
            Halaman User
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalTambahUser">Tambah User+</button>
                </div>
            </div>
            <!----------------------------------- Modal user baru ------------------------>
            <div class="modal fade" id="ModalTambahUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah User</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="Nama" name="nama" required>
                                            <label for="floatingInput">Nama</label>
                                            <div class="invalid-feedback">
                                                Masukkan Nama.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="Username" name="username" required>
                                            <label for="floatingInput">Username</label>
                                            <div class="invalid-feedback">
                                                Masukkan Username.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" aria-label="Default select example" name="level" required>
                                                <option selected hidden value="0">Pilih level User</option>
                                                <option value="1">Owner/Admin</option>
                                                <option value="2">Kasir</option>
                                                <option value="3">Pelayan</option>
                                                <option value="4">Dapur</option>
                                                <option value="5">User/pelanggan</option>
                                            </select>
                                            <label for="floatingInput">Level User</label>
                                            <div class="invalid-feedback">
                                                Pilih Level User.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="floatingInput" placeholder="08xxxx" name="NoTlp" required>
                                            <label for="floatingInput">No. Telfon</label>
                                            <div class="invalid-feedback">
                                                Masukkan No. Tlp.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="floatingInput" placeholder="Password" name="email" required>
                                            <label for="floatingPassword">Email</label>
                                            <div class="invalid-feedback">
                                                Masukkan Email Anda.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="floatingInput" placeholder="Password" name="password" required>
                                            <label for="floatingPassword">Password</label>
                                            <div class="invalid-feedback">
                                                Masukkan Password Anda.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-floating">
                                    <textarea class="form-control" id="" style="height:100px" name="alamat" required></textarea>
                                    <!-- <input type="password" class="form-control" id="floatingPassword" placeholder="Password"> -->
                                    <label for="floatingPassword">Alamat</label>
                                    <div class="invalid-feedback">
                                        Masukkan Alamat.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="input_user">Save changes</button>
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
                                <th scope="col">Nama</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Level</th>
                                <th scope="col">No. Tlp</th>
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
                                    <td><?php echo $row['nama'] ?></td>
                                    <td><?php echo $row['username'] ?></td>
                                    <td><?php echo $row['email'] ?></td>
                                    <td>
                                        <?php
                                        if ($row['level'] == 1) {
                                            echo "Admin";
                                        } elseif ($row['level'] == 2) {
                                            echo "Kasir";
                                        } elseif ($row['level'] == 3) {
                                            echo "Pelayan";
                                        } elseif ($row['level'] == 4) {
                                            echo "Dapur";
                                        }elseif ($row['level'] == 5) {
                                            echo "User/pelanggan";
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $row['NoTlp'] ?></td>
                                    <td class="d-flex">
                                        <button class="btn btn-info btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalView<?php echo $row['id'] ?>"><img src="../image/eye.svg"></button>
                                        <button class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalEdit<?php echo $row['id'] ?>"><img src="../image/pencil-square.svg"></button>
                                        <button class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#ModalDelete<?php echo $row['id'] ?>"><img src="../image/trash.svg"></button>
                                        <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#Modalresetpass<?php echo $row['id'] ?>"><i class="bi bi-key"></i></button>
                                    </td>
                                </tr>

                                <!--------------- Modal View -------------------->
                                <div class="modal fade" id="ModalView<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Data User</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input disabled type="text" class="form-control not_allowed" id="floatingInput" placeholder="Nama" name="nama" value="<?php echo $row['nama']; ?>">
                                                                <label for="floatingInput">Nama</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input disabled type="text" class="form-control not_allowed" id="floatingInput" placeholder="Username" name="username" value="<?php echo $row['username']; ?>">
                                                                <label for="floatingInput">Username</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="form-floating mb-3">
                                                                <select disabled class="form-select not_allowed" aria-label="Default select example" name="level" id="">
                                                                <?php
                                                                $data = array("Owner/Admin","Kasir","Pelayan","Dapur", "User");
                                                                foreach ($data as $key => $value) {
                                                                    if ($row['level'] == $key + 1) {
                                                                        echo "<option selected value='$key'>$value</option>";
                                                                    } else {
                                                                        echo "<option value='$key'>$value</option>";
                                                                    }
                                                                }
                                                                ?>
                                                                </select>
                                                                <label for="floatingInput">Level User</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <div class="form-floating mb-3">
                                                                <input disabled type="number" class="form-control not_allowed" id="floatingInput" placeholder="08xxxx" name="NoTlp" value="<?php echo $row['NoTlp']; ?>">
                                                                <label for="floatingInput">No. Telfon</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                        <div class="form-floating mb-3">
                                                                <input disabled class="form-control not_allowed" id="floatingInput" placeholder="08xxxx" name="Email" value="<?php echo $row['email'] ?>">
                                                                <label for="floatingInput">Email</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="input-group form-floating mb-3">
                                                            <input disabled type="password" class="form-control" id="floatingPassword<?php echo $row['id'] ?>" placeholder="Password" name="password" value="<?php echo $_SESSION['pass'] ?>" required readonly>
                                                            <label for="floatingPassword<?php echo $row['id'] ?>">Password</label>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text toggle-password" style="cursor: pointer;" id="togglePasswordModal<?php echo $row['id'] ?>">
                                                                    <img src="../image/eye-close.png" alt="Toggle Password" width="57">
                                                                </span>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-floating">
                                                        <textarea disabled class="form-control not_allowed" id="" style="height:100px" name="alamat" required><?php echo $row['alamat']; ?></textarea>
                                                        <label for="floatingPassword">Alamat</label>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                document.getElementById("togglePasswordModal<?php echo $row['id'] ?>").addEventListener("click", function() {
                                    var passwordInput = document.getElementById("floatingPassword<?php echo $row['id'] ?>");
                                    var passwordIcon = document.getElementById("togglePasswordModal<?php echo $row['id'] ?>");

                                    if (passwordInput.type === "password") {
                                        passwordInput.type = "text";
                                        passwordIcon.innerHTML = '<img src="../image/eye-open.png" alt="Toggle Password" height="44" width="57">';
                                    } else {
                                        passwordInput.type = "password";
                                        passwordIcon.innerHTML = '<img src="../image/eye-close.png" alt="Toggle Password" width="57">';
                                    }
                                });
                            </script>
                                <!--======================================= akhir modal View ===================================================================-->

                                <!--======================================= modal Edit =========================================================================-->
                                <div class="modal fade" id="ModalEdit<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data User</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST" class="needs-validation" novalidate>
                                                <input type="hidden" value="<?php echo $row['id'];?>" name="id">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input type="text" class="form-control" id="floatingInput" placeholder="Nama" name="nama" value="<?php echo $row['nama']; ?>" required>
                                                                <label for="floatingInput">Nama</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Nama.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-floating mb-3">
                                                                <input <?php echo ($row['username'] == $_SESSION['user']) ? 'disabled' : '';?> type="text" class="form-control" id="floatingInput" placeholder="Username" name="username" value="<?php echo $row['username']; ?>" required>
                                                                <label for="floatingInput">Username</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Username.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <div class="form-floating mb-3">
                                                                <select class="form-select" aria-label="Default select example" name="level" id="" required>
                                                                <?php
                                                                $data = array("Owner/Admin","Kasir","Pelayan","Dapur","User");
                                                                foreach ($data as $key => $value) {
                                                                    if ($row['level'] == $key + 1) {
                                                                        echo "<option selected value=".($key + 1).">$value</option>";
                                                                    } else {
                                                                        echo "<option value=".($key + 1).">$value</option>";
                                                                    }
                                                                }
                                                                ?>
                                                                </select>
                                                                <label for="floatingInput">Level User</label>
                                                                    <div class="invalid-feedback">
                                                                        Pilih Level User
                                                                    </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <div class="form-floating mb-3">
                                                                <input type="number" class="form-control" id="floatingInput" placeholder="08xxxx" name="NoTlp" value="<?php echo $row['NoTlp']; ?>" required>
                                                                <label for="floatingInput">No. Telfon</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan No. Tlp.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-floating mb-3">
                                                                <input type="email" class="form-control" id="floatingInput" placeholder="Password" name="email" value="<?php echo $row['email'] ?>" required>
                                                                <label for="floatingPassword">Email</label>
                                                                <div class="invalid-feedback">
                                                                    Masukkan Email Anda.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-floating">
                                                        <textarea class="form-control" id="" style="height:100px" name="alamat" required><?php echo $row['alamat']; ?></textarea>
                                                        <label for="floatingPassword">Alamat</label>
                                                        <div class="invalid-feedback">
                                                            Masukkan Alamat.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary" name="update_user">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- akhir edit -->

                                <!--======================================= modal Delete =========================================================================-->
                                <div class="modal fade" id="ModalDelete<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Data User</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                <input type="hidden" value="<?php echo $row['id'];?>" name="id">
                                                    <div class="col-lg-12">
                                                        <?php
                                                        if($row['username'] == $_SESSION['user']){
                                                            echo "<div class='alert alert-danger'>Anda tidak dapat menghapus akun sendiri</div>";
                                                        }else{
                                                            echo "Apakah Anda Ingin Menghapus <b>$row[username]</b>";
                                                        }
                                                        ?>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger" name="hapus_user" <?php echo ($row['username'] == $_SESSION['user']) ? 'disabled' : '';?>>Hapus</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- akhir Delete -->

                                <!--======================================= modal reset password =========================================================================-->
                                <div class="modal fade" id="Modalresetpass<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Reset Password</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="" method="POST">
                                                <input type="hidden" value="<?php echo $row['id'];?>" name="id">
                                                    <div class="col-lg-12">
                                                        <?php
                                                        if($row['username'] == $_SESSION['user']){
                                                            echo "<div class='alert alert-danger'>Anda tidak dapat mereset password sendiri</div>";
                                                        }else{
                                                            echo "Apakah Anda Ingin Mereset password <b>$row[username]</b> menjadi password bawaan yaitu <b>Password</b>??";
                                                        }
                                                        ?>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success" name="reset_pass" <?php echo ($row['username'] == $_SESSION['user']) ? 'disabled' : '';?>>Reset Password</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- akhir reset password -->
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