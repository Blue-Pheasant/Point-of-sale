<?php

namespace app\Core;

/**
 * Single rendering engine for the entire application.
 *
 * Router delegates to this class; duplicate render logic in Router is kept
 * only as thin wrappers that call through here.
 */
class View
{
    public string $title = '';

    /**
     * Render a view wrapped in its layout.
     *
     * Resolves the active layout from the current controller (falls back to
     * Application::$layout when no controller is set).
     *
     * @param string               $view   View name relative to views/ (no .php).
     * @param array<string, mixed> $params Variables extracted into the view.
     * @return string The fully rendered HTML page.
     */
    public function renderView(string $view, array $params = []): string
    {
        $layoutName = Application::$app->layout;
        if (Application::$app->controller) {
            $layoutName = Application::$app->controller->layout;
        }
        $viewContent   = $this->renderViewOnly($view, $params);
        $layoutContent = $this->renderLayout($layoutName);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Render only the view body without a layout.
     *
     * @param string               $view
     * @param array<string, mixed> $params
     * @return string
     */
    public function renderViewOnly(string $view, array $params = []): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include Application::$ROOT_DIR . "/views/$view.php";
        return (string) ob_get_clean();
    }

    /**
     * Render pre-built content inside the active layout.
     *
     * @param string $viewContent Already-rendered body HTML.
     * @return string
     */
    public function renderContent(string $viewContent): string
    {
        $layoutName = Application::$app->layout;
        if (Application::$app->controller) {
            $layoutName = Application::$app->controller->layout;
        }
        $layoutContent = $this->renderLayout($layoutName);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Render a layout file by name.
     *
     * @param string $layout Layout name (e.g. 'main', 'admin', 'auth').
     * @return string
     */
    public function renderLayout(string $layout): string
    {
        ob_start();
        include Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return (string) ob_get_clean();
    }
}
