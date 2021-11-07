<?php
/*
    controllers/user.php
*/
namespace app\controllers;

use app\core\Controller;
use app\core\Input;
use app\core\Response;
use app\core\Session;
use app\models\Customer;

class CustomerController extends Controller{
    public function __construct()
    {
		parent::__construct();
	}

    public function index() 
    {
        return $this->render('customer/index');
    }

    public function remove()
    {
        $customer_id = Input::get('customer_id');
        $customerModel = new Customer;
        if($customerModel->delete($customer_id)) {
            Session::set('Success', 'Customer has id ' . $customer_id . ' has been deleted.');
            Response::redirect('customer');
        }
    }

    public function update()
    {
        //Too long :(( pls wait me some days bro
    }

    public function view()
    {
        
    }
}