<?php
/**
 * Cart Class - Handles all cart database operations
 */

require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Cart extends db_connection {
    
    /**
     * Add product to cart or update quantity if exists
     */
    public function add_to_cart($product_id, $customer_id, $quantity, $ip_address) {
        // Check if product already exists in cart
        $check_sql = "SELECT * FROM cart WHERE p_id = ? AND c_id = ?";
        $check_stmt = $this->db_conn()->prepare($check_sql);
        $check_stmt->bind_param("ii", $product_id, $customer_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Product exists, update quantity
            $row = $result->fetch_assoc();
            $new_qty = $row['qty'] + $quantity;
            $update_sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
            $update_stmt = $this->db_conn()->prepare($update_sql);
            $update_stmt->bind_param("iii", $new_qty, $product_id, $customer_id);
            return $update_stmt->execute();
        } else {
            // Product doesn't exist, insert new
            $insert_sql = "INSERT INTO cart (p_id, c_id, qty, ip_add) VALUES (?, ?, ?, ?)";
            $insert_stmt = $this->db_conn()->prepare($insert_sql);
            $insert_stmt->bind_param("iiis", $product_id, $customer_id, $quantity, $ip_address);
            return $insert_stmt->execute();
        }
    }
    
    /**
     * Get all cart items for a customer
     */
    public function get_cart_items($customer_id) {
        $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, 
                       cat.cat_name, b.brand_name,
                       (p.product_price * c.qty) as subtotal
                FROM cart c
                JOIN products p ON c.p_id = p.product_id
                LEFT JOIN categories cat ON p.product_cat = cat.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE c.c_id = ?
                ORDER BY c.p_id DESC";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $cart_items = [];
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
        }
        
        return $cart_items;
    }
    
    /**
     * Update cart item quantity
     */
    public function update_cart_quantity($product_id, $customer_id, $quantity) {
        $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("iii", $quantity, $product_id, $customer_id);
        return $stmt->execute();
    }
    
    /**
     * Remove item from cart
     */
    public function remove_from_cart($product_id, $customer_id) {
        $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("ii", $product_id, $customer_id);
        return $stmt->execute();
    }
    
    /**
     * Empty entire cart for a customer
     */
    public function empty_cart($customer_id) {
        $sql = "DELETE FROM cart WHERE c_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }
    
    /**
     * Get cart total
     */
    public function get_cart_total($customer_id) {
        $sql = "SELECT SUM(p.product_price * c.qty) as total
                FROM cart c
                JOIN products p ON c.p_id = p.product_id
                WHERE c.c_id = ?";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
    
    /**
     * Get cart item count
     */
    public function get_cart_count($customer_id) {
        $sql = "SELECT SUM(qty) as count FROM cart WHERE c_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] ?? 0;
    }
}
?>