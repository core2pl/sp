<?php

namespace engine\objects\form\input;

class text extends \engine\object {

    public $core;
    
    public function __construct($core) {
        $this->core = $core;
        parent::__construct($core);
    }

    public function showAction() {
        return $this->core->output->render('text_input.html.twig', array('name' => $this->properties->get('name')));
    }    
}