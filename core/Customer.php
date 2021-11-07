<?php

namespace app\core;

use app\core\DBModel;

abstract class CustomerModel extends DBModel
{
    abstract public function getDisplayName(): string;
}