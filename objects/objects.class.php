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
        if (isset($vars[':action']) && isset($vars[':object_id'])) {
            $db = core::$db;
            $dbo = $db->_select('objects')
                ->_where('id=:id')->_bind(':id', $this->vars[':object_id'])
                ->_execute(false);
            $type = $db->_select('types')
                ->_where('id=:id')->_bind(':id', $dbo['type_id'])
                ->_execute(false);

            $obj = new $type['class']($this->vars[':object_id'] , null, null);
            $obj->class = $type['class'];
            switch ($vars[':action']) {
                case 'update': {
//                    var_dump($_POST);
//                    if ($obj->table) {
                        $result = $db->_update($obj->table);
                                   var_dump($_POST);
                        foreach($obj->schema as $key=>$type) {
                            $result = $result->_set($key, ':'.$key)->_bind(':'.$key, $_POST[$key]);
                        }
                        $result->_where('object_id = :oid')->_bind(':oid', $obj->ID)->_execute();
//                        var_dump($result);
//                    }
                }
                    break;
            }
        }
    }

    public function setOutput($async = false) {
        $out = core::$output;
        $db = core::$db;
        $dbo = $db->_select('objects')
            ->_where('id=:id')->_bind(':id', $this->vars[':object_id'])
            ->_execute(false);
        $type = $db->_select('types')
            ->_where('id=:id')->_bind(':id', $dbo['type_id'])
            ->_execute(false);
        $obj = new $type['class']($this->vars[':object_id'] , null, null);
        $obj->class = $type['class'];

        if (isset($this->vars[':action']) && isset($this->vars[':object_id'])) {
            switch ($this->vars[':action']) {
                case 'edit':
                case 'update': {
                    $this->content = $out->render($this->template, array('object' => $obj, 'action' => $this->vars[':action']));
                }
                    break;
            }
        }

        $out->set('page', $this);
    }
}