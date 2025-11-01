<?php
/**
 * Brand Controller - Business logic layer for brands
 * File: controllers/brand_controller.php
 */

require_once '../classes/brand_class.php';

/**
 * Add a new brand
 * @param string $brand_name - Brand name
 * @param int $cat_id - Category ID
 * @param int $user_id - User ID
 * @return int|false - Brand ID on success, false on failure
 */
function add_brand_ctr($brand_name, $cat_id, $user_id)
{
    if (empty($brand_name) || empty($cat_id) || empty($user_id)) {
        error_log("add_brand_ctr: Empty parameters");
        return false;
    }
    
    $brand_name = trim($brand_name);
    if (strlen($brand_name) < 2 || strlen($brand_name) > 100) {
        error_log("add_brand_ctr: Invalid brand name length");
        return false;
    }
    
    $brand = new Brand();
    $result = $brand->addBrand($brand_name, $cat_id, $user_id);
    
    if ($result) {
        error_log("add_brand_ctr: Successfully added brand with ID: " . $result);
    } else {
        error_log("add_brand_ctr: Failed to add brand");
    }
    
    return $result;
}

/**
 * Get all brands for a user
 * @param int $user_id - User ID
 * @return array|false - Array of brands or false
 */
function get_brands_by_user_ctr($user_id)
{
    if (empty($user_id)) {
        error_log("get_brands_by_user_ctr: Empty user_id");
        return false;
    }
    
    $brand = new Brand();
    return $brand->getBrandsByUser($user_id);
}

/**
 * Get a single brand by ID
 * @param int $brand_id - Brand ID
 * @param int $user_id - User ID
 * @return array|false - Brand data or false
 */
function get_brand_by_id_ctr($brand_id, $user_id)
{
    if (empty($brand_id) || empty($user_id)) {
        error_log("get_brand_by_id_ctr: Empty parameters");
        return false;
    }
    
    $brand = new Brand();
    return $brand->getBrandById($brand_id, $user_id);
}

/**
 * Update a brand
 * @param int $brand_id - Brand ID
 * @param string $brand_name - New brand name
 * @param int $user_id - User ID
 * @return bool - True on success, false on failure
 */
function update_brand_ctr($brand_id, $brand_name, $user_id)
{
    if (empty($brand_id) || empty($brand_name) || empty($user_id)) {
        error_log("update_brand_ctr: Empty parameters");
        return false;
    }
    
    $brand_name = trim($brand_name);
    if (strlen($brand_name) < 2 || strlen($brand_name) > 100) {
        error_log("update_brand_ctr: Invalid brand name length");
        return false;
    }
    
    $brand = new Brand();
    return $brand->updateBrand($brand_id, $brand_name, $user_id);
}

/**
 * Delete a brand
 * @param int $brand_id - Brand ID
 * @param int $user_id - User ID
 * @return bool - True on success, false on failure
 */
function delete_brand_ctr($brand_id, $user_id)
{
    if (empty($brand_id) || empty($user_id)) {
        error_log("delete_brand_ctr: Empty parameters");
        return false;
    }
    
    $brand = new Brand();
    return $brand->deleteBrand($brand_id, $user_id);
}

/**
 * Check if a brand + category combination exists
 * @param string $brand_name - Brand name
 * @param int $cat_id - Category ID
 * @param int $user_id - User ID
 * @return bool - True if exists, false otherwise
 */
function check_brand_category_exists_ctr($brand_name, $cat_id, $user_id)
{
    if (empty($brand_name) || empty($cat_id) || empty($user_id)) {
        error_log("check_brand_category_exists_ctr: Empty parameters");
        return false;
    }
    
    $brand = new Brand();
    return $brand->brandCategoryExists($brand_name, $cat_id, $user_id);
}

/**
 * Get ALL brands (not just for a specific user)
 * Used for dropdowns in product management
 * @return array|false - Array of all brands with category names or false
 */
function get_all_brands_ctr()
{
    try {
        require_once(dirname(__FILE__) . '/../settings/db_class.php');
        
        $db = new db_connection();
        if (!$db->db_connect()) {
            error_log("get_all_brands_ctr: Database connection failed");
            return false;
        }
        
        $sql = "SELECT b.*, c.cat_name 
                FROM brands b 
                LEFT JOIN categories c ON b.cat_id = c.cat_id 
                ORDER BY c.cat_name, b.brand_name";
        
        $result = $db->db->query($sql);
        
        if (!$result) {
            error_log("get_all_brands_ctr: Query failed - " . $db->db->error);
            return false;
        }
        
        $brands = array();
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }
        
        return $brands;
    } catch (Exception $e) {
        error_log("get_all_brands_ctr: Exception - " . $e->getMessage());
        return false;
    }
}
?>