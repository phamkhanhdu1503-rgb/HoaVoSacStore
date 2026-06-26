<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành công - Hoa Vô Sắc</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #fff8fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;
        }

        .success-card {
            width: 100%;
            max-width: 550px;
            background: #fff;
            border-radius: 20px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, .08);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin: auto;
            border-radius: 50%;
            background: #e8fff1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 25px;
        }

        .success-icon i {
            font-size: 55px;
            color: #28a745;
        }

        h2 {
            color: #a50920;
            font-weight: 700;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            line-height: 1.7;
        }

        .btn-home {
            background: #a50920;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            margin-top: 25px;
            transition: .25s;
        }

        .btn-home:hover {
            background: #870718;
            color: #fff;
        }
    </style>

</head>

<body>

    <div class="success-card">

        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>

        <h2>Đăng ký thành công!</h2>

        <p>
            Cảm ơn bạn đã đăng ký nhận tư vấn tại
            <strong>Hoa Vô Sắc</strong>.
            <br><br>
            Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất để hỗ trợ lựa chọn bó hoa phù hợp nhất.
        </p>

        <a href="index.php" class="btn btn-home">
            <i class="bi bi-house-door-fill"></i>
            Quay về Trang Chủ
        </a>

    </div>

</body>

</html>