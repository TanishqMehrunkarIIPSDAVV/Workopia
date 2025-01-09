<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize
{
    /**
     * Check if user is authenticated
     * @return bool
     */
    function isAuthenticated()
    {
        return Session::check("user");
    }

    /**
     * Handle the user`s request
     * @param string $role
     * @return bool
     */
    function handle($role)
    {
        if($role === "guest" && $this->isAuthenticated())
        {
            return redirect("/");
        }
        else if($role === "auth" && !$this->isAuthenticated())
        {
            return redirect("/auth/login");
        }
    }
}