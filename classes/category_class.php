<?php
/**
 * Category Class - Handles all category database operations
 * File: classes/category_class.php
 */

require_once '../settings/db_class.php';

class Category extends db_connection
{
    public function __construct()
    {
        // Ensure database connection is established
        if (!$this->db_connect()) {
            error_log("Failed to connect to database in Category class");
            die("Database connection failed");
        }
    }

    /**
     * Add new category
     * @param string $category_name - The name of the category
     * @param int $user_id - The ID of the user creating the category
     * @return int|false - Returns category ID on success, false on failure
     */
    public function addCategory($category_name, $user_id)
    {
        try {
            // Validate inputs
            $category_name = trim($category_name);
            
            if (empty($category_name)) {
                error_log("Category name is empty");
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id: " . var_export($user_id, true));
                return false;
            }

            // Check if database connection exists
            if ($this->db === null) {
                error_log("Database connection is null in addCategory");
                return false;
            }

            // Check if category name already exists for this user
            if ($this->categoryExists($category_name, $user_id)) {
                error_log("Category already exists: " . $category_name . " for user: " . $user_id);
                return false;
            }

            // Prepare statement
            $stmt = $this->db->prepare("INSERT INTO categories (cat_name, user_id) VALUES (?, ?)");
            
            if (!$stmt) {
                error_log("Prepare failed in addCategory: " . $this->db->error);
                return false;
            }

            // Bind parameters - string for category name, integer for user_id
            $stmt->bind_param("si", $category_name, $user_id);
            
            // Execute statement
            if ($stmt->execute()) {
                $insert_id = $this->db->insert_id;
                error_log("SUCCESS: Category added with ID: " . $insert_id);
                $stmt->close();
                return $insert_id;
            } else {
                error_log("Execute failed in addCategory: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in addCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all categories for a specific user
     * @param int $user_id - The user's ID
     * @return array - Array of categories
     */
    public function getCategoriesByUser($user_id)
    {
        try {
            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in getCategoriesByUser: " . var_export($user_id, true));
                return array();
            }

            if ($this->db === null) {
                error_log("Database connection is null in getCategoriesByUser");
                return array();
            }

            $stmt = $this->db->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY cat_name");
            
            if (!$stmt) {
                error_log("Prepare failed in getCategoriesByUser: " . $this->db->error);
                return array();
            }

            $stmt->bind_param("i", $user_id);
            
            if (!$stmt->execute()) {
                error_log("Execute failed in getCategoriesByUser: " . $stmt->error);
                $stmt->close();
                return array();
            }

            $result = $stmt->get_result();
            $categories = array();
            
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            $stmt->close();
            return $categories;
        } catch (Exception $e) {
            error_log("Exception in getCategoriesByUser: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Get single category by ID
     * @param int $cat_id - Category ID
     * @param int $user_id - User ID (for security)
     * @return array|null - Category data or null
     */
    public function getCategoryById($cat_id, $user_id)
    {
        try {
            if (empty($cat_id) || !is_numeric($cat_id)) {
                error_log("Invalid cat_id in getCategoryById: " . var_export($cat_id, true));
                return null;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in getCategoryById: " . var_export($user_id, true));
                return null;
            }

            if ($this->db === null) {
                error_log("Database connection is null in getCategoryById");
                return null;
            }

            $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in getCategoryById: " . $this->db->error);
                return null;
            }

            $stmt->bind_param("ii", $cat_id, $user_id);
            
            if (!$stmt->execute()) {
                error_log("Execute failed in getCategoryById: " . $stmt->error);
                $stmt->close();
                return null;
            }

            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Exception in getCategoryById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update category
     * @param int $cat_id - Category ID
     * @param string $category_name - New category name
     * @param int $user_id - User ID (for security)
     * @return bool - True on success, false on failure
     */
    public function updateCategory($cat_id, $category_name, $user_id)
    {
        try {
            $category_name = trim($category_name);
            
            if (empty($category_name)) {
                error_log("Category name is empty in updateCategory");
                return false;
            }

            if (empty($cat_id) || !is_numeric($cat_id)) {
                error_log("Invalid cat_id in updateCategory: " . var_export($cat_id, true));
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in updateCategory: " . var_export($user_id, true));
                return false;
            }

            if ($this->db === null) {
                error_log("Database connection is null in updateCategory");
                return false;
            }

            // Check if new name already exists for this user (excluding current category)
            $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND user_id = ? AND cat_id != ?");
            
            if (!$stmt) {
                error_log("Prepare failed in updateCategory (check): " . $this->db->error);
                return false;
            }

            $stmt->bind_param("sii", $category_name, $user_id, $cat_id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                error_log("Category name already exists in updateCategory: " . $category_name);
                $stmt->close();
                return false;
            }
            $stmt->close();

            // Update the category
            $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in updateCategory (update): " . $this->db->error);
                return false;
            }

            $stmt->bind_param("sii", $category_name, $cat_id, $user_id);
            
            if ($stmt->execute()) {
                error_log("SUCCESS: Category updated - ID: " . $cat_id);
                $stmt->close();
                return true;
            } else {
                error_log("Execute failed in updateCategory: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in updateCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete category
     * @param int $cat_id - Category ID
     * @param int $user_id - User ID (for security)
     * @return bool - True on success, false on failure
     */
    public function deleteCategory($cat_id, $user_id)
    {
        try {
            if (empty($cat_id) || !is_numeric($cat_id)) {
                error_log("Invalid cat_id in deleteCategory: " . var_export($cat_id, true));
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in deleteCategory: " . var_export($user_id, true));
                return false;
            }

            if ($this->db === null) {
                error_log("Database connection is null in deleteCategory");
                return false;
            }

            $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in deleteCategory: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("ii", $cat_id, $user_id);
            
            if ($stmt->execute()) {
                $affected_rows = $stmt->affected_rows;
                error_log("SUCCESS: Category deleted - ID: " . $cat_id . ", Rows affected: " . $affected_rows);
                $stmt->close();
                return true;
            } else {
                error_log("Execute failed in deleteCategory: " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Exception in deleteCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if category name exists for user
     * @param string $category_name - Category name to check
     * @param int $user_id - User ID
     * @return bool - True if exists, false otherwise
     */
    public function categoryExists($category_name, $user_id)
    {
        try {
            $category_name = trim($category_name);
            
            if (empty($category_name)) {
                error_log("Category name is empty in categoryExists");
                return false;
            }

            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in categoryExists: " . var_export($user_id, true));
                return false;
            }

            if ($this->db === null) {
                error_log("Database connection is null in categoryExists");
                return false;
            }

            $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in categoryExists: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("si", $category_name, $user_id);
            
            if (!$stmt->execute()) {
                error_log("Execute failed in categoryExists: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $exists = $stmt->get_result()->num_rows > 0;
            $stmt->close();
            
            return $exists;
        } catch (Exception $e) {
            error_log("Exception in categoryExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get category count for a user
     * @param int $user_id - User ID
     * @return int - Number of categories
     */
    public function getCategoryCount($user_id)
    {
        try {
            if (empty($user_id) || !is_numeric($user_id)) {
                error_log("Invalid user_id in getCategoryCount: " . var_export($user_id, true));
                return 0;
            }

            if ($this->db === null) {
                error_log("Database connection is null in getCategoryCount");
                return 0;
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM categories WHERE user_id = ?");
            
            if (!$stmt) {
                error_log("Prepare failed in getCategoryCount: " . $this->db->error);
                return 0;
            }

            $stmt->bind_param("i", $user_id);
            
            if (!$stmt->execute()) {
                error_log("Execute failed in getCategoryCount: " . $stmt->error);
                $stmt->close();
                return 0;
            }

            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            return (int)$result['count'];
        } catch (Exception $e) {
            error_log("Exception in getCategoryCount: " . $e->getMessage());
            return 0;
        }
    }
}
?>