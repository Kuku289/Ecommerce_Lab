<?php
/**
 * Category Controller - Business logic layer for categories
 * File: controllers/category_controller.php
 */

require_once '../classes/category_class.php';

/**
 * Add a new category
 * @param string $category_name - Category name
 * @param int $user_id - User ID
 * @return int|false - Category ID on success, false on failure
 */
function add_category_ctr($category_name, $user_id)
{
    // Validate inputs
    if (empty($category_name) || empty($user_id)) {
        error_log("add_category_ctr: Empty category_name or user_id");
        return false;
    }
    
    // Validate category name length
    $category_name = trim($category_name);
    if (strlen($category_name) < 2) {
        error_log("add_category_ctr: Category name too short");
        return false;
    }
    
    if (strlen($category_name) > 100) {
        error_log("add_category_ctr: Category name too long");
        return false;
    }
    
    // Create category object and add category
    $category = new Category();
    $result = $category->addCategory($category_name, $user_id);
    
    if ($result) {
        error_log("add_category_ctr: Successfully added category with ID: " . $result);
    } else {
        error_log("add_category_ctr: Failed to add category");
    }
    
    return $result;
}

/**
 * Get all categories for a user
 * @param int $user_id - User ID
 * @return array|false - Array of categories or false
 */
function get_categories_by_user_ctr($user_id)
{
    if (empty($user_id)) {
        error_log("get_categories_by_user_ctr: Empty user_id");
        return false;
    }
    
    $category = new Category();
    return $category->getCategoriesByUser($user_id);
}

/**
 * Get a single category by ID
 * @param int $cat_id - Category ID
 * @param int $user_id - User ID
 * @return array|false - Category data or false
 */
function get_category_by_id_ctr($cat_id, $user_id)
{
    if (empty($cat_id) || empty($user_id)) {
        error_log("get_category_by_id_ctr: Empty cat_id or user_id");
        return false;
    }
    
    $category = new Category();
    return $category->getCategoryById($cat_id, $user_id);
}

/**
 * Update a category
 * @param int $cat_id - Category ID
 * @param string $category_name - New category name
 * @param int $user_id - User ID
 * @return bool - True on success, false on failure
 */
function update_category_ctr($cat_id, $category_name, $user_id)
{
    if (empty($cat_id) || empty($category_name) || empty($user_id)) {
        error_log("update_category_ctr: Empty parameters");
        return false;
    }
    
    // Validate category name length
    $category_name = trim($category_name);
    if (strlen($category_name) < 2 || strlen($category_name) > 100) {
        error_log("update_category_ctr: Invalid category name length");
        return false;
    }
    
    $category = new Category();
    return $category->updateCategory($cat_id, $category_name, $user_id);
}

/**
 * Delete a category
 * @param int $cat_id - Category ID
 * @param int $user_id - User ID
 * @return bool - True on success, false on failure
 */
function delete_category_ctr($cat_id, $user_id)
{
    if (empty($cat_id) || empty($user_id)) {
        error_log("delete_category_ctr: Empty cat_id or user_id");
        return false;
    }
    
    $category = new Category();
    return $category->deleteCategory($cat_id, $user_id);
}

/**
 * Check if a category exists
 * @param string $category_name - Category name
 * @param int $user_id - User ID
 * @return bool - True if exists, false otherwise
 */
function check_category_exists_ctr($category_name, $user_id)
{
    if (empty($category_name) || empty($user_id)) {
        error_log("check_category_exists_ctr: Empty category_name or user_id");
        return false;
    }
    
    $category = new Category();
    return $category->categoryExists($category_name, $user_id);
}

/**
 * Get category count for a user
 * @param int $user_id - User ID
 * @return int - Number of categories
 */
function get_category_count_ctr($user_id)
{
    if (empty($user_id)) {
        error_log("get_category_count_ctr: Empty user_id");
        return 0;
    }
    
    $category = new Category();
    return $category->getCategoryCount($user_id);
}

/**
 * Get ALL categories (not just for a specific user)
 * Used for dropdowns in product management
 * @return array|false - Array of all categories or false
 */
function get_all_categories_ctr()
{
    try {
        require_once(dirname(__FILE__) . '/../settings/db_class.php');
        
        $db = new db_connection();
        if (!$db->db_connect()) {
            error_log("get_all_categories_ctr: Database connection failed");
            return false;
        }
        
        $sql = "SELECT * FROM categories ORDER BY cat_name";
        $result = $db->db->query($sql);
        
        if (!$result) {
            error_log("get_all_categories_ctr: Query failed - " . $db->db->error);
            return false;
        }
        
        $categories = array();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        
        return $categories;
    } catch (Exception $e) {
        error_log("get_all_categories_ctr: Exception - " . $e->getMessage());
        return false;
    }
}
?>