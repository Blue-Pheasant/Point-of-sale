<?php

namespace app\core;

use app\core\DBModel;

abstract class FeedbackModel extends DBModel
{
    abstract public function getDisplayInfo(): string;
}

abstract class CartModel extends DBModel
{
    abstract public function getDisplayInfo(): string;
}

