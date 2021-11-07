<?php
/*
    controllers/feedback/index.php
*/
namespace app\controllers;

use app\core\Controller;

    class FeedbackController extends Controller {
       public function __construct()
       {
           parent::__construct();
       }

       public function index()
       {
           return $this->render('feedback');
       }

       public function response()
       {

       }

       public function remove()
       {

       }
    }
