<?php

namespace app\core;

use app\core\DBModel;

abstract class CategoryModel extends DBModel
{
    abstract public function getDisplayName(): string;
}

abstract class FeedbackModel extends DBModel
{
    abstract public function getDisplayInfo(): string;
}

abstract class ProductModel extends DBModel
{
    abstract public function getDisplayInfo(): string;
}

abstract class CartModel extends DBModel
{
    abstract public function getDisplayInfo(): string;
}

abstract class RecordModel extends DBModel
{
    abstract public function getDisplayInfo(): string;
}
