
<?php
require_once "../Model/Database.php";

$db = new Database();

// lấy id từ URL
$id = $_GET['id'] ?? 'NV001';

// query nhân viên
$sql = "SELECT * FROM Employee WHERE employee_id = '$id'";
$emp = $db->getOne($sql);

// nếu không có dữ liệu
if (!$emp) {
    die("Không tìm thấy nhân viên");
}

$sqlAcc = "SELECT status FROM Account WHERE employee_id = '$id'";
$acc = $db->getOne($sqlAcc);

$status = $acc['status'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $employee_id = $_POST['employee_id'];

    $db->connect()->query("
        UPDATE Account
        SET status = 'Active'
        WHERE employee_id = '$employee_id'
    ");

    // giả lập gửi mail (sau này nâng cấp SMTP)
    // sendMail($employee_id);

    header("Location: ../View/employeedetail.php?id=$employee_id");
    exit();
}
   
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin chi tiết nhân viên</title>
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
        <div class="menu-item active" onclick="window.location.href='../View/employeelist.php'"> <i class="fa-solid fa-user"></i> Thông tin nhân sự</div>
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

    <!-- nội dung chính -->
    <main class="main">

        <div class="employee-layout">
            <!-- LEFT -->
            <div class="employee-detail">

                <!-- Header nhỏ -->
                <div class="employee-top">
                    <button class="btn-toolbar" onclick="window.location.href='../View/employeelist.php'">&lt; Go back</button>

                    <div class="top-actions">
                        <button class="btn-toolbar">Chỉnh sửa</button>
                        <button class="btn-toolbar">Thôi việc</button>
                    </div>
                </div>

                <!-- Card -->
                <div class="employee-card">

                    <!-- Profile -->
                    <div class="employee-profile">
                        <div class="employee-avatar"></div>

                        <div>
                            <div class="employee-name"><?= $emp['full_name'] ?></div>
                            <div class="employee-id"><?= $emp['employee_id'] ?></div>
                            <?php
                            if ($emp['work_status'] == 'Đang làm việc') {
                                $statusClass = 'employee-status';
                            } elseif ($emp['work_status'] == 'Sắp nghỉ việc') {
                                $statusClass = 'employee-status-warning';
                            } else {
                                $statusClass = 'employee-status1';
                            }
                            ?>

                            <div class="<?= $statusClass ?>">
                                <?= $emp['work_status'] ?>
                            </div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="employee-info">

                        <div class="employee-item">
                            <span>Ngày sinh</span>
                            <span><?= date('d/m/Y', strtotime($emp['date_of_birth'])) ?></span>
                        </div>

                        <div class="employee-item">
                            <span>Giới tính</span>
                            <span><?= $emp['gender'] ?></span>
                        </div>

                        <div class="employee-item">
                            <span>Số điện thoại</span>
                            <span><?= $emp['phone_number'] ?></span>
                        </div>

                        <div class="employee-item">
                            <span>Email</span>
                            <span><?= $emp['email'] ?></span>
                        </div>

                        <div class="employee-item">
                            <span>Số CCCD</span>
                            <span><?= $emp['identity_card_number'] ?></span>
                        </div>

                        <div class="employee-item">
                            <span>Địa chỉ</span>
                            <span><?= $emp['address'] ?></span>
                        </div>

                        <div class="employee-item">
                            <span>Dân tộc</span>
                            <span><?= $emp['ethnic_group'] ?></span>
                        </div>

                    </div>

                    <!-- Bottom button -->
                    <?php if ($status == 'Pending'): ?>
                        <button class="btn-activate">Kích hoạt tài khoản</button>

                    <?php elseif ($status == 'Active'): ?>
                        <button class="btn-inactivate">Đã kích hoạt tài khoản</button>

                    <?php elseif ($status == 'Locked'): ?>
                        <button class="btn-unactivate">Tài khoản đã khoá</button>

                    <?php endif; ?>
                </div>
            </div>
            <!-- RIGHT -->
            <div class="employee-work">

                <!-- Tabs -->
                <div class="tabs">
                    <div class="tab active">Thông tin công việc</div>
                    <div class="tab">Thông tin tài khoản</div>
                    <div class="tab">Chấm công & Lương</div>
                </div>

                <!-- GRID -->
                <div class="work-grid">

                    <!-- LEFT CARD -->
                    <div class="work-card">
                        <h3>Vị trí & Tổ chức</h3>

                        <div class="field">
                            <label>Phòng ban</label>
                            <div class="value"><?= $emp['department'] ?></div>
                        </div>

                        <div class="field">
                            <label>Chức vụ</label>
                            <div class="value"><?= $emp['position'] ?></div>
                        </div>

                        <div class="field">
                            <label>Loại hình nhân sự</label>
                            <div class="value"><?= $emp['employee_type'] ?></div>
                        </div>

                        <div class="field">
                            <label>Ngày vào làm</label>
                            <div class="value">
                                <?= date('d/m/Y', strtotime($emp['start_date'])) ?>
                            </div>
                        </div>

                        <div class="field">
                            <label>Ngày ký HĐ chính thức</label>
                            <div class="value">
                                <?= $emp['contract_date'] ? date('d/m/Y', strtotime($emp['contract_date'])) : 'N/A' ?>
                            </div>
                        </div>

                        <div class="field">
                            <label>Ngày hết hạn hợp đồng</label>
                            <div class="value">
                                <?= $emp['end_date'] ? date('d/m/Y', strtotime($emp['end_date'])) : 'N/A' ?>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT TOP -->
                    <div class="work-card">
                        <h3>Trình độ & Chuyên môn</h3>

                        <div class="field">
                            <label>Trình độ học vấn</label>
                            <div class="value"><?= $emp['education_level'] ?></div>
                        </div>

                        <div class="field">
                            <label>Chuyên ngành</label>
                            <div class="value"><?= $emp['major'] ?></div>
                        </div>

                        <div class="field">
                            <label>Trình độ ngoại ngữ</label>
                            <div class="value"><?= $emp['foreign_language'] ?></div>
                        </div>
                    </div>

                    <!-- RIGHT BOTTOM -->
                    <div class="work-card">
                        <h3>Tài liệu đính kèm</h3>

                        <div class="file">
                            <span><?= $emp['contract_file_url'] ?></span>
                        </div>

                        <div class="file">
                            <span><?= $emp['degree_file_url'] ?></span>
                        </div>

                        <div class="file">
                            <span><?= $emp['certificate_file_url'] ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <form method="POST" enctype="multipart/form-data">
    <div class="modal-overlay">
    <div class="modal1">
        <div class="modal-header">
            <h2>Kích hoạt tài khoản</h2>
            <span class="close" onclick="window.location.href='../View/employeedetail.php?id=<?php echo $emp['employee_id']; ?>'">×</span>
        </div>

        <div class="modal1-body">
            <p style="margin-top: 10px; font-size: 16px; line-height: 1.6;">Bạn có chắc chắn muốn kích hoạt tài khoản nhân viên <b><?= $emp['full_name'] ?></b>?
            </p>

            <p style="font-size: 16px; line-height: 1.6;">
                Sau khi xác nhận, hệ thống sẽ gửi thông tin tài khoản tới email cho nhân viên.
            </p>

            <input type="hidden" name="employee_id" value="<?= $emp['employee_id'] ?>">

        </div>

        <div class="modal-footer">
            <button 
                type="button" 
                class="btn" 
                onclick="window.location.href='../View/employeedetail.php?id=<?php echo $emp['employee_id']; ?>'">
                Huỷ
            </button>
            <button type="submit" class="btn primary">Xác nhận</button>
        </div>

    </div>
    </div>
    </form>
</div>

</body>
</html>