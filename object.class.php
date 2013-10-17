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
        if ($this->table && $db->_tableExists($this->table) && $this->ID != null) {
            $properties = $db->_select($this->table)
                ->_where('object_id=:id')->_bind(':id', $this->ID)
                ->_execute(false);
            if (!$properties) return;
            foreach ($properties as $name=>$value) {
                if (!in_array($name, array('id', 'object_id'))) {
                    $this->properties[$name] = $value;
                    $this->$name = $value;
                }
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

    public function save() {
        if ($this->table == false || empty($this->schema)) { return false; }
        $obj = core::$db->_select($this->table)->_where('id='.$this->ID)->_execute(true);
//        var_dump($this);
        if ($obj != null) {
            $stmt = core::$db->_update($this->table);
            foreach ($this->schema as $key=>$type) {
                $stmt->_set($key, ':'.$key)->_bind(':'.$key, $this->$key);
            }
            $stmt = $stmt->_where('object_id='.$this->ID)->_execute(true);
            var_dump($stmt);
        } else {
            $stmt = core::$db->_insert($this->table);
            foreach ($this->schema as $key=>$type) {
                $stmt->_value($key, ':'.$key)->_bind(':'.$key, $this->$$key);
            }
            $stmt->_execute(true);
        }
    }

    public function set($data) {
        foreach ($data as $key=>$value) {
            $this->$key = $value;
        }
        return $this;
    }

}