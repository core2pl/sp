<?php
/**
 * Created by JetBrains PhpStorm.
 * User: krzysztof_moskalik
 * Date: 05.07.13
 * Time: 14:18
 * To change this template use File | Settings | File Templates.
 */

namespace engine;


class error {

    public function __construct($code, $data = null) {
        switch ($code) {
            case '404': header('HTTP/1.0 404 Not Found');
        }
        require_once './Twig/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem(array('./views/errors', './engine/views/errors'));
        $this->twig = new \Twig_Environment($loader);
        echo $this->twig->render($code.'.html.twig', $data);
        exit;
    }

}