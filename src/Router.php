<?php

namespace Mephiztopheles\Routing;

use Closure;
use Exception;
use Mephiztopheles\Routing\exception\APIException;
use Mephiztopheles\Routing\exception\MethodNotAllowedException;

/**
 * @param $url string
 *
 * @return string
 */
function cleanUri ( $url ) {

    $phpSelf = dirname( $_SERVER[ 'PHP_SELF' ] );

    // prevent removing fist slash
    if ( $phpSelf != "/" )
        $url = str_replace( $phpSelf, '', $url );

    // remove query-string
    $queryString = strpos( $url, '?' );
    if ( $queryString !== false )
        $url = substr( $url, 0, $queryString );

    // allows to use http://localhost/index.php/uri
    $basename = basename( $_SERVER[ 'PHP_SELF' ] );
    if ( substr( $url, 1, strlen( $basename ) ) == $basename )
        $url = substr( $url, strlen( $basename ) + 1 );

    // ensure it ends with a /
    $url = rtrim( $url, '/' ) . '/';
    // remove double slashes
    $url = preg_replace( '/\/+/', '/', $url );

    return $url;
}

/**
 * Used to handle routes  to eliminate the need to create a php file for every endpoint
 */
class Router {

    protected $namespace = '\Controller';

    /**
     * @var Response
     * @type Response
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    private $root;

    private $register;
    private $accessProvider;

    /**
     * Router constructor.
     *
     * @param string                 $root
     * @param AbstractAccessProvider $accessProvider
     */
    public function __construct ( string $root, AbstractAccessProvider $accessProvider = null ) {

        $this->root           = $root;
        $this->response       = new Response();
        $this->request        = new Request();
        $this->register       = new RouteRegister();
        $this->accessProvider = $accessProvider;
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function get ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "get" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function head ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "head" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function post ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "post" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function put ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "put" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function delete ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "delete" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function connect ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "connect" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function options ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "options" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function trace ( $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "trace" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function patch ( string $url, $callback ) {
        return $this->add( $url, $callback )->allowMethod( "patch" );
    }

    /**
     * @param string         $url
     *
     * @param Closure|string $callback
     *
     * @return RouteBuilder
     * @throws Exception
     */
    public function add ( string $url, $callback ) {

        $route = new Route( $this->root . $url, $callback, $this->request, $this->response );

        $this->register->add( $route );

        return new RouteBuilder( $route );
    }

    public function setControllerNamespace ( $namespace ) {
        $this->namespace = $namespace;
    }

    private function defaultRequestHandler () {
        $this->response->notFound();
    }

    /**
     * @param $route
     *
     * @return bool
     */
    private function checkAccess ( Route $route ) {

        if ( $this->accessProvider->check( $route ) )
            return true;

        $this->response->notAllowed();
        return false;
    }

    private function resolve () {

        $uri           = cleanUri( $this->request->requestUri );
        $requestMethod = strtoupper( $this->request->requestMethod );

        $this->response->header( "-x-uri:$uri" );

        try {

            $route = $this->register->find( $uri, $requestMethod );

        } catch ( MethodNotAllowedException $e ) {

            $this->response->methodNotAllowed();
            return;
        }

        if ( !isset( $route ) ) {

            $this->defaultRequestHandler();
            return;
        }

        $params = $route->getParams();

        if ( !$this->checkAccess( $route ) )
            return;

        try {

            if ( gettype( $route->getCallback() ) == "string" ) {

                $parts  = explode( "@", $route->getCallback() );
                $clazz  = $this->namespace . "\\" . $parts[ 0 ];
                $method = $parts[ 1 ];

                $result = call_user_func_array( array( new $clazz(), $method ), $params );
            } else {

                $result = call_user_func_array( $route->getCallback(), $params );
            }

            if ( !empty( $result ) ) {

                if ( $result instanceof Response )
                    $result->send();
                else
                    $this->response->json( $result )->send();
            }

        } catch ( Exception $e ) {

            if ( $e instanceof APIException )
                $this->response->header( "x-message: {$e->getMessage()}" )->status( $e->getStatusCode() );
            else
                $this->response->header( "{$this->request->serverProtocol} 500 {$e->getMessage()}" );
        }
    }

    function __destruct () {
        $this->resolve();
    }
}