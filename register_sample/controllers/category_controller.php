<?php

require_once '../classes/category_class.php';

function add_category_ctr($category_name, $user_id)
{
    if (empty($category_name) || empty($user_id)) {
        return false;
    }
    
    // Validate category name
    if (strlen($category_name) < 2 || strlen($category_name) > 100) {
        return false;
    }
    
    $category = new Category();
    return $category->addCategory($category_name, $user_id);
}

function get_categories_by_user_ctr($user_id)
{
    if (empty($user_id)) {
        return false;
    }
    
    $category = new Category();
    return $category->getCategoriesByUser($user_id);
}

function get_category_by_id_ctr($cat_id, $user_id)
{
    if (empty($cat_id) || empty($user_id)) {
        return false;
    }
    
    $category = new Category();
    return $category->getCategoryById($cat_id, $user_id);
}

function update_category_ctr($cat_id, $category_name, $user_id)
{
    if (empty($cat_id) || empty($category_name) || empty($user_id)) {
        return false;
    }
    
    // Validate category name
    if (strlen($category_name) < 2 || strlen($category_name) > 100) {
        return false;
    }
    
    $category = new Category();
    return $category->updateCategory($cat_id, $category_name, $user_id);
}

function delete_category_ctr($cat_id, $user_id)
{
    if (empty($cat_id) || empty($user_id)) {
        return false;
    }
    
    $category = new Category();
    return $category->deleteCategory($cat_id, $user_id);
}

function check_category_exists_ctr($category_name, $user_id)
{
    if (empty($category_name) || empty($user_id)) {
        return false;
    }
    
    $category = new Category();
    return $category->categoryExists($category_name, $user_id);
}