<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 28.08.13
 * Time: 21:27
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


use engine\core;

class objects extends \engine\object {

    protected $table = false;
    protected $template = 'objects.html.twig';

    public $content;

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
//        var_dump($vars);
        if (isset($vars[':action']) && isset($vars[':object_id'])) {
            switch ($vars[':action']) {
                case 'update': {
                    $db = core::$db;
                    $dbo = $db->_select('objects')
                        ->_where('id=:id')->_bind(':id', $this->vars[':object_id'])
                        ->_execute(false);
                    $type = $db->_select('types')
                        ->_where('id=:id')->_bind(':id', $dbo['type_id'])
                        ->_execute(false);

                    $obj = new $type['class']($this->vars[':object_id'] , null, null);
                    $obj->class = $type['class'];
                    if ($obj->table) {
                        $result = $db->_update($obj->table);
                        foreach($obj->schema as $key=>$type) {
                            $result = $result->_set($key, ':'.$key)->_bind(':'.$key, $_POST[$key]);
                        }
                        $result->_where('object_id = :oid')->_bind(':oid', $obj->ID)->_execute();
                    }
                }
                    break;
            }
        }
    }

    public function setOutput($async = false) {
        $out = core::$output;
        $db = core::$db;


        if (isset($this->vars[':action'])) {
            switch ($this->vars[':action']) {
                case 'edit':
                case 'update': {
                    if (isset($this->vars[':object_id'])) {
                        $dbo = $db->_select('objects')
                            ->_where('id=:id')->_bind(':id', $this->vars[':object_id'])
                            ->_execute(false);
                        $type = $db->_select('types')
                            ->_where('id=:id')->_bind(':id', $dbo['type_id'])
                            ->_execute(false);
                        $obj = new $type['class']($this->vars[':object_id'] , null, null);
                        $obj->class = $type['class'];
                        $this->content = $out->render($this->template, array('object' => $obj, 'action' => $this->vars[':action']));
                    }
                }
                    break;
                case 'show': {
                    $types = array();
                    $rows = $db->_select('types')->_execute(true);
                    foreach ($rows as $row) {
                        $objects = $db->_select('objects')
                                      ->_where('type_id=:tid')->_bind(':tid', $row['id'])
                                      ->_execute(true);
                        $types[] = array(
                            'id' => $row['id'],
                            'class' => $row['class'],
                            'count' => count($objects)
                        );
                    }
                    $this->content = $out->render($this->template, array('types' => $types, 'action' => $this->vars[':action']));
                }
                    break;
                case 'show-objects' : {
                    if (isset($this->vars[':type_id'])) {
                        $objects = array();
                        $rows = $db->_select('objects')
                            ->_where('type_id=:tid')->_bind(':tid', $this->vars[':type_id'])
                            ->_execute(true);
                        foreach ($rows as $row) {
                            $objects[] = array(
                                'id' => $row['id'],
                                'name' => $row['name']
                            );
                        }
                        $this->content = $out->render($this->template, array('objects' => $objects, 'action' => $this->vars[':action']));
                    }
                }
                    break;
            }
        }
//        var_dump($this);
        $out->set('page', $this);
    }
}