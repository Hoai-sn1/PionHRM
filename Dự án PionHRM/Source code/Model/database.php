<?php

class Database {
    private $hostname = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname   = "hr_management";
    private $conn;

    // =========================
    // KẾT NỐI DATABASE
    // =========================
    public function connect() {
        $this->conn = new mysqli(
            $this->hostname,
            $this->username,
            $this->password,
            $this->dbname
        );

        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
        return $this->conn;
    }

    // =========================
    // EXECUTE QUERY (SELECT)
    // =========================
    public function query($sql) {
        $conn = $this->connect();
        $result = $conn->query($sql);

        if (!$result) {
            die("Lỗi SQL: " . $conn->error);
        }

        return $result;
    }

    // =========================
    // LẤY NHIỀU DÒNG
    // =========================
    public function getAll($sql) {
        $result = $this->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // =========================
    // LẤY 1 DÒNG
    // =========================
    public function getOne($sql) {
        $result = $this->query($sql);
        return $result->fetch_assoc();
    }

    // =========================
    // KIỂM TRA TỒN TẠI
    // =========================
    public function exists($sql) {
        $result = $this->query($sql);
        return $result->num_rows > 0;
    }

    // =========================
    // INSERT / UPDATE / DELETE
    // =========================
    public function execute($sql) {
        $conn = $this->connect();

        $result = $conn->query($sql);

        if (!$result) {
            die("Lỗi SQL: " . $conn->error);
        }

        return $result;
    }

    // =========================
    // LẤY ID VỪA INSERT
    // =========================
    public function getLastId() {
        return $this->conn->insert_id;
    }
}