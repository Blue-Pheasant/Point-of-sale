<?php

namespace app\Core;

/**
 * Class Controller
 *
 * This class is responsible for handling the controller operations of the application.
 * It uses the Router, Middleware, and Response classes.
 *
 * @package app\Core
 */
class Controller
{
    /**
     * @var string $layout The layout of the controller.
     * Default is 'main'.
     */
    public string $layout = 'main';

    /**
     * @var string $action The action of the controller.
     */
    public string $action = '';

    /**
     * @var Middleware $middleware The middleware instance.
     */
    public Middleware $middleware;

    /**
     * Method render
     *
     * Renders the view with the given parameters.
     *
     * @param string $view The view to be rendered.
     * @param array $params The parameters to be passed to the view.
     * @return array|bool|string
     */
    public function render(string $view, array $params = []): bool|array|string
    {
        return Application::$app->router->renderView($view, $params);
    }

    /**
     * Method setLayout
     *
     * Sets the layout of the controller.
     *
     * @param string $layout The layout of the controller.
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Method registerMiddleware
     *
     * Registers the middleware with the given actions.
     *
     * @param string $middleware The middleware to be registered.
     * @param array $actions The actions to be registered.
     */
    public function registerMiddleware(string $middleware, array $actions = []): void
    {
        $this->middleware = new $middleware($actions);
        $this->middleware->execute();
    }

    /**
     * Method getMiddleware
     *
     * Returns the middleware instance.
     *
     * @return Middleware
     */
    public function getMiddleware(): Middleware
    {
        return $this->middleware;
    }

    /**
     * Method redirect
     *
     * Redirects to the given URL.
     *
     * @param string $url The URL to be redirected to.
     * @return array|bool|string
     */
    public function redirect(string $url): bool|array|string
    {
        return  Application::$app->response->redirect($url);
    }

    /**
     * Method intended
     *
     * Redirects to the intended URL.
     *
     * @param string $url The URL to be redirected to.
     * @return array|bool|string
     */
    public function intended(string $url): bool|array|string
    {
        return Application::$app->router->intended($url);
    }

    /**
     * Method setFlash
     *
     * Sets the flash message with the given key and message.
     *
     * @param string $key The key of the flash message.
     * @param string $message The message of the flash message.
     * @return array|bool|string
     */
    public function setFlash(string $key, string $message): bool|array|string
    {
        return Application::$app->session->setFlash($key, $message);
    }

    /**
     * Method getFlash
     *
     * Gets the flash message with the given key.
     *
     * @param string $key The key of the flash message.
     * @return array|bool|string
     */
    public function getFlash(string $key): bool|array|string
    {
        return Application::$app->session->getFlash($key);
    }

    /**
     * Method refresh
     *
     * Refreshes the current URL.
     *
     * @return array|bool|string
     */
    public function refresh(): bool|array|string
    {
        $currentUrl = Application::$app->request->getReqest();
        return Application::$app->response->redirect($currentUrl);
    }

    /**
     * Method back
     *
     * Redirects back to the previous URL.
     *
     * @return array|bool|string
     */
    public function back(): bool|array|string
    {
        return Application::$app->response->back();
    }
}