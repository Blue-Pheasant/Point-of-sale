<?php

namespace app\Core;

/**
 * Base model for user entities.
 *
 * Profile validation is now declared via rules() on the concrete subclass;
 * the old validateUpdateProfile() duplicate has been removed.
 */
abstract class UserModel extends DBModel
{
    abstract public function getDisplayName(): string;
}
