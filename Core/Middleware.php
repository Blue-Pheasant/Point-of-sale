<?php

namespace app\Core;

/**
 * Base middleware class.
 *
 * Subclasses implement execute() which is invoked by the router pipeline
 * (via Router::runMiddleware) before the controller action runs.
 *
 * The $actions array allows action-scoped middleware: when non-empty, execute()
 * should only apply its logic when currentAction() is in the list.
 */
abstract class Middleware
{
    /** @var list<string> */
    protected array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    protected function currentAction(): string
    {
        return Application::$app->controller?->action ?? '';
    }

    abstract public function execute(): void;
}
