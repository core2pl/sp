<?php

namespace engine\objects;

class page extends \engine\object {

    /**
     * Nazwa tabeli zawierajacej właściwości obiektu
     *
     * @var string
     */
    public  $table = 'pages';

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

    public $content;

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
        if (empty($this->properties['template'])) {
            $content = $this->properties['content'];
        } else {
            $this->content = $this->properties['content'];
            $content = $output->render($this->properties['template'], array('page' => $this));
        }

        $output->set('page', array(
            'title' => $this->properties['title'],
            'content' => $content,
            'vars' => $this->vars
        ));
        return $output;
    }

}

?>
