<?php
session_start();
include "../Dikoneksi.php";

$query = mysqli_query($Koneksi, "SELECT * FROM user_login WHERE username = '$_SESSION[user]'");
$record = mysqli_fetch_array($query);

//////////////////// ubah password ///////////////////////
if (isset($_POST['ubah_password'])) {
    $passwordlama = $_POST['passwordlama'];
    $passwordbaru = $_POST['passwordbaru'];
    $passwordulang = $_POST['passwordulang'];

    $userQuery = mysqli_query($Koneksi, "SELECT * FROM user_login WHERE username = '$_SESSION[user]'");
    $user = mysqli_fetch_assoc($userQuery);

    if (password_verify($passwordlama, $user['password'])) {
        if ($passwordbaru == $passwordulang) {
            $hashedPassword = password_hash($passwordbaru, PASSWORD_DEFAULT);

            $updateQuery = mysqli_query($Koneksi, "UPDATE user_login SET password = '$hashedPassword' WHERE username = '$_SESSION[user]'");

            if ($updateQuery) {
                $message = "<script>alert('Password berhasil diubah');
                            window.history.back()</script>";
            } else {
                $message = "<script>alert('Terjadi kesalahan saat mengubah password');
                            window.history.back()</script>";
            }
        } else {
            $message = "<script>alert('Password baru tidak sesuai dengan konfirmasi');
                        window.history.back()</script>";
        }
    } else {
        $message = "<script>alert('Password lama yang dimasukkan salah');
                    window.history.back()</script>";
    }

    echo $message;
    exit();
}

/////////////////////////// ubah profil //////////////////////////////
if (isset($_POST['ubah_profil'])) {
    $nama = $_POST['nama'];
    $nohp = $_POST['nohp'];
    $alamat = $_POST['alamat'];

    $updateQuery = mysqli_query($Koneksi, "UPDATE user_login SET nama = '$nama', NoTlp = '$nohp', alamat = '$alamat' WHERE username = '$_SESSION[user]'");

    if ($updateQuery) {
        $message = "<script>alert('Data Profil berhasil Dirubah');
                    window.history.back()</script>";
    } else {
        $message = "<script>alert('Data Profil Gagal Dirubah');
                    window.history.back()</script>";
    }

    echo $message;
    exit();
}
?>

<nav class="navbar navbar-expand navbar-dark sticky-top" style="background-color: #40b0bf;">
    <div class="container-lg">
        <a class="navbar-brand" href=""><img src="../image/food-network.png" width="60px" height="60px"> Waroeng Modern Bites<a>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav">
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="hidden-xs"><?PHP ECHO $_SESSION['user']; ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end mt-2">
                        <li><a class="dropdown-item" href="../"><i class="bi bi-house-door"></i> Home</a></li>
                        <li><a class="dropdown-item" href="../Profil" data-bs-toggle="modal" data-bs-target="#ModalPerson<?php echo $row['id'] ?>"><i class="bi bi-person-square"></i> Profil</a></li>
                        <li><a class="dropdown-item" href="../Setting"><i class="bi bi-gear"></i> Setting</a></li>
                        <li><a class="dropdown-item" href="../Profil" data-bs-toggle="modal" data-bs-target="#ModalUPassword<?php echo $row['id'] ?>"><i class="bi bi-key"></i></i> Ubah Password</a></li>
                        <li><a class="dropdown-item" href="../Logout"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
                        
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <!--======================================= modal ubah password =========================================================================-->
<div class="modal fade" id="ModalUPassword<?php echo $row['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Password</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-floating mb-3">
                                <input disabled type="text" class="form-control not_allowed" id="floatingInput" placeholder="Username" name="username" value="<?php echo $_SESSION['user'] ?>">
                                <label for="floatingInput">Username</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group form-floating mb-3">
                                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="passwordlama" value="<?php echo $_SESSION['pass'] ?>" required>
                                <label for="floatingPassword">Password Lama</label>
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" style="cursor: pointer;" id="togglePassword">
                                        <img src="../image/eye-close.png" alt="Toggle Password" width="57">
                                    </span>
                                </div>
                                <div class="invalid-feedback">
                                    Masukkan Password Lama.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-group form-floating mb-3">
                                <input type="password" class="form-control" id="floatingPasswordNew" placeholder="Password Baru" name="passwordbaru" required>
                                <label for="floatingPasswordNew">Password Baru</label>
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" style="cursor: pointer;" id="togglePasswordNew">
                                        <img src="../image/eye-close.png" alt="Toggle Password" width="57">
                                    </span>
                                </div>
                                <div class="invalid-feedback">
                                    Masukkan Password Baru.
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group form-floating mb-3">
                                <input type="password" class="form-control" id="floatingPasswordConfirm" placeholder="Ulangi Password Baru" name="passwordulang" required>
                                <label for="floatingPasswordConfirm">Ulangi Password Baru</label>
                                <div class="input-group-append">
                                    <span class="input-group-text toggle-password" style="cursor: pointer;" id="togglePasswordConfirm">
                                        <img src="../image/eye-close.png" alt="Toggle Password" width="57">
                                    </span>
                                </div>
                                <div class="invalid-feedback">
                                    Ulangi Password Baru.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="ubah_password">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById("togglePassword").addEventListener("click", function() {
        togglePasswordVisibility("floatingPassword", "togglePassword");
    });

    document.getElementById("togglePasswordNew").addEventListener("click", function() {
        togglePasswordVisibility("floatingPasswordNew", "togglePasswordNew");
    });

    document.getElementById("togglePasswordConfirm").addEventListener("click", function() {
        togglePasswordVisibility("floatingPasswordConfirm", "togglePasswordConfirm");
    });

    function togglePasswordVisibility(passwordInputId, passwordIconId) {
        var passwordInput = document.getElementById(passwordInputId);
        var passwordIcon = document.getElementById(passwordIconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordIcon.innerHTML = '<img src="../image/eye-open.png" alt="Toggle Password" width="57" height="44">';
        } else {
            passwordInput.type = "password";
            passwordIcon.innerHTML = '<img src="../image/eye-close.png" alt="Toggle Password" width="57">';
        }
    }
</script>
<!-- akhir ubah password -->


<!--======================================= modal Profil =========================================================================-->
<div class="modal fade" id="ModalPerson" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Profile</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-floating mb-3">
                                <input disabled type="text" class="form-control not_allowed" id="floatingInput" placeholder="Username" name="username" value="<?php echo $_SESSION['user'] ?>">
                                <label for="floatingInput">Username</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingNama" placeholder="Username" name="nama" value="<?php echo $record['nama']; ?>" required>
                                <label for="floatingInput">Nama</label>
                                <div class="invalid-feedback">
                                Masukkan Nama Anda.
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="floatingInput" placeholder="Username" name="nohp" value="<?php echo $record['NoTlp']; ?>" required>
                                <label for="floatingInput">Nomor HP</label>
                                <div class="invalid-feedback">
                                Masukkan Nomor Hp Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="floatingInput" placeholder="Username" name="nohp" value="<?php echo $record['email']; ?>" required>
                                <label for="floatingInput">Email</label>
                                <div class="invalid-feedback">
                                Masukkan Email Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="" style="height:100px" name="alamat" required><?php echo $record['alamat']; ?></textarea>
                                <label for="floatingInput">Alamat</label>
                                <div class="invalid-feedback">
                                Masukkan Alamat Email Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="ubah_profil">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- akhir  profil-->
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