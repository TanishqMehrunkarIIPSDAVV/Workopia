<?php
/**
 * Get a base path
 * @param string $path
 * @return string
 */

function basePath($path="")
{
    $bp = __DIR__."/".$path;
    if(file_exists($bp)) return $bp;
    else echo "Path $path does not exist";
}

/**
 * Require a file
 * @param string $name
 * @return void
 */

function load($name)
{
    $vp = basePath("views/{$name}.php");
    if(file_exists($vp)) require_once $vp;
    else echo "View Path $name does not exist";
}

/**
 * Require a component
 * @param string $name
 * @return void
 */

function loadComponent($name)
{
    $cp = basePath("views/components/{$name}.php");
    if(file_exists($cp)) require_once $cp;
    else echo "Component Path $name does not exist";
}

/**
 * Inspect
 * @param mixed $value
 * @return void
 */

function inspect($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}

/**
 * Inspect and Die
 * @param mixed $value
 * @return void
 */

function inspectDie($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}