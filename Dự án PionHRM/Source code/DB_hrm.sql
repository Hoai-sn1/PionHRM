DROP DATABASE IF EXISTS hr_management;
CREATE DATABASE hr_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE hr_management;

-- ========================
-- 1. EMPLOYEE
-- ========================
CREATE TABLE Employee (
    employee_id VARCHAR(8) PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('Nam','Nữ','Khác'),
    date_of_birth DATE NOT NULL,
    phone_number VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100),
    identity_card_number VARCHAR(12) UNIQUE NOT NULL,
    address VARCHAR(255),
    ethnic_group VARCHAR(50) DEFAULT 'Kinh',
    position VARCHAR(100),
    department VARCHAR(100),
    work_status ENUM('Đang làm việc','Đã nghỉ việc') DEFAULT 'Đang làm việc',
    manager VARCHAR(8),
    employee_type VARCHAR(50),
    start_date DATE NOT NULL,
    contract_date DATE,
    end_date DATE,
    education_level VARCHAR(100),
    major VARCHAR(100),
    foreign_language VARCHAR(255),
    contract_file_url VARCHAR(500),
    degree_file_url VARCHAR(500),
    certificate_file_url VARCHAR(500),
    base_salary DECIMAL(15,2) DEFAULT 0,
    annual_leave_limit INT DEFAULT 12,
    FOREIGN KEY (manager) REFERENCES Employee(employee_id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- TRIGGER check end_date lớn hơn start_date

DELIMITER $$

CREATE TRIGGER check_end_date_before_insert
BEFORE INSERT ON Employee
FOR EACH ROW
BEGIN
    IF NEW.end_date IS NOT NULL AND NEW.end_date <= NEW.start_date THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'end_date must be greater than start_date';
    END IF;
END$$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER check_end_date_before_update
BEFORE UPDATE ON Employee
FOR EACH ROW
BEGIN
    IF NEW.end_date IS NOT NULL AND NEW.end_date <= NEW.start_date THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'end_date must be greater than start_date';
    END IF;
END$$

DELIMITER ;

-- ========================
-- 2. TIMESHEET
-- ========================
CREATE TABLE Timesheet (
    timesheet_id INT AUTO_INCREMENT PRIMARY KEY,
    period DATE NOT NULL,
    working_days DECIMAL(4,1),
    status_timesheet ENUM('Open','Closed') DEFAULT 'Open',
    holiday_days INT DEFAULT 0
) ENGINE=InnoDB;

-- ========================
-- 3. PAYROLL
-- ========================
CREATE TABLE Payroll (
    payroll_id INT AUTO_INCREMENT PRIMARY KEY,
    period DATE UNIQUE NOT NULL,
    status ENUM('Nháp','Đã duyệt') DEFAULT 'Nháp',
    approval_date DATETIME
) ENGINE=InnoDB;

-- ========================
-- 4. ACCOUNT
-- ========================
CREATE TABLE Account (
    account_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(8) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin','Manager','Employee') DEFAULT 'Employee',
    status ENUM('Active','Locked','Pending') DEFAULT 'Pending',

    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ========================
-- 5. ATTEND RECORD
-- ========================
CREATE TABLE AttendRecord (
    attend_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(8) NOT NULL,
    work_date DATE NOT NULL,
    check_in DATETIME,
    check_out DATETIME,

    UNIQUE (employee_id, work_date),

    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ========================
-- 6. LEAVE REQUEST
-- ========================
CREATE TABLE LeaveRequest (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(8) NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    leave_date DATE NOT NULL,
    leave_shift ENUM('Sáng','Chiều','Cả ngày'),
    reason_leave VARCHAR(255),
    evidence_url VARCHAR(500),
    status_leave ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',

    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ========================
-- 7. PAYROLL VARIATION
-- ========================
CREATE TABLE PayrollVariation (
    variation_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(8) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    applied_period DATE NOT NULL,
    variation_type ENUM('Bonus','Deduction'),
    amount DECIMAL(15,2) NOT NULL,
    reason VARCHAR(255),

    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ========================
-- 8. RESIGNATION
-- ========================
CREATE TABLE Resignation (
    resign_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(8) NOT NULL,
    resign_type VARCHAR(50) NOT NULL,
    resign_reason VARCHAR(255),
    end_working_date DATE NOT NULL,

    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ========================
-- 9. TIMESHEET DETAIL
-- ========================
CREATE TABLE TimesheetDetail (
    timesheet_id INT,
    employee_id VARCHAR(8),
    actual_working_days DECIMAL(4,1) DEFAULT 0,
    overtime_hours DECIMAL(5,2) DEFAULT 0,
    paid_leave_days DECIMAL(4,1) DEFAULT 0,
    annual_leave_days DECIMAL(4,1) DEFAULT 0,
    unpaid_leave_days DECIMAL(4,1) DEFAULT 0,
    late_count INT DEFAULT 0,
    forgot_checkin_count INT DEFAULT 0,

    PRIMARY KEY (timesheet_id, employee_id),

    FOREIGN KEY (timesheet_id) REFERENCES Timesheet(timesheet_id)
    ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ========================
-- 10. PAYROLL DETAIL
-- ========================
CREATE TABLE PayrollDetail (
    payroll_id INT,
    employee_id VARCHAR(8),
    actual_salary DECIMAL(15,2) DEFAULT 0,
    fixed_allowance DECIMAL(15,2) DEFAULT 0,
    attendance_allowance DECIMAL(15,2) DEFAULT 0,
    overtime_pay DECIMAL(15,2) DEFAULT 0,
    other_bonus DECIMAL(15,2) DEFAULT 0,
    insurance_deduction DECIMAL(15,2) DEFAULT 0,
    other_deduction DECIMAL(15,2) DEFAULT 0,

    PRIMARY KEY (payroll_id, employee_id),

    FOREIGN KEY (payroll_id) REFERENCES Payroll(payroll_id)
    ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES Employee(employee_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ========================
-- INDEX (TỐI ƯU)
-- ========================
CREATE INDEX idx_employee_name ON Employee(full_name);
CREATE INDEX idx_attendance_date ON AttendRecord(work_date);
CREATE INDEX idx_payroll_period ON Payroll(period);