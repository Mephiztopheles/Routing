<?php


namespace Mephiztopheles\Routing;


abstract class AbstractAccessProvider {

    /**
     * @param Route $route
     *
     * @return bool
     */
    public abstract function check ( Route $route );
}