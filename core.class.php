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
    public static $path;
    public $entities;

    /**
     * Konstruktor tworzący obiekty wszystkich potrzebnych modułów
     */
    public function __construct() {
        session_start();
        self::$router = new router();
        self::$input = new input();
        self::$path = self::$input->getPath();
        self::$config = self::$input->getConfig();
        self::$db = new db();
        self::$output = new output();
        self::$objects = new objects();

//        $page = new \engine\objects\page();
//        self::$objects->add($page, '/login');
        $this->entities = self::$objects->getEntities();
//        $page = $this->entities[3];
  //      $page->title = 'strona logowania 1';
//        $page->save();

//        var_dump($this->entities);



       // var_dump(getenv('DB_NAME'));
     //   var_dump(get_object_vars($this));
    }

    /**
     * Metoda wyświetlająca zawartość strony
     */
    public function showSite() {

        self::$output->showSite($this);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name) {
        return self::$$name;
    }

}