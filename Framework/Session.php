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
        if(session_status() == PHP_SESSION_NONE) session_start();
    }

    /**
     * Set a Session Variable
     * @param string $key
     * @param mixed $value
     * @return void
     */
    static function set($key,$value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a Session Variable Value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    static function get($key,$default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Check a Session Key Exists
     * @param string $key
     * @param bool
     */
    static function check($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Unset a Session Variable
     * @param string $key
     * @return void
     */
    static function unset($key)
    {
        if(isset($_SESSION[$key]))
        {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy a Session
     * @return void
     */
    static function destroy()
    {
        if(session_status() == PHP_SESSION_ACTIVE)
        {
            session_unset();
            session_destroy();
        }
    }

    /**
     * Set a Flash Message
     * @param string $key
     * @param string $msg
     * @return void
     */
    static function setFlash($key,$msg)
    {
        self::set("flash_$key",$msg);
    }

    /**
     * Get a Flash Message
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    static function getFlash($key,$default=null)
    {
        $msg = self::get("flash_$key",$default);
        self::unset("flash_$key");
        return $msg;
    }
}