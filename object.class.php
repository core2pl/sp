<?php

namespace engine;

/**
 * Bazowa klasa każdego obiektu
 *
 * Class object
 * @package engine
 */
class object {

    /**
     * @var int $ID ID obiektu
     */
    public $ID;

    /**
     * @var array Tablica parametrów wydobytych z URI przez router
     */
    public $vars;

    /**
     * @var string
     */
    public $routing;

    /**
     * Konstruktur pobierający dodatkowe parametry obiektu z bazy dancyh
     *
     * @param int $ID ID obiektu
     * @param array $vars Tablica parametrów wydobytych z URI przez router
     */
    public function __construct($ID = null, $vars = null, $routing = null) {
        $this->ID = $ID;
        $this->vars = $vars;
        $this->routing = $routing;
        $db = core::$db;
        $properties = $db->_select($this->table)
                               ->_where('object_id=:id')->_bind(':id', $this->ID)
                               ->_execute(false);
        if (!$properties) return;
        foreach ($properties as $name=>$value) {
            if (!in_array($name, array('id', 'object_id'))) {
                $this->properties[$name] = $value;
            }
        }
    }

    /**
     * Metoda zapisująca dodatkowe dane do bufora wyjściowego
     *
     * @param bool $async Parametr defiiujący czy strona została wywołana asynchronicznie przez AJAX
     */
    public function setOutput($async = false) {

    }

}