<?php
/*
    controllers/feedback/index.php
*/
namespace app\controllers;

use app\core\Controller;
use app\core\Input;
use app\models\Feedback;
use app\core\Application;
use app\core\Session;

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
