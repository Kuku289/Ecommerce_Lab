<?php
/**
 * Brand Class - Handles all brand database operations
 * File: classes/brand_class.php
 */

require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Brand extends db_connection
{
    public function __construct()
    {
        if (!$this->db_connect()) {
            error_log("Failed to connect to database in Brand class");
            die("Database connection failed");
        }
    }

    /**
     * Add new brand
     * @param string $brand_name - The name of the brand
     * @param int $cat_id - The category ID
     * @param int $user_id - The ID of the user creating the brand
     * @return int|false - Returns brand ID on success, false on failure
     */
    public function addBrand($brand_name, $cat_id, $user_id)
    {
        try {
            $brand_name = trim($brand_name);
            
            if (empty($brand_name)) {
                error_log("Brand name is empty");
                return false;
            }

            if (empty($cat_id) || !is_numeric($cat_id)) {
                error_log("Invalid cat_id: " . var_export($cat_id, true));
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id: " . var_export($user_id, true));
                return false;
            }

            if ($this->db === null) {
                error_log("Database connection is null in addBrand");
                return false;
            }

            // Check if brand name + category combination already exists for this user
            if ($this->brandCategoryExists($brand_name, $cat_id, $user_id)) {
                error_log("Brand + Category combination already exists");
                return false;
            }

            $stmt = $this->db->prepare("INSERT INTO brands (brand_name, cat_id, user_id) VALUES (?, ?, ?)");
            
            if (!$stmt) {
                error_log("Prepare failed in addBrand: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("sii", $brand_name, $cat_id, $user_id);
            
            if ($stmt->execute()) {
                $insert_id = $this->db->insert_id;
                error_log("SUCCESS: Brand added with ID: " . $insert_id);
                $stmt->close();
                return $insert_id;
            } else {
                error_log("Execute failed in addBrand: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in addBrand: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all brands for a specific user with category information
     * @param int $user_id - The user's ID
     * @return array - Array of brands with category names
     */
    public function getBrandsByUser($user_id)
    {
        try {
            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in getBrandsByUser");
                return array();
            }

            if ($this->db === null) {
                error_log("Database connection is null");
                return array();
            }

            $stmt = $this->db->prepare("
                SELECT b.*, c.cat_name 
                FROM brands b 
                LEFT JOIN categories c ON b.cat_id = c.cat_id 
                WHERE b.user_id = ? 
                ORDER BY c.cat_name, b.brand_name
            ");
            
            if (!$stmt) {
                error_log("Prepare failed in getBrandsByUser: " . $this->db->error);
                return array();
            }

            $stmt->bind_param("i", $user_id);
            
            if (!$stmt->execute()) {
                error_log("Execute failed in getBrandsByUser: " . $stmt->error);
                $stmt->close();
                return array();
            }

            $result = $stmt->get_result();
            $brands = array();
            
            while ($row = $result->fetch_assoc()) {
                $brands[] = $row;
            }
            
            $stmt->close();
            return $brands;
        } catch (Exception $e) {
            error_log("Exception in getBrandsByUser: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Get single brand by ID
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID (for security)
     * @return array|null - Brand data or null
     */
    public function getBrandById($brand_id, $user_id)
    {
        try {
            if (empty($brand_id) || !is_numeric($brand_id)) {
                error_log("Invalid brand_id in getBrandById");
                return null;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in getBrandById");
                return null;
            }

            if ($this->db === null) {
                error_log("Database connection is null");
                return null;
            }

            $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in getBrandById: " . $this->db->error);
                return null;
            }

            $stmt->bind_param("ii", $brand_id, $user_id);
            
            if (!$stmt->execute()) {
                error_log("Execute failed in getBrandById: " . $stmt->error);
                $stmt->close();
                return null;
            }

            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Exception in getBrandById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update brand
     * @param int $brand_id - Brand ID
     * @param string $brand_name - New brand name
     * @param int $user_id - User ID (for security)
     * @return bool - True on success, false on failure
     */
    public function updateBrand($brand_id, $brand_name, $user_id)
    {
        try {
            $brand_name = trim($brand_name);
            
            if (empty($brand_name)) {
                error_log("Brand name is empty in updateBrand");
                return false;
            }

            if (empty($brand_id) || !is_numeric($brand_id)) {
                error_log("Invalid brand_id in updateBrand");
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in updateBrand");
                return false;
            }

            if ($this->db === null) {
                error_log("Database connection is null");
                return false;
            }

            // Get current brand to check category
            $current_brand = $this->getBrandById($brand_id, $user_id);
            if (!$current_brand) {
                error_log("Brand not found or unauthorized");
                return false;
            }

            // Check if new name + category combination already exists (excluding current brand)
            $stmt = $this->db->prepare("
                SELECT brand_id FROM brands 
                WHERE brand_name = ? AND cat_id = ? AND user_id = ? AND brand_id != ?
            ");
            
            if (!$stmt) {
                error_log("Prepare failed in updateBrand: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("siii", $brand_name, $current_brand['cat_id'], $user_id, $brand_id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                error_log("Brand name already exists in this category");
                $stmt->close();
                return false;
            }
            $stmt->close();

            $stmt = $this->db->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in updateBrand: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("sii", $brand_name, $brand_id, $user_id);
            
            if ($stmt->execute()) {
                error_log("SUCCESS: Brand updated");
                $stmt->close();
                return true;
            } else {
                error_log("Execute failed in updateBrand: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in updateBrand: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete brand
     * @param int $brand_id - Brand ID
     * @param int $user_id - User ID (for security)
     * @return bool - True on success, false on failure
     */
    public function deleteBrand($brand_id, $user_id)
    {
        try {
            if (empty($brand_id) || !is_numeric($brand_id)) {
                error_log("Invalid brand_id in deleteBrand");
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in deleteBrand");
                return false;
            }

            if ($this->db === null) {
                error_log("Database connection is null");
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in deleteBrand: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("ii", $brand_id, $user_id);
            
            if ($stmt->execute()) {
                error_log("SUCCESS: Brand deleted");
                $stmt->close();
                return true;
            } else {
                error_log("Execute failed in deleteBrand: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in deleteBrand: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if brand + category combination exists for user
     * @param string $brand_name - Brand name to check
     * @param int $cat_id - Category ID
     * @param int $user_id - User ID
     * @return bool - True if exists, false otherwise
     */
    public function brandCategoryExists($brand_name, $cat_id, $user_id)
    {
        try {
            $brand_name = trim($brand_name);
            
            if (empty($brand_name)) {
                return false;
            }

            if (empty($cat_id) || !is_numeric($cat_id)) {
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                return false;
            }

            if ($this->db === null) {
                return false;
            }

            $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in brandCategoryExists: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("sii", $brand_name, $cat_id, $user_id);
            
            if (!$stmt->execute()) {
                error_log("Execute failed in brandCategoryExists: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $exists = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            
            return $exists;
        } catch (Exception $e) {
            error_log("Exception in brandCategoryExists: " . $e->getMessage());
            return false;
        }
    }
}
?>