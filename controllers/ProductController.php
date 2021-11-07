<?php
/*
    controllers/product.php
*/

namespace app\controllers;

use app\core\Controller;

class ProductController extends Controller {
        public function __construct()
        {
            parent::__construct();
        }

        public function index() 
        {
            return $this->render('product');    
        }

        public function add() 
        {

        }

        public function remove()
        {

        }

        public function update()
        {

        }
    }