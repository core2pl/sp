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
        $config = core::$config;
        $router = core::$router;
        $obj = array();
        $path = explode('?',urldecode($_SERVER['REQUEST_URI']));
        if ($config['site']['root_directory'] != '/') {
            $path = preg_replace('#'.$config['site']['root_directory'].'#', '', $path[0],1);
        } else {
            $path = $path[0];
        }
//        $path = str_replace($config['site']['root_directory'], '', $path[0]);
//            var_dump($db->isConnected());
        $routings = $db->_select('routings')->_orderBy('priority', false)->_execute(true);
//        var_dump($routings);
        if ($db->isConnected() && $routings != null){
            foreach ($routings as $routing) {
//                var_dump($routing);
                if ($router->match($path, $routing['routing'], $vars)) {
                    $objects = $db->_select('objects')
                        ->_where('id=:id')->_bind(':id', $routing['object_id'])
                        ->_execute(true);
                    foreach ($objects as $object) {
                        $type = $db->_select('types')
                            ->_where('id=:id')->_bind(':id', $object['type_id'])
                            ->_execute();
//                    if (!isset($obj[$type['class']]) || count($obj[$type['class']]->vars) < count($vars)) {
                        $obj[$type['class']] = new $type['class']($object['id'],$vars,$routing['routing']);
//                    }
                    }
                }
            }
        } else {
            $routings = $config['routings'];
//            var_dump($routings);
            foreach ($routings as $routing => $class) {
//                var_dump($path);
                if ($router->match($path, $routing, $vars)) {
//                    var_dump('match!');
                    $obj[$class] = new $class(null,$vars,$routing);
                }
            }
//            var_dump($obj);
        }


        if (!count($routings)) return;

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
        if ($obj->table) {
            $result = $db->_createTable($obj->table)
                ->_addCol('id', 'integer', true, true)
                ->_addCol('object_id', 'integer');
            foreach($obj->schema as $col=>$type) {
                $result = $result->_addCol($col, $type);
            }
            $result->_addKey('id', true);
            $result->_addKey('object_id', false, true);
            $result->_execute();
        }
        $db->_insert('types')->_value('class', ':class')->_bind(':class',$class)->_execute();
    }

}