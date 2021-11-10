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
            $feedback_id = Input::get('feedback_id');
            $feedbackModel = new Feedback;
            if($feedbackModel->delete($feedback_id)) {
                Session::set('Success', 'Feedback has id ' . $feedback_id . 'has been deleted.');
                Application::$app->response->redirect('feedback');
            } 
       }
    }
