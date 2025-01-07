<?php

namespace Framework;

class Session
{
    /**
     * Start a Session
     * @return void
     */
    static function start()
    {
        if(session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
    }
}