<?php

namespace app\controllers;

use app\core\Controller;

class AdminController extends Controller
{
    public function __construct() {}

    public function index() 
    {
<<<<<<< HEAD
        parent::__construct();
    }

    public function index() 
    {
=======
>>>>>>> long
        return $this->render('dashboard');
    }

    public function products()
    {
        return $this->render('products');
    }

    public function stores()
    {
        return $this->render('stores');
    }

    public function orders()
    {
        return $this->render('orders');
    }
}