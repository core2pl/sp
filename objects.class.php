<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 18.08.13
 * Time: 21:10
 * To change this template use File | Settings | File Templates.
 */

namespace engine;

/**
 * Klasa zarządzająca obiektami
 *
 * Class objects
 * @package engine
 */
class objects {

    public function __construct() {
//        $this->install('\engine\objects\user');
    }

    /**
     * Metoda tworząca wszysktie obiekty w oparciu o aktualną ścieżkę URL
     *
     * @return array Tablica obiektów na aktualnej ścieżce
     */
    public function getObjects() {
        $db = core::$db;
        $config = core::$config;
        $router = core::$router;
        $obj = array();
        $path = explode('?',urldecode($_SERVER['REQUEST_URI']));
        $path = str_replace($config['site']['root_directory'], '', $path[0]);
        $routings = $db->_select('routings')->_orderBy('priority', false)->_execute(true);
        if (!count($routings)) return;
        foreach ($routings as $routing) {
            if ($router->match($path, $routing['routing'], $vars)) {
                $objects = $db->_select('objects')
                    ->_where('id=:id')->_bind(':id', $routing['object_id'])
                    ->_execute(true);
                foreach ($objects as $object) {
                    $type = $db->_select('types')
                        ->_where('id=:id')->_bind(':id', $object['type_id'])
                        ->_execute();
                    if (!isset($obj[$type['class']]) || count($obj[$type['class']]->vars) < count($vars)) {
                         $obj[$type['class']] = new $type['class']($object['id'],$vars,$routing['routing']);
                    }
                }
            }
        }
//        var_dump($obj);
        return $obj;
    }

    /**
     * Metoda zapisuje do bazy danych nowy tym obiektu oraz tworzy tabele z właściwościami
     *
     * @param $class Nazwa klasy obiektu (wraz z przestrzenią nazw)
     */
    public function install($class) {
        $obj = new $class();
        $db = core::$db;
        $result = $db->_createTable($obj->table)
                     ->_addCol('id', 'integer', true, true)
                     ->_addCol('object_id', 'integer');
        foreach($obj->schema as $col=>$type) {
            $result = $result->_addCol($col, $type);
        }
        $result->_addKey('id', true);
        $result->_addKey('object_id', false, true);
        $result->_execute();
        $db->_insert('types')->_value('class', ':class')->_bind(':class',$class)->_execute();
    }

}