<?php

namespace app\core;

use app\core\DBModel;

abstract class UserModel extends DBModel
{
    abstract public function getDisplayName(): string;
}