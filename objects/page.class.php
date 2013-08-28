<?php

namespace engine\objects;

class page extends \engine\object {

    /**
     * Nazwa tabeli zawierajacej właściwości obiektu
     *
     * @var string
     */
    public $table = 'pages';

    /**
     * Pola tabeli z właściwościami obiektu
     *
     * @var array
     */
    public $schema = array(
        'title' => 'string',
        'description' => 'string',
        'keywords' => 'string',
        'content' => 'text',
        'template' => 'string'
    );

    /**
     * @param int $ID
     * @param array $vars
     */
    public function __construct($ID = null, $vars = null, $routing = null) {
		parent::__construct($ID, $vars, $routing);
    }

    /**
     * @param bool $async
     * @return \engine\output|void
     */
    public function setOutput($async = false) {
        parent::setOutput($async);
        $output = \engine\core::$output;
        $output->set('page', array(
            'title' => $this->properties['title'],
            'content' => (empty($this->properties['template'])) ? $this->properties['content'] : $output->render($this->properties['template'], $output->dataCache),
            'vars' => $this->vars
        ));
        return $output;
    }

}

?>
