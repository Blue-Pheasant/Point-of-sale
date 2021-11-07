<?php
/*
    controllers/category/index.php
*/
namespace app\controllers;

use app\core\Controller;

    class CategoryController extends Controller {
        public function __construct()
        {
            parent::__construct();
        }

        public function index() 
        {
            return $this->render('category');    
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


