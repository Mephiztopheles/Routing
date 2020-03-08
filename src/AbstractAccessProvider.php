<?php


namespace Mephiztopheles\Routing;


abstract class AbstractAccessProvider {


    function check ( Route $route, Request $request, Response $response ) {

        $denied  = $route->getDenied();
        $allowed = $route->getAllowed();

        if ( !count( $allowed ) && !count( $denied ) )
            return true;

        foreach ( $denied as $secure ) {

            if ( is_callable( $secure ) )
                $allowed = call_user_func_array( $secure, [ $route, $request, $response ] );
            else
                $allowed = $this->isAllowed( $secure, $route, $request, $response );

            if ( !$allowed )
                return false;
        }

        if ( !count( $allowed ) )
            return true;

        foreach ( $allowed as $secure ) {

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
     * @return bool
     */
    public abstract function isAllowed ( $object, Route $route, Request $request, Response $response );
}