<?php
/**
 * Supplier Controller - Business logic for suppliers
 */

require_once(dirname(__FILE__) . '/../classes/supplier_class.php');

function add_supplier_ctr($name, $email, $phone, $address, $description, $logo = null) {
    $supplier = new Supplier();
    return $supplier->add_supplier($name, $email, $phone, $address, $description, $logo);
}

function get_all_suppliers_ctr() {
    $supplier = new Supplier();
    return $supplier->get_all_suppliers();
}

function get_verified_suppliers_ctr() {
    $supplier = new Supplier();
    return $supplier->get_verified_suppliers();
}

function get_supplier_ctr($supplier_id) {
    $supplier = new Supplier();
    return $supplier->get_supplier($supplier_id);
}

function update_supplier_ctr($supplier_id, $name, $email, $phone, $address, $description, $logo = null) {
    $supplier = new Supplier();
    return $supplier->update_supplier($supplier_id, $name, $email, $phone, $address, $description, $logo);
}

function verify_supplier_ctr($supplier_id, $admin_id, $status, $fda, $organic, $fair_trade, $local) {
    $supplier = new Supplier();
    return $supplier->verify_supplier($supplier_id, $admin_id, $status, $fda, $organic, $fair_trade, $local);
}

function delete_supplier_ctr($supplier_id) {
    $supplier = new Supplier();
    return $supplier->delete_supplier($supplier_id);
}

function add_certification_ctr($supplier_id, $cert_type, $cert_name, $cert_number, $cert_document, $issue_date, $expiry_date) {
    $supplier = new Supplier();
    return $supplier->add_certification($supplier_id, $cert_type, $cert_name, $cert_number, $cert_document, $issue_date, $expiry_date);
}

function get_supplier_certifications_ctr($supplier_id) {
    $supplier = new Supplier();
    return $supplier->get_supplier_certifications($supplier_id);
}

function get_supplier_products_ctr($supplier_id) {
    $supplier = new Supplier();
    return $supplier->get_supplier_products($supplier_id);
}
?>