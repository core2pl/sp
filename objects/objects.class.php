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

    public $table = false;
    public $template = 'objects.html.twig';

    public $showInAdminPanel = false;
    public $adminPanelName = 'Obiekty';

    public $content;

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
//        var_dump($vars);
        if (isset($vars[':action'])) {
            switch ($vars[':action']) {
                case 'update': {
                    if (isset($vars[':object_id']))
                    {
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
                }
                    break;
                case 'add-process': {
                    if (isset($vars[':type_id'])) {
                        $db = core::$db;
                        $type = $db->_select('types')
                                   ->_where('id=:tid')->_bind(':tid', $vars[':type_id'])
                                   ->_execute(false);
                        $obj = new $type['class']();
//                        var_dump($obj);
                        $db->_insert('objects')
                           ->_value('type_id', ':tid')->_bind(':tid', $vars[':type_id'])
                           ->_value('priority', ':prior')->_bind(':prior', ((isset($obj->priority)) ? $obj->priority : 1 ))
                           ->_value('name', ':name')->_bind(':name', $_POST['o_name'])
                           ->_execute();
                        $id = $db->_lastId();
//                        var_dump($id);
                        $stmt = $db->_insert($obj->table);
                        foreach($obj->schema as $col=>$type) {
                            $stmt = $stmt->_value($col, ':'.$col)->_bind(':'.$col, $_POST[$col]);
                        }
                        $stmt->_value('object_id', ':oid')->_bind(':oid', $id)
                             ->_execute();
                    }
                }
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
                        $obj = new $row['class'](null, null, null);
                        if (isset($obj->showInAdminPanel) && $obj->showInAdminPanel == true) {
                            $types[] = array(
                                'id' => $row['id'],
                                'class' => $row['class'],
                                'count' => count($objects),
                                'name' => (isset($obj->adminPanelName)) ? $obj->adminPanelName : $row['class']
                            );
                        }
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
                case 'add': {
                    if (!isset($this->vars[':type_id'])) {
                        $types = array();
                        $rows  = $db->_select('types')->_execute(true);
                        foreach ($rows as $row) {
                            $obj = new $row['class']();
                            if (isset($obj->showInAdminPanel) && $obj->showInAdminPanel == true) {
                                $types[] = array (
                                    'id' => $row['id'],
                                    'name' => $obj->adminPanelName
                                );
                            }
                            unset($obj);
                        }
                        $this->content = $out->render($this->template, array('types' => $types, 'action' => $this->vars[':action']));
                    } else {
                        $type = $db->_select('types')
                                   ->_where('id=:tid')->_bind(':tid', $this->vars[':type_id'])
                                   ->_execute(false);

                        $this->content = $out->render($this->template, array(
                            'type' => $type,
                            'object' => new $type['class'](),
                            'action' => $this->vars[':action']
                        ));
                    }
                }
                    break;
            }
        }
//        var_dump($this);
        $out->set('page', $this);
    }
}