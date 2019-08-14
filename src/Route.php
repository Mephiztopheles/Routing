<?php


namespace Mephiztopheles\Routing;


use Mephiztopheles\Routing\exception\MethodNotAllowedException;

class Route {

    private $methods = [];
    private $allowed = [];
    private $denied = [];

    private $url;
    private $regex;

    private $callback;

    /**
     * @var array
     */
    private $params;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    function __construct( string $url, $callback, Request $request, Response $response ) {

        $this->response = $response;
        $this->request = $request;

        $this->url = $url;
        $this->callback = $callback;

        $this->format();
    }

    public function getCallback() {
        return $this->callback;
    }

    public function getParams() {
        return $this->params;
    }

    public function getAllowed() {
        return $this->allowed;
    }

    public function getDenied() {
        return $this->denied;
    }

    public function equals( Route $route ) {
        return $this->url === $route->url;
    }

    /**
     * @param string $url
     * @param string $method
     *
     * @return bool
     * @throws MethodNotAllowedException
     */
    public function matches( string $url, string $method ) {

        if ( preg_match( $this->regex, $url, $matches ) ) {

            if ( !in_array( $method, $this->methods ) )
                throw new MethodNotAllowedException();

            $this->params = [ $this->request, $this->response ];

            foreach ( $matches as $key => $match )
                if ( is_string( $key ) )
                    $this->params[] = $match;

            return true;
        }

        return false;
    }

    public function addMethod( string $method ) {
        $this->methods[] = $method;
    }

    public function removeMethod( string $method ) {

        foreach ( $this->methods as $key => $value )
            if ( $method === $value )
                array_splice( $this->methods, $key, 1 );
    }

    public function allow( $object ) {

        foreach ( $this->denied as $key => $value )
            if ( $object === $value )
                array_splice( $this->denied, $key, 1 );

        $this->allowed[] = $object;
    }

    public function deny( $object ) {

        foreach ( $this->allowed as $key => $value )
            if ( $object === $value )
                array_splice( $this->allowed, $key, 1 );

        $this->denied[] = $object;
    }

    public function toString() {
        return $this->url;
    }

    private function format() {

        // Make sure the route ends in a / since all of the URLs will
        $this->regex = rtrim( $this->url, '/' ) . '/';
        // Custom capture, format: <:var_name|regex>
        $this->regex = preg_replace( '/\<\:(.*?)\|(.*?)\>/', '(?P<\1>\2)', $this->regex );
        // Alphanumeric capture (0-9A-Za-z-_), format: <:var_name>
        $this->regex = preg_replace( '/\<\:(.*?)\>/', '(?P<\1>[A-Za-z0-9\-\_]+)', $this->regex );
        // Numeric capture (0-9), format: <#var_name>
        $this->regex = preg_replace( '/\<\#(.*?)\>/', '(?P<\1>[0-9]+)', $this->regex );
        // Numeric capture (0-9) (optional), format: <?#var_name>
        $this->regex = preg_replace( '/\<\?\#(.*?)\>/', '(?P<\1>[0-9])?', $this->regex );
        // Wildcard capture (Anything INCLUDING directory separators), format: <*var_name>
        $this->regex = preg_replace( '/\<\*(.*?)\>/', '(?P<\1>.+)', $this->regex );
        // Wildcard capture (Anything EXCLUDING directory separators), format: <!var_name>
        $this->regex = preg_replace( '/\<\!(.*?)\>/', '(?P<\1>[^\/]+)', $this->regex );
        // Add the regular expression syntax to make sure we do a full match or no match
        $this->regex = '#^' . $this->regex . '$#';
    }
}