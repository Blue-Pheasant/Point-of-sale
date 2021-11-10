<?php
/*
    controllers/user.php
*/
namespace app\controllers;

use app\core\Controller;
use app\core\Input;
use app\core\Application;
use app\core\Session;
use app\models\User;

class UserController extends Controller{
    public function __construct() {}

    public function index() 
    {
        return $this->render('user');
    }

    public function remove()
    {
        
    }

    public function update()
    {
        //Too long :(( pls wait me some days bro
    }

    public function view()
    {
        
    }
}