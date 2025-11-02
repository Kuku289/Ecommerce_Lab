<?php
require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Product extends db_connection {
    
    // Add a new product - returns product_id
    public function add_product($category_id, $brand_id, $title, $price, $description, $image, $keywords) {
        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) 
                VALUES ('$category_id', '$brand_id', '$title', '$price', '$description', '$image', '$keywords')";
        
        $result = $this->db_query($sql);
        
        if ($result) {
            return $this->db_conn()->insert_id;
        }
        
        return false;
    }
    
    // ⭐ LAB REQUIRED: View all products
    public function view_all_products() {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_id DESC";
        return $this->db_fetch_all($sql);
    }
    
    // ⭐ LAB REQUIRED: Search products by query
    public function search_products($query) {
        $query = $this->db_conn()->real_escape_string($query);
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_title LIKE '%$query%' 
                   OR p.product_desc LIKE '%$query%' 
                   OR p.product_keywords LIKE '%$query%'
                ORDER BY p.product_id DESC";
        return $this->db_fetch_all($sql);
    }
    
    // ⭐ LAB REQUIRED: Filter products by category
    public function filter_products_by_category($cat_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = '$cat_id' 
                ORDER BY p.product_id DESC";
        return $this->db_fetch_all($sql);
    }
    
    // ⭐ LAB REQUIRED: Filter products by brand
    public function filter_products_by_brand($brand_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand = '$brand_id' 
                ORDER BY p.product_id DESC";
        return $this->db_fetch_all($sql);
    }
    
    // ⭐ LAB REQUIRED: View single product
    public function view_single_product($id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = '$id'";
        return $this->db_fetch_one($sql);
    }
    
    // ⭐ EXTRA CREDIT: Search by keyword efficiently
    public function search_by_keyword($keyword) {
        $keyword = $this->db_conn()->real_escape_string($keyword);
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE FIND_IN_SET('$keyword', REPLACE(p.product_keywords, ', ', ',')) > 0
                ORDER BY p.product_id DESC";
        return $this->db_fetch_all($sql);
    }
    
    // ⭐ EXTRA CREDIT: Composite search with multiple filters
    public function composite_search($filters = []) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE 1=1";
        
        // Add search query filter
        if (!empty($filters['query'])) {
            $query = $this->db_conn()->real_escape_string($filters['query']);
            $sql .= " AND (p.product_title LIKE '%$query%' 
                      OR p.product_desc LIKE '%$query%' 
                      OR p.product_keywords LIKE '%$query%')";
        }
        
        // Add category filter
        if (!empty($filters['category']) && $filters['category'] > 0) {
            $sql .= " AND p.product_cat = '" . intval($filters['category']) . "'";
        }
        
        // Add brand filter
        if (!empty($filters['brand']) && $filters['brand'] > 0) {
            $sql .= " AND p.product_brand = '" . intval($filters['brand']) . "'";
        }
        
        // Add price range filter
        if (!empty($filters['max_price']) && $filters['max_price'] > 0) {
            $sql .= " AND p.product_price <= " . floatval($filters['max_price']);
        }
        
        if (!empty($filters['min_price']) && $filters['min_price'] >= 0) {
            $sql .= " AND p.product_price >= " . floatval($filters['min_price']);
        }
        
        $sql .= " ORDER BY p.product_id DESC";
        
        return $this->db_fetch_all($sql);
    }
    
    // Get all products (legacy - keeping for backward compatibility)
    public function get_all_products() {
        return $this->view_all_products();
    }
    
    // Get a single product by ID (legacy)
    public function get_product_by_id($product_id) {
        return $this->view_single_product($product_id);
    }
    
    // Update product
    public function update_product($product_id, $category_id, $brand_id, $title, $price, $description, $image, $keywords) {
        $sql = "UPDATE products 
                SET product_cat = '$category_id', 
                    product_brand = '$brand_id', 
                    product_title = '$title', 
                    product_price = '$price', 
                    product_desc = '$description', 
                    product_image = '$image', 
                    product_keywords = '$keywords' 
                WHERE product_id = '$product_id'";
        return $this->db_query($sql);
    }
    
    // Delete product
    public function delete_product($product_id) {
        $sql = "DELETE FROM products WHERE product_id = '$product_id'";
        return $this->db_query($sql);
    }
    
    // Get products by category (legacy)
    public function get_products_by_category($category_id) {
        return $this->filter_products_by_category($category_id);
    }
    
    // Get products by brand (legacy)
    public function get_products_by_brand($brand_id) {
        return $this->filter_products_by_brand($brand_id);
    }
    
    // Update product image
    public function update_product_image($product_id, $image_path) {
        $sql = "UPDATE products SET product_image = '$image_path' WHERE product_id = '$product_id'";
        return $this->db_query($sql);
    }
}
?>