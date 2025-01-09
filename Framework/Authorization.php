<?php

namespace Framework;

use Framework\Session;

class Authorization
{
    /**
     * Check if owner
     * @param int $id
     * @return bool
     */
    static function isOwner($id)
    {
        $sessionUser=Session::get("user");
        if($sessionUser !== null && isset($sessionUser["id"]))
        {
            $sessionUserId = (int) $sessionUser["id"];
            return $sessionUserId === $id;
        }
        return false;
    }
}