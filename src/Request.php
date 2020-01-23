<?php

namespace Mephiztopheles\Routing;

/**
 *
 * Holds request information
 * Class Request
 *
 * @package Routing
 */
class Request {

    public $serverProtocol;
    public $requestMethod;
    public $requestUri;
    public $documentRoot;
    public $body;
    public $query;

    public function __construct () {

        $this->serverProtocol = $_SERVER[ "SERVER_PROTOCOL" ];
        $this->documentRoot   = $_SERVER[ "DOCUMENT_ROOT" ];
        $this->requestUri     = $_SERVER[ "REQUEST_URI" ];
        $this->requestMethod  = $_SERVER[ "REQUEST_METHOD" ];

        $this->body  = $this->getBody();
        $this->query = $this->getQuery();
    }

    /**
     * retrieves POST input decoded from JSON
     *
     * @param bool $assoc
     *
     * @return mixed|null
     */
    public function getBody ( $assoc = false ) {

        if ( $this->requestMethod === "GET" )
            return null;

        return json_decode( file_get_contents( 'php://input' ), $assoc );
    }

    /**
     * retrieves GET input as stdClass and automatically parses numeric parameters
     *
     * @param bool $assoc
     *
     * @return mixed
     */
    public function getQuery ( $assoc = false ) {

        $output = json_decode( json_encode( $_GET ), $assoc );

        foreach ( $output as $k => $v )
            if ( is_numeric( $v ) )
                $output->$k = intval( $v );

        return $output;
    }
}
