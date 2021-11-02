<?php

namespace app\controllers;


use app\core\Controller;

class AboutController extends Controller
{
    public function index()
    {
        return $this->render('about');
    }
}