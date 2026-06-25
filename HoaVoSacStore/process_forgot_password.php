<?php

$conn = new mysqli("localhost", "root", "", "HoaVoSacStore");

if ($conn->connect_error) {
    die("Lỗi kết nối!");
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$new_password = $_POST['new_password'];

$sql = "SELECT * FROM users WHERE username=? AND email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $hashed_password = password_hash(
        $new_password,
        PASSWORD_DEFAULT
    );

    $update = $conn->prepare(
        "UPDATE users SET password=? WHERE username=?"
    );

    $update->bind_param(
        "ss",
        $hashed_password,
        $username
    );

    if ($update->execute()) {

        echo "<script>
                alert('Đổi mật khẩu thành công!');
                window.location='login.php';
              </script>";

    } else {

        echo "<script>
                alert('Có lỗi xảy ra!');
                history.back();
              </script>";

    }

} else {

    echo "<script>
            alert('Sai tên đăng nhập hoặc email!');
            history.back();
          </script>";

}

$conn->close();
?>