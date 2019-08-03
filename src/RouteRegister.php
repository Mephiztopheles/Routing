<?php


namespace Mephiztopheles\Routing;


use \Exception;
use Mephiztopheles\Routing\exception\MethodNotAllowedException;

class RouteRegister {

    /**
     * @type Route[]
     */
    private $list = [];

    /**
     * @param string $url
     * @param string $method
     *
     * @return Route
     * @throws MethodNotAllowedException
     */
    public function find( string $url, string $method ) {

        foreach ( $this->list as $entry )
            if ( $entry->matches( $url, $method ) )
                return $entry;

        return null;
    }

    /**
     * @param Route $route
     *
     * @throws Exception
     */
    public function add( Route $route ) {

        foreach ( $this->list as $entry )
            if ( $entry->equals( $route ) )
                throw new Exception( "Route $route already registered" );

        $this->list[] = $route;
    }
}