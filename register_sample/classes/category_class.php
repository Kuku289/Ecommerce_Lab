<?php

require_once '../settings/db_class.php';

class Category extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    // Add new category
    public function addCategory($category_name, $user_id)
    {
        // Check if category name already exists for this user
        if ($this->categoryExists($category_name, $user_id)) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO categories (cat_name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $category_name, $user_id);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Get all categories for a specific user
    public function getCategoriesByUser($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY cat_name");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $categories = array();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    // Get single category by ID
    public function getCategoryById($cat_id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cat_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update category
    public function updateCategory($cat_id, $category_name, $user_id)
    {
        // Check if new name already exists for this user (excluding current category)
        $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND user_id = ? AND cat_id != ?");
        $stmt->bind_param("sii", $category_name, $user_id, $cat_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            return false; // Name already exists
        }

        $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $category_name, $cat_id, $user_id);
        
        return $stmt->execute();
    }

    // Delete category
    public function deleteCategory($cat_id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cat_id, $user_id);
        
        return $stmt->execute();
    }

    // Check if category name exists for user
    public function categoryExists($category_name, $user_id)
    {
        $stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND user_id = ?");
        $stmt->bind_param("si", $category_name, $user_id);
        $stmt->execute();
        
        return $stmt->get_result()->num_rows > 0;
    }
}