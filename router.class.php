<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 18.08.13
 * Time: 17:30
 * To change this template use File | Settings | File Templates.
 */

namespace engine;

/**
 * Klasa Routera, parsująca adres URI w kontekscie routingu
 *
 * Class router
 * @package engine
 */
class router {

    /**
     * Metoda sprawdza, czy podany adres URI pasuje do schematu routing'u
     *
     * @param $uri Adres URI
     * @param $routing Routing do sprawdzenia
     * @param $vars Jezeli w routing'u podane są parametry do sparsowania
     * to są one zapisywane w tej zmiennej
     * @return bool Zwraca true jeżeli routing pasuje do adresu URI,
     * w przeciwnym wypadku false
     */
    public function match($uri, $routing, &$vars) {
        $variables = array();
        preg_match_all('#:[a-zA-Z0-9_*]+#',$routing, $vars);
        $uri_array = preg_split('#[\s/]+#', $uri);
        $routing_array = preg_split('#[\s/]+#', $routing);
        if (in_array(':**', $routing_array)) return true;
        foreach ($uri_array as $key=>$step) {
//            var_dump($routing_array);
            if (!isset($routing_array[$key])) {
                return false;
            } elseif (preg_match('#:[a-zA-Z0-9_\*]#', $routing_array[$key])) {
                $variables[$routing_array[$key]] = $uri_array[$key];
//                var_dump($routing_array[$key]);
                if (($routing_array[$key] == ':**') || ($routing_array[$key] == ':*' && $key >= array_search(':*', $routing_array))) {
                    $vars = $variables;
//                    var_dump('ok');
                    return true;
                }
            } elseif ($routing_array[$key] != $uri_array[$key]) {
                return false;
            } elseif (count($routing_array) != count($uri_array)) {
                return false;
            }

        }
        $vars = $variables;
        return true;
    }

}