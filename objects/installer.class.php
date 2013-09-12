<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 12.09.13
 * Time: 21:23
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


use engine\core;

class installer {

    public $template = 'installer.html.twig';
    public $content;
    public $vars;

    public function __construct($ID = null, $vars = null, $routing = null) {
        $this->vars = $vars;
    }

    public function setOutput() {
        $this->content = core::$output->render($this->template, array('action' => $this->vars[':action']));
        core::$output->set('page', $this);
    }

}