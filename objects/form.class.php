<?php

namespace engine\objects;

class form extends \engine\object {

    public $input;
    public $core;
    public $header;
    public $footer;

    public function __construct($core) {
    	$this->core = $core;
		parent::__construct($core);
    }

	public function showAction() {
	    $this->childs = $this->getChildObjects();
        foreach ($this->childs as $child) {
            $input = $child->showAction();
            $this->input[] = $input;
            $this->input[$child->properties->get('name')] = $input;
        }
        $this->header = $this->core->output->render('form_header.html.twig', array('name' => $this->properties->get('name')));
        $this->footer = $this->core->output->render('form_footer.html.twig', array());
        $this->core->output
            ->set($this->properties->get('name'), $this);

	}
}