<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductSeeder {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function run()
    {
        $data = [
            [
                'name' => 'Product 1',
                'slug' => 'product-1',
                'price' => 100,
                'stock' => 5,
                'image' => null,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Product 2',
                'slug' => 'product-2',
                'price' => 200,
                'stock' => 10,
                'image' => null,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];


        $this->CI->db->insert_batch('products', $data);
    }
}