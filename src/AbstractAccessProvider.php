<?php


namespace Mephiztopheles\Routing;


use Mephiztopheles\Routing\Route;

abstract class AbstractAccessProvider {

    /**
     * @param Route $route
     *
     * @return bool
     */
    public abstract function check( Route $route );
}