<?php
/**
 * Created by JetBrains PhpStorm.
 * User: krzysztof_moskalik
 * Date: 28.08.13
 * Time: 09:49
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


use engine\core;

class installer extends \engine\object {

    public $table = false;

    private $installed = false;

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
        $objects = core::$objects;
//        var_dump($vars);
//        var_dump($this->vars);
        if (isset($vars[':action'])) {
            switch ($vars[':action']) {
                case 'install': {
                    if (isset($_POST['class-name'])) {
                        $objects->install($_POST['class-name']);
                        $this->installed = $_POST['class-name'];
                    }
                    break;
                }
            }
        }
    }

    public function setOutput($async = false) {
        $out = core::$output;
        if ($this->installed) {
            $out->set('installer', array(
               'installed' => true,
                'class' => $this->installed
            ));
        }
    }
}