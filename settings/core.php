<?php
session_start();

function check_login() {
    return isset($_SESSION['user_id']);
}

function check_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;
}

function get_user_id() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : false;
}

function get_user_name() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
}

function get_user_role() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 2;
}
?>