<?php

require_once dirname(__FILE__) . '/../settings/db_class.php';

/**
 * User class for handling customer operations
 */
class User extends db_connection
{
    private $user_id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $date_created;
    private $phone_number;
    private $country;
    private $city;

    public function __construct($user_id = null)
    {
        parent::db_connect();
        if ($user_id) {
            $this->user_id = $user_id;
            $this->loadUser();
        }
    }

    private function loadUser($user_id = null)
    {
        if ($user_id) {
            $this->user_id = $user_id;
        }
        if (!$this->user_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['customer_name'];
            $this->email = $result['customer_email'];
            $this->role = $result['user_role'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
            $this->phone_number = $result['customer_contact'];
            $this->country = isset($result['customer_country']) ? $result['customer_country'] : null;
            $this->city = isset($result['customer_city']) ? $result['customer_city'] : null;
        }
        return $result ? true : false;
    }

    public function createUser($name, $email, $password, $phone_number, $role, $country = null, $city = null)
    {
        // Check if email exists first
        if ($this->emailExists($email)) {
            return false;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if ($country && $city) {
            $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, user_role, customer_country, customer_city) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssiss", $name, $email, $hashed_password, $phone_number, $role, $country, $city);
        } else {
            $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, user_role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $email, $hashed_password, $phone_number, $role);
        }
        
        if ($stmt->execute()) {
            return mysqli_insert_id($this->db);
        }
        return false;
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Login user with email and password
     */
    public function loginUser($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result && password_verify($password, $result['customer_pass'])) {
            return $result;
        }
        return false;
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Validate user input for registration
     */
    public function validateRegistrationInput($data)
    {
        $errors = array();

        // Validate name
        if (empty($data['name']) || strlen($data['name']) < 2 || strlen($data['name']) > 100) {
            $errors[] = 'Name must be between 2-100 characters';
        }

        // Validate email
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        // Validate password
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }

        // Validate phone number
        if (empty($data['phone_number']) || !preg_match('/^[0-9+\-\s]{10,15}$/', $data['phone_number'])) {
            $errors[] = 'Valid phone number is required (10-15 digits)';
        }

        // Validate country (if provided)
        if (!empty($data['country']) && (strlen($data['country']) < 2 || strlen($data['country']) > 50)) {
            $errors[] = 'Country must be between 2-50 characters';
        }

        // Validate city (if provided)
        if (!empty($data['city']) && (strlen($data['city']) < 2 || strlen($data['city']) > 50)) {
            $errors[] = 'City must be between 2-50 characters';
        }

        return $errors;
    }

    /**
     * Update user information
     */
    public function updateUser($user_id, $data)
    {
        $updates = array();
        $types = "";
        $values = array();

        if (isset($data['name'])) {
            $updates[] = "customer_name = ?";
            $types .= "s";
            $values[] = $data['name'];
        }

        if (isset($data['phone_number'])) {
            $updates[] = "customer_contact = ?";
            $types .= "s";
            $values[] = $data['phone_number'];
        }

        if (isset($data['country'])) {
            $updates[] = "customer_country = ?";
            $types .= "s";
            $values[] = $data['country'];
        }

        if (isset($data['city'])) {
            $updates[] = "customer_city = ?";
            $types .= "s";
            $values[] = $data['city'];
        }

        if (empty($updates)) {
            return array('status' => 'error', 'message' => 'No data to update');
        }

        $values[] = $user_id;
        $types .= "i";

        $sql = "UPDATE customer SET " . implode(", ", $updates) . " WHERE customer_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return array('status' => 'success', 'message' => 'User updated successfully');
        } else {
            return array('status' => 'error', 'message' => 'Update failed: ' . $this->db->error);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            return array('status' => 'success', 'message' => 'User deleted successfully');
        } else {
            return array('status' => 'error', 'message' => 'Delete failed: ' . $this->db->error);
        }
    }

    // Getter methods
    public function getUserId() { return $this->user_id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getPhoneNumber() { return $this->phone_number; }
    public function getCountry() { return $this->country; }
    public function getCity() { return $this->city; }
    public function getDateCreated() { return $this->date_created; }
}
?>