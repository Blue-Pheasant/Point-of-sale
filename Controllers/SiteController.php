<?php

namespace app\Controllers;

use app\Core\Controller;
use app\Models\Store;

/**
 * Class SiteController
 *
 * This class is responsible for handling the site operations of the application.
 * It extends the base Controller class and uses the Store model.
 *
 * @package app\Controllers
 */
class SiteController extends Controller
{
    /**
     * Method home
     *
     * Renders the 'home' view with the application name.
     *
     * @return array|bool|string
     */
    public function home(): array|bool|string
    {
        return $this->render('home', [
            'name' => 'Buy me store'
        ]);
    }

    /**
     * Method error
     *
     * Renders the 'permission' view with the error message.
     *
     * @return array|bool|string
     */
    public function error(): array|bool|string
    {
        $this->setLayout('auth');
        return $this->render('permission');
    }

    /**
     * Method about
     *
     * Renders the 'about' view.
     *
     * @return array|bool|string
     */
    public function about(): array|bool|string
    {
        return $this->render('about');
    }

    /**
     * Method stores
     *
     * Fetches all stores and renders the 'stores' view with the fetched data.
     *
     * @return array|bool|string
     */
    public function stores(): array|bool|string
    {
        $stores = Store::getAll();
        $this->setLayout('main');
        return $this->render('stores', [
            'store' => $stores
        ]);
    }

    /**
     * Method contact
     *
     * Renders the 'contact' view.
     *
     * @return array|bool|string
     */
    public function contact(): array|bool|string
    {
        return $this->render('contact');
    }

    /**
     * Method payment
     *
     * Renders the 'payment' view.
     *
     * @return array|bool|string
     */
    public function notice(): array|bool|string
    {
        $this->setLayout('auth');
        return $this->render('payment_success');
    }
}
