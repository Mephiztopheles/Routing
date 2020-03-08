<?php


namespace Mephiztopheles\Routing;


abstract class AbstractAccessProvider {

    function check ( Route $route, Request $request, Response $response ) {

        $denyConfigs  = $route->getDenied();
        $allowConfigs = $route->getAllowed();

        if ( !count( $allowConfigs ) && !count( $denyConfigs ) )
            return true;

        foreach ( $denyConfigs as $secure ) {

            if ( is_callable( $secure ) )
                $allowed = call_user_func_array( $secure, [ $route, $request, $response ] );
            else
                $allowed = $this->isDenied( $secure, $route, $request, $response );

            if ( $allowed )
                return false;
        }

        if ( !count( $allowConfigs ) )
            return true;

        foreach ( $allowConfigs as $secure ) {

            if ( is_callable( $secure ) )
                $allowed = call_user_func_array( $secure, [ $route, $request, $response ] );
            else
                $allowed = $this->isAllowed( $secure, $route, $request, $response );

            if ( $allowed )
                return true;
        }

        return false;
    }

    /**
     * @param          $object
     * @param Route    $route
     * @param Request  $request
     * @param Response $response
     *
     * @return bool if the current object means to allow the access to the route
     */
    public abstract function isAllowed ( $object, Route $route, Request $request, Response $response );

    /**
     * @param          $object
     * @param Route    $route
     * @param Request  $request
     * @param Response $response
     *
     * @return bool if the current object means to deny the access to the route
     */
    public abstract function isDenied ( $object, Route $route, Request $request, Response $response );
}