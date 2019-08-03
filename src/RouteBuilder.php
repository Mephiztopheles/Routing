<?php


namespace Mephiztopheles\Routing;


class RouteBuilder {

    private $route;

    public function __construct( Route $route ) {
        $this->route = $route;
    }

    public function allowMethod( ...$methods ) {

        foreach ( $methods as $method )
            $this->route->addMethod( strtoupper( $method ) );

        return $this;
    }

    public function denyMethod( string ...$methods ) {

        foreach ( $methods as $method )
            $this->route->removeMethod( $method );

        return $this;
    }

    public function allow( $object ) {

        $this->route->allow( $object );

        return $this;
    }
}