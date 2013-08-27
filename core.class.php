<?php

namespace engine;


/**
 * Class core
 *
 * Main engine class
 *
 * @package engine
 * @autor mariush
 */
class core {

    public static $router;
    public static $input;
    public static $config;
    public static $db;
    public static $output;
    public static $objects;
    public static $pathObjects;

    /**
     * Konstruktor tworzący obiekty wszystkich potrzebnych modułów
     */
    public function __construct() {
        session_start();
        self::$router = new router();
        self::$input = new input();
        self::$config = self::$input->getConfig();
        self::$db = new db();
        self::$output = new output();
        self::$objects = new objects();
        self::$pathObjects = self::$objects->getObjects();

    }

    /**
     * Metoda wyświetlająca zawartość strony
     */
    public function showSite() {
        self::$output->showSite();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name) {
        return self::$$name;
    }

}