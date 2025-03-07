<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Auth.php';

class User {
    private $conn;
    private $table = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    // ✅ Register User
    public function register($username, $email, $password) {
        if (empty($username) || empty($email) || empty($password)) {
            return ["status" => 400, "message" => "All fields are required"];
        }

    // ✅ Check if username or email already exists
    try {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE username = :username OR email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':username' => $username,
            ':email' => $email
        ]);
        $count = $stmt->fetchColumn(); // Get the number of matching users

        if ($count > 0) {
            return ["status" => 409, "message" => "Username or email already exists"];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table . " (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);

       
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            $query1 = "SELECT * FROM " . $this->table . " WHERE email = :email";
            $stmt = $this->conn->prepare($query1);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $token = Auth::generateToken($user);
            // return ["status" => 200, "message" => "Login successful",];

            return ["status" => 201, "message" => "User registered successfully", "token" => $token];
        } catch (Exception $e) {
            return ["status" => 500, "message" => "Error: " . $e->getMessage()];
        }
    }

    // ✅ Login User
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ["status" => 400, "message" => "Email and password are required"];
        }

        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $token = Auth::generateToken($user);
            return ["status" => 200, "message" => "Login successful", "token" => $token];
        }

        return ["status" => 401, "message" => "Invalid email or password"];
    }
}
