<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow">
                <div class="card-body">

                    <h3 class="text-center mb-4">Quên mật khẩu</h3>

                    <form action="process_forgot_password.php" method="POST">

                        <div class="mb-3">
                            <label>Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <button class="btn btn-primary w-100">
                            Đổi mật khẩu
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>