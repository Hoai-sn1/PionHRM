<?php
require_once "../Model/Database.php";

$db = new Database();

// lấy dữ liệu nhân viên
$sql = "SELECT * FROM Employee";
$data = $db->getAll($sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách nhân viên</title>
    <link rel="stylesheet" href="../asset/HRM.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
        <img src="../asset/logoPion.jpg" alt="Logo">
        </div>
        <div class="menu-item"><i class="fa-solid fa-house"></i> Bảng điều khiển</div>
        <div class="menu-item active"> <i class="fa-solid fa-user"></i> Thông tin nhân sự</div>
        <div class="menu-item"> <i class="fa-solid fa-file-lines"></i> Đơn xin nghỉ</div>
        <div class="menu-item"> <i class="fa-solid fa-calendar-check"></i> Chấm công</div>
        <div class="menu-item"> <i class="fa-solid fa-file-invoice"></i> Biến động lương </div>
        <div class="menu-item"> <i class="fa-solid fa-dollar-sign"></i> Tiền lương</div>
    </div>

    <!-- Header -->
    <header class="header">
        <h1>Danh sách nhân viên</h1>

        <div class="header-right">
                        <button class="btnheader"> <i class="fa-solid fa-sun fa-lg"></i></button>
            <button class="btnheader"> <i class="fa-solid fa-bell fa-lg"></i></button>
            <div class="user">
                <div class="avatar">CN</div>
                <span>Nguyễn Văn A</span>
            </div>
        </div>
    </header>

    <!-- Table -->
    <main class="main has-toolbar">
        <div class="toolbar">
            <input class="search-box" placeholder="Tìm kiếm...">

            <div class="actions">
                        <button class="btn-toolbar" onclick="window.location.href='../View/createemployee.php'"> <i class="fa-solid fa-plus"></i> Thêm nhân viên</button>
                <button class="btn-toolbar"> <i class="fa-solid fa-bars"></i> Action</button>
            </div>
        </div>

        <div class="table">

            <div class="table-header">
                <div>STT</div>
                <div>Mã NV</div>
                <div>Tên nhân viên</div>
                <div>Email</div>
                <div>SĐT</div>
                <div>Phòng ban</div>
                <div>Tình trạng</div>
                <div>Action</div>
            </div>

            <!-- LOOP DATA -->
            <?php 
            $stt = 1;
            foreach ($data as $row): 
            ?>
                <div class="table-row">
                    <div><?= $stt++ ?></div>
                    <div><?= $row['employee_id'] ?></div>
                    <div><?= $row['full_name'] ?></div>
                    <div><?= $row['email'] ?></div>
                    <div><?= $row['phone_number'] ?></div>
                    <div><?= $row['department'] ?></div>

                <div>
                    <?php if ($row['work_status'] == 'Đang làm việc'): ?>
                        <span class="status working">Đang làm việc</span>

                    <?php elseif ($row['work_status'] == 'Sắp nghỉ việc'): ?>
                        <span class="status warning">Sắp nghỉ việc</span>

                    <?php else: ?>
                        <span class="status off">Đã nghỉ việc</span>
                    <?php endif; ?>
                </div>

                    <div class="actions">
                        <button onclick="window.location.href='../View/employeedetail.php?id=<?= $row['employee_id'] ?>'"><i class="fa-solid fa-eye"></i></button>
                        <button onclick="window.location.href='../View/updateemployee.php?id=<?= $row['employee_id'] ?>'"><i class="fa-solid fa-pen"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    </main>

</div>

</body>
</html>