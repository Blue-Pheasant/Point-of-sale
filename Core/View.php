<?php

namespace app\Core;


/**
 * Class View
 *
 * This class is responsible for handling the views of the application.
 *
 * @package app\Core
 */
class View
{
    /**
     * @var string $title The title of the view.
     */
    public string $title = '';

    /**
     * Method renderView
     *
     * Renders the view with the layout.
     *
     * @param string $view The view to be rendered.
     * @param array $params The parameters to be passed to the view.
     * @return string
     */
    public function renderView(string $view, array $params): string
    {
        $layoutName = Application::$app->layout;
        if (Application::$app->controller) {
            $layoutName = Application::$app->controller->layout;
        }
        $viewContent = $this->renderViewOnly($view, $params);
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layoutName.php";
        $layoutContent = ob_get_clean();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Renders a view without a layout.
     *
     * This method takes a view name and an associative array of parameters.
     * It extracts the parameters into variables that can be used in the view.
     * It then starts output buffering and includes the view file.
     * Finally, it returns the contents of the output buffer and cleans the buffer.
     *
     * @param string $view The name of the view to render.
     * @param array $params An associative array of parameters to extract into variables for the view.
     * @return string The rendered view.
    */
    public function renderViewOnly(string $view, array $params): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}