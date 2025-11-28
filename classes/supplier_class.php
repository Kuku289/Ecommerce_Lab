<?php
/**
 * Supplier Class - Handles supplier database operations
 */

require_once(dirname(__FILE__) . '/../settings/db_class.php');

class Supplier extends db_connection {
    
    /**
     * Add a new supplier
     */
    public function add_supplier($name, $email, $phone, $address, $description, $logo = null) {
        $sql = "INSERT INTO suppliers (supplier_name, supplier_email, supplier_phone, supplier_address, 
                supplier_description, supplier_logo, verification_status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $phone, $address, $description, $logo);
        
        if ($stmt->execute()) {
            return $this->db_conn()->insert_id;
        }
        return false;
    }
    
    /**
     * Get all suppliers
     */
    public function get_all_suppliers() {
        $sql = "SELECT s.*, 
                       (SELECT COUNT(*) FROM products WHERE supplier_id = s.supplier_id) as product_count,
                       c.customer_name as verified_by_name
                FROM suppliers s
                LEFT JOIN customer c ON s.verified_by = c.customer_id
                ORDER BY s.registration_date DESC";
        
        $result = $this->db_query($sql);
        
        $suppliers = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $suppliers[] = $row;
            }
        }
        return $suppliers;
    }
    
    /**
     * Get verified suppliers only
     */
    public function get_verified_suppliers() {
        $sql = "SELECT s.*, 
                       (SELECT COUNT(*) FROM products WHERE supplier_id = s.supplier_id) as product_count
                FROM suppliers s
                WHERE s.verification_status = 'Verified'
                ORDER BY s.supplier_name ASC";
        
        $result = $this->db_query($sql);
        
        $suppliers = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $suppliers[] = $row;
            }
        }
        return $suppliers;
    }
    
    /**
     * Get single supplier
     */
    public function get_supplier($supplier_id) {
        $sql = "SELECT s.*, 
                       (SELECT COUNT(*) FROM products WHERE supplier_id = s.supplier_id) as product_count,
                       c.customer_name as verified_by_name
                FROM suppliers s
                LEFT JOIN customer c ON s.verified_by = c.customer_id
                WHERE s.supplier_id = ?";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $supplier_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Update supplier
     */
    public function update_supplier($supplier_id, $name, $email, $phone, $address, $description, $logo = null) {
        if ($logo) {
            $sql = "UPDATE suppliers SET supplier_name = ?, supplier_email = ?, supplier_phone = ?, 
                    supplier_address = ?, supplier_description = ?, supplier_logo = ? 
                    WHERE supplier_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("ssssssi", $name, $email, $phone, $address, $description, $logo, $supplier_id);
        } else {
            $sql = "UPDATE suppliers SET supplier_name = ?, supplier_email = ?, supplier_phone = ?, 
                    supplier_address = ?, supplier_description = ? 
                    WHERE supplier_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("sssssi", $name, $email, $phone, $address, $description, $supplier_id);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Verify supplier
     */
    public function verify_supplier($supplier_id, $admin_id, $status, $fda, $organic, $fair_trade, $local) {
        $sql = "UPDATE suppliers 
                SET verification_status = ?, verified_by = ?, verified_date = NOW(),
                    fda_approved = ?, organic_certified = ?, fair_trade_certified = ?, local_farmer = ?
                WHERE supplier_id = ?";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("siiiii", $status, $admin_id, $fda, $organic, $fair_trade, $local, $supplier_id);
        
        return $stmt->execute();
    }
    
    /**
     * Delete supplier
     */
    public function delete_supplier($supplier_id) {
        $sql = "DELETE FROM suppliers WHERE supplier_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $supplier_id);
        return $stmt->execute();
    }
    
    /**
     * Add certification
     */
    public function add_certification($supplier_id, $cert_type, $cert_name, $cert_number, $cert_document, $issue_date, $expiry_date) {
        $sql = "INSERT INTO supplier_certifications (supplier_id, cert_type, cert_name, cert_number, 
                cert_document, issue_date, expiry_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("issssss", $supplier_id, $cert_type, $cert_name, $cert_number, $cert_document, $issue_date, $expiry_date);
        
        return $stmt->execute();
    }
    
    /**
     * Get supplier certifications
     */
    public function get_supplier_certifications($supplier_id) {
        $sql = "SELECT * FROM supplier_certifications WHERE supplier_id = ? ORDER BY upload_date DESC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $supplier_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $certifications = [];
        while ($row = $result->fetch_assoc()) {
            $certifications[] = $row;
        }
        return $certifications;
    }
    
    /**
     * Get products by supplier
     */
    public function get_supplier_products($supplier_id) {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.product_cat = c.cat_id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE p.supplier_id = ?
                ORDER BY p.product_id DESC";
        
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $supplier_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }
}
?>