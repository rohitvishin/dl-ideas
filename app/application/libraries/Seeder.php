<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seeder {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function call($seeder)
    {
        require_once APPPATH . 'database/seeds/' . $seeder . '.php';
        $seed = new $seeder();
        $seed->run();
    }
}