<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_product_model extends CI_Model
{
    public function createProduct($data)
    {
        return $this->db->insert('products', $data);
    }

    public function getCatalogProducts($limit = NULL)
    {
        $query = $this->db
            ->select('id, name, slug, description, price, stock, image, status, created_at')
            ->from('products')
            ->where('status', 1)
            ->order_by('id', 'DESC');

        if ($limit !== NULL) {
            $query->limit((int) $limit);
        }

        return $query->get()->result_array();
    }

    public function getRecentProducts($limit = 10)
    {
        return $this->db
            ->select('id, name, slug, price, stock, status, created_at')
            ->from('products')
            ->order_by('id', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    public function existsBySlug($slug)
    {
        return $this->db
            ->from('products')
            ->where('slug', $slug)
            ->limit(1)
            ->count_all_results() > 0;
    }

    public function findActiveProductBySlug($slug)
    {
        return $this->db
            ->select('id, name, slug, description, price, stock, image, status, created_at')
            ->from('products')
            ->where('slug', $slug)
            ->where('status', 1)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function countAllProducts()
    {
        return (int) $this->db->count_all('products');
    }
}
