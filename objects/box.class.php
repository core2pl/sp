<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 27.08.13
 * Time: 19:54
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


use engine\core;

class box extends \engine\object {

    public $table = 'boxes';

    public $schema = array(
        'name' => 'string',
        'content' => 'text',
        'template' => 'string',
        'title' => 'string'
    );

    public $showInAdminPanel = true;
    public $adminPanelName = 'Boksy';

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
    }

    function setOutput($async = false) {
        $output = core::$output;
        $output->set('box', array(
            'title' => $this->properties['title'],
            'content' => (empty($this->properties['template'])) ? $this->properties['content'] : $output->render($this->properties['template'], $output->dataCache),
            'vars' => $this->vars
        ));
    }
}