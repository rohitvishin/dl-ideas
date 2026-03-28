<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

    public function index()
    {

        $this->load->database();
        $this->load->library('migration');

        if ($this->migration->latest() === FALSE)
        {
            show_error($this->migration->error_string());
        }
        else
        {
            // Optional: reset tables (dev only)
            $this->db->truncate('users');
            $this->db->truncate('products');

            // Run seeders
            $this->load->library('seeder');
            $this->seeder->call('UserSeeder');
            $this->seeder->call('ProductSeeder');

            echo "✅ Migration + Seeding completed!";
        }
    }
}