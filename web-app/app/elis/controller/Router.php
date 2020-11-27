<?php

namespace elis\controller;

use elis\utils\Conf;
use elis\presenter;

/**
 * Router controller
 * @version 0.0.1 201121 created
 */
class Router
{

    /**
     * Load and run the presenter by uri
     *
     * @param string $uri
     * @return void
     */
    public static function route(string $uri)
    {
        $params = explode('/', substr($uri, strlen(Conf::get('URL_DIR'))));
        if (empty($params[0])) {
            (new presenter\Home([]))->run();
        } else {
            $classClassName = 'elis\\presenter\\' . ucfirst(array_shift($params));
            try {
                $presenter = new $classClassName($params);
                if (method_exists($classClassName, 'run')) {
                    $presenter->run();
                }
            } catch (Exception $e) {
                (new presenter\Error(['404']))->run();
            }
        }
    }

    /**
     * Redirect the page to the specific uri
     *
     * @return void
     */
    public static function redirect(string $uri)
    {
        header("Location: " . (strpos($uri, 'http') === 0 ? '' : Conf::get('URL_BASE') . Conf::get('URL_DIR')) . $uri);
        header("Connection: close");
        exit;
    }
}
