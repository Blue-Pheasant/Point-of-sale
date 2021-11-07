<?php

namespace app\core;

use app\core\DbModel;


abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}