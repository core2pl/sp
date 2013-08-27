<?php

namespace engine;

/**
 * Klasa zarządzająca danymi wejściowymi
 *
 * Class input
 * @package engine
 */
class input {
    /**
     * @var Konfiguracja strony pobrana z pliku
     */
    public $config;

    /**
     * @var Parametry pobrane z tablicy $_GET
     */
    public $params;

    /**
     * @var Parametry pobrane z tablicy $_POST
     */
    public $form;

    public function __construct() {
//        $this->getParams();
//        $this->getFormParams();
        return $this;
    }

    /**
     * Metoda pobierająca konfigurację strony z pliku
     *
     * @param null $part @deprecated
     * @return mixed Zwraca całość lub fragment konfiguracji, lub false w przypadku błędu
     */
    public function getConfig($part = null) {
        require './config.php';
        $this->config = $config; 
        if ($part == null) {
            return $config;
        } elseif(isset($config[$part])) {
            return $config[$part];
        }
        return false;
    }

    /**
     * @deprecated
     */
    public function getParams() {
        if (isset($_GET)) {
            $this->params = $_GET;
        }
    }

    /**
     * @deprecated
     */
    public function getFormParams() {
        if (isset($_POST)) {
            $this->form = $_POST;
        }
    }

    /**
     * @deprecated
     */
    public function get($key) {
        if (isset($this->form[$key])) {
            return $this->form[$key];
        } elseif (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return false;
    }

    /**
     * Metoda sprawdzająca czy wywołanie strony nastąpiło asynchrnocznie za pomocą AJAX'a
     *
     * @return bool Zwraca true jeżeli użyte zostało żadanie AJAX, w przeciwnym wypadku false
     */
    public function isAjaxRequest() {
        return ($this->get('new_url')) ? true : false;
    }
    
}

?>
