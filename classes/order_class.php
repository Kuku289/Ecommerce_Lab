<?php
/**
 * Order Class - Handles all order database operations
 */

require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Order extends db_connection {
    
    /**
     * Create a new order
     */
    public function create_order($customer_id, $invoice_no, $order_status = 'Pending') {
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_status, order_date) 
                VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("iss", $customer_id, $invoice_no, $order_status);
        
        if ($stmt->execute()) {
            return $this->db_conn()->insert_id; // Return the new order_id
        }
        return false;
    }
    
    /**
     * Add order details (items in the order)
     */
    public function add_order_details($order_id, $product_id, $quantity) {
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("iii", $order_id, $product_id, $quantity);
        
        return $stmt->execute();
    }
    
    /**
     * Record payment
     */
    public function record_payment($order_id, $customer_id, $amount) {
        $sql = "INSERT INTO payment (order_id, customer_id, amt, payment_date) 
                VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("iid", $order_id, $customer_id, $amount);
        
        return $stmt->execute();
    }
    
    /**
     * Get all orders for a customer
     */
    public function get_customer_orders($customer_id) {
        $sql = "SELECT o.*, p.amt as payment_amount, p.payment_date,
                       (SELECT COUNT(*) FROM orderdetails WHERE order_id = o.order_id) as item_count
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = ?
                ORDER BY o.order_date DESC";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get order details with products
     */
    public function get_order_details($order_id) {
        $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image,
                       (p.product_price * od.qty) as subtotal
                FROM orderdetails od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $details = [];
        while ($row = $result->fetch_assoc()) {
            $details[] = $row;
        }
        
        return $details;
    }
    
    /**
     * Get single order info
     */
    public function get_order($order_id) {
        $sql = "SELECT o.*, p.amt as payment_amount, p.payment_date
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.order_id = ?";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
}
?>