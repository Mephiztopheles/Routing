<?php


namespace Mephiztopheles\Routing;


class RouteBuilder {

    private $route;

    function __construct ( Route $route ) {
        $this->route = $route;
    }

    public function allowMethod ( ...$methods ) {

        foreach ( $methods as $method )
            $this->route->addMethod( strtoupper( $method ) );

        return $this;
    }

    public function denyMethod ( string ...$methods ) {

        foreach ( $methods as $method )
            $this->route->removeMethod( $method );

        return $this;
    }

    public function allow ( ...$objects ) {

        foreach ( $objects as $object )
            $this->route->allow( $object );

        return $this;
    }

    public function deny ( ...$objects ) {

        foreach ( $objects as $object )
            $this->route->deny( $object );

        return $this;
    }
}