<?php

require_once '../classes/user_class.php';

function register_user_ctr($name, $email, $password, $phone_number, $role, $country = null, $city = null)
{
    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($phone_number)) {
        return false;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $user = new User();
    $user_id = $user->createUser($name, $email, $password, $phone_number, $role, $country, $city);
    if ($user_id) {
        return $user_id;
    }
    return false;
}

function login_user_ctr($email, $password)
{
    // Basic validation
    if (empty($email) || empty($password)) {
        return false;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    $user = new User();
    $result = $user->loginUser($email, $password);
    
    if ($result) {
        return $result;
    }
    return false;
}

function check_email_exists_ctr($email)
{
    $user = new User();
    return $user->emailExists($email);
}

function get_user_by_email_ctr($email)
{
    $user = new User();
    return $user->getUserByEmail($email);
}