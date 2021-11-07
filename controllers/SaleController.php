<?php
/*
    controllers/SaleController.php
*/

namespace app\controllers;

use app\core\Controller;

class SaleController extends Controller {
        public function __construct()
        {
            parent::__construct();
        }

        public function index()
        {
            return $this->render('sale');
        }

        public function new()
        {
            // Too long :(( wait for me some days
        }
}