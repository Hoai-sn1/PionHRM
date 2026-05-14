<?php
require_once "../Model/Database.php";

$db = new Database();
$conn = $db->connect();

// lấy dữ liệu nhân viên
$sql = "SELECT * FROM Employee";
$data = $db->getAll($sql);

// lấy id từ URL
if (!isset($_GET['id'])) {
    die("Thiếu ID nhân viên");
}

$employee_id = $_GET['id'];

// lấy dữ liệu nhân viên
$sql = "SELECT * FROM Employee WHERE employee_id = '$employee_id'";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Không tìm thấy nhân viên");
}

$employee = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = $_POST['full_name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? null;
    $phone_number = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $identity_card_number = $_POST['identity_card_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $ethnic_group = $_POST['ethnic_group'] ?? 'Kinh';

    $department = $_POST['department'] ?? '';
    $position = $_POST['position'] ?? '';
    $employee_type = $_POST['employee_type'] ?? '';

    $start_date = $_POST['start_date'] ?? null;
    $contract_date = $_POST['contract_date'] ?? null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    $education_level = $_POST['education_level'] ?? '';
    $major = $_POST['major'] ?? '';
    $foreign_language = $_POST['foreign_language'] ?? '';

    $base_salary = $_POST['base_salary'] ?? 0;
    $annual_leave_limit = $_POST['annual_leave_limit'] ?? 12;

    $full_name = $conn->real_escape_string($full_name);
    $address = $conn->real_escape_string($address);
    $position = $conn->real_escape_string($position);
    $ethnic_group = $conn->real_escape_string($ethnic_group);

    $sql = "
    UPDATE Employee SET
        full_name = '$full_name',
        gender = '$gender',
        date_of_birth = '$date_of_birth',
        phone_number = '$phone_number',
        email = '$email',
        identity_card_number = '$identity_card_number',
        address = '$address',
        ethnic_group = '$ethnic_group',
        position = '$position',
        department = '$department',
        employee_type = '$employee_type',
        start_date = '$start_date',
        contract_date = '$contract_date',
        end_date = " . ($end_date === null ? "NULL" : "'$end_date'") . ",
        education_level = '$education_level',
        major = '$major',
        foreign_language = '$foreign_language',
        base_salary = '$base_salary',
        annual_leave_limit = '$annual_leave_limit'
    WHERE employee_id = '$employee_id'
    ";

    $result = $conn->query($sql);

    if (!$result) {
        die("UPDATE ERROR: " . $conn->error);
    }

    header("Location: ../View/employeedetail.php?id=$employee_id");
    exit();
}

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
        <div class="menu-item" onclick="window.location.href='timesheet.html'"> <i class="fa-solid fa-calendar-check"></i> Chấm công</div>
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
                        <button class="btn-toolbar" onclick="window.location.href='../View/create_employee.php'"> <i class="fa-solid fa-plus"></i> Thêm nhân viên</button>
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
                        <button><i class="fa-solid fa-pen"></i></button>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    </main>

    <!-- MODAL -->
    <form method="POST" enctype="multipart/form-data">
    <div class="modal-overlay">
        <div class="modal">

            <div class="modal-header">
                <h2>Sửa thông tin nhân viên</h2>
                <span class="close" onclick="window.location.href='../View/employeelist.php'">×</span>
            </div>

            <div class="modal-body">

                <!-- THÔNG TIN CÁ NHÂN -->
                <div class="form-section">
                    <h3>Thông tin cá nhân</h3>

                    <div class="form-group">
                        <label>Họ và tên*</label>
                        <input type="text" name="full_name" value="<?= $employee['full_name'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="date_of_birth" value="<?= $employee['date_of_birth'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gender">
                            <option value="Nam" <?= $employee['gender'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                            <option value="Nữ" <?= $employee['gender'] == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại*</label>
                        <input type="text" name="phone_number" value="<?= $employee['phone_number'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Email*</label>
                        <input type="email" name="email" value="<?= $employee['email'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Số CCCD</label>
                        <input type="text" name="identity_card_number" value="<?= $employee['identity_card_number'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <input type="text" name="address" value="<?= $employee['address'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Dân tộc</label>
                        <select name="ethnic_group">
                            <option <?= $employee['ethnic_group'] == 'Kinh' ? 'selected' : '' ?>>Kinh</option>
                            <option <?= $employee['ethnic_group'] == 'Tày' ? 'selected' : '' ?>>Tày</option>
                            <option <?= $employee['ethnic_group'] == "H'mông" ? 'selected' : '' ?>>H'mông</option>
                            <option <?= $employee['ethnic_group'] == 'Thái' ? 'selected' : '' ?>>Thái</option>
                            <option <?= $employee['ethnic_group'] == 'Khác' ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>


                </div>

                <!-- THÔNG TIN VỊ TRÍ TỔ CHỨC -->
                <div class="form-section">
                    <h3>Vị trí & Tổ chức</h3>

                    <div class="form-group">
                        <label>Phòng ban *</label>
                        <select name="department">
                            <option <?= $employee['department'] == 'Phát triển' ? 'selected' : '' ?>>Phát triển</option>
                            <option <?= $employee['department'] == 'Hành chính nhân sự' ? 'selected' : '' ?>>Hành chính nhân sự</option>
                            <option <?= $employee['department'] == 'Kinh doanh' ? 'selected' : '' ?>>Kinh doanh</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Chức vụ *</label>
                        <input type="text" name="position" value="<?= $employee['position'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Loại hình nhân sự</label>
                        <input type="text" name="employee_type" value="<?= $employee['employee_type'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Ngày vào làm</label>
                        <input type="date" name="start_date" value="<?= $employee['start_date'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Ngày ký HĐ lao động</label>
                        <input type="date" name="contract_date" value="<?= $employee['contract_date'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Ngày hết hạn hợp đồng</label>
                        <input type="date" name="end_date" value="<?= $employee['end_date'] ?>">
                    </div>

                </div>

                <!-- TRÌNH ĐỘ VÀ CHUYÊN MÔN -->
                <div class="form-section">
                    <h3>Trình độ & Chuyên môn</h3>

                    <div class="form-group">
                        <label>Trình độ học vấn</label>
                        <input type="text" name="education_level" value="<?= $employee['education_level'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Chuyên ngành</label>
                        <input type="text" name="major" value="<?= $employee['major'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Trình độ ngoại ngữ</label>
                        <input type="text" name="foreign_language" value="<?= $employee['foreign_language'] ?>">
                    </div>
                </div>

                <!-- Tài liệu đính kèm -->
                <div class="form-section">
                    <h3>Tài liệu đính kèm</h3>

                    <div class="form-group">
                        <label>Hợp đồng</label>
                        <input type="file" name="contract_file">
                    </div>

                    <div class="form-group">
                        <label>Bằng cấp</label>
                        <input type="file" name="degree_file">
                    </div>

                    <div class="form-group">
                        <label>Chứng chỉ ngoại ngữ</label>
                        <input type="file" name="certificate_file">
                    </div>
                </div>

                <!-- thông tin khác -->
                <div class="form-section">
                    <h3>Thông tin khác</h3>

                    <div class="form-group">
                        <label>Lương cơ bản</label>
                        <input type="text" name="base_salary" value="<?= $employee['base_salary'] ?>">
                    </div>

                    <div class="form-group">
                        <label>Hạn mức nghỉ phép năm</label>
                        <input type="text" name="annual_leave_limit" value="<?= $employee['annual_leave_limit'] ?>">
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn"
                onclick="window.location.href='../View/employeelist.php'">Huỷ</button>
                <button type="submit" class="btn primary">Xác nhận</button>
            </div>

        </div>
    </div>    
    </form>
</div>


</body>
</html>