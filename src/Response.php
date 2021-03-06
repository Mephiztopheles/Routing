<?php

namespace Mephiztopheles\Routing;

/**
 * Includes shorthands for the response codes
 * Class Response
 *
 * @package Routing
 */
class Response {

    private $content;

    private $status = null;

    public function ok () {
        return $this->status( 200 );
    }

    public function created () {
        return $this->status( 201 );
    }

    public function methodNotAllowed () {
        return $this->status( 405 );
    }

    public function unauthorized () {
        return $this->status( 401 );
    }

    public function notAllowed () {
        return $this->status( 403 );
    }

    public function notFound () {
        return $this->status( 404 );
    }

    public function download ( $content, $filename = "" ) {

        header( "Content-Type: application/x-download" );
        header( "Content-Disposition: attachment; filename=\"$filename\"" );
        header( "Cache-Control: private, max-age=0, must-revalidate" );
        header( "Pragma: public" );

        $this->content = $content;

        return $this;
    }

    public function json ( $content = null ) {

        $this->header( "Content-Type: application/json; charset=UTF-8" );

        if ( isset( $content ) )
            $this->content = json_encode( $content, JSON_UNESCAPED_UNICODE );

        return $this;
    }

    public function text ( $content = null ) {

        $this->header( "Content-Type: text/html; charset=UTF-8" );

        if ( isset( $content ) )
            $this->content = $content;

        return $this;
    }

    public function status ( $status, $message = null ) {

        if ( !isset( $message ) )
            $message = $this->getMessageToStatus( $status );

        $this->status = $status;

        return $this->header( $_SERVER[ "SERVER_PROTOCOL" ] . " $status $message" );
    }

    public function getStatus () {
        return $this->status;
    }

    public function header ( $header ) {

        header( $header );

        return $this;
    }

    public function redirect ( $location ) {
        return $this->header( "Location: $location" );
    }

    public function send () {
        echo $this->content;
    }

    private function getMessageToStatus ( $status ) {

        switch ( $status ) {

            case 200:
                return "OK";
            case 201:
                return "Created";
            case 202:
                return "Accepted";
            case 203:
                return "Non-Authoritative Information";
            case 204:
                return "No Content";
            case 205:
                return "Reset Content";
            case 206:
                return "Partial Content";
            case 207:
                return "Multi-Status";
            case 208:
                return "Already Reported";
            case 226:
                return "IM Used";

            case 300:
                return "Multiple Choices";
            case 301:
                return "Moved Permanently";
            case 302:
                return "Found (Moved Temporarily)";
            case 303:
                return "See Other";
            case 304:
                return "Not Modified";
            case 305:
                return "Use Proxy";
            case 307:
                return "Temporary Redirect";
            case 308:
                return "Permanent Redirect";

            case 400:
                return "Bad Request";
            case 401:
                return "Unauthorized";
            case 402:
                return "Payment Required";
            case 403:
                return "Forbidden";
            case 404:
                return "Not found";
            case 405:
                return "Method Not Allowed";
            case 406:
                return "Not Acceptable";
            case 407:
                return "Proxy Authentication Required";
            case 408:
                return "Request Timeout";
            case 409:
                return "Conflict";
            case 410:
                return "Gone";
            case 411:
                return "Length Required";
            case 412:
                return "Precondition Failed";
            case 413:
                return "Request Entity Too Large";
            case 414:
                return "URI Too Long";
            case 415:
                return "Unsupported Media Type";
            case 416:
                return "Requested range not satisfiable";
            case 417:
                return "Expectation Failed";
            case 420:
                return "Policy Not Fulfilled";
            case 421:
                return "Misdirected Request";
            case 422:
                return "Unprocessable Entity";
            case 423:
                return "Locked";
            case 424:
                return "Failed Dependency";
            case 426:
                return "Upgrade Required";
            case 428:
                return "Precondition Required";
            case 429:
                return "Too Many Requests";
            case 431:
                return "Request Header Fields Too Large";
            case 451:
                return "Unavailable For Legal Reasons";

            case 500:
                return "Internal Server Error";
            case 501:
                return "Not Implemented";
            case 502:
                return "Bad Gateway";
            case 503:
                return "Service Unavailable";
            case 504:
                return "Gateway Timeout";
            case 505:
                return "HTTP Version not supported";
            case 506:
                return "Variant Also Negotiates";
            case 507:
                return "Insufficient Storage";
            case 508:
                return "Loop Detected";
            case 509:
                return "Bandwidth Limit Exceeded";
            case 510:
                return "Not Extended";
            case 511:
                return "Network Authentication Required";

            default:
                return "";
        }
    }
}