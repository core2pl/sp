<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 06.07.13
 * Time: 17:00
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


use engine\core;

class user extends \engine\object {

    public $table = 'users';

    public $schema = array(
        'email' => 'string',
        'password' => 'password',
        'admin' => 'boolean'
    );
    public $email;

    public $admin = false;

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
        $this->admin = ($this->properties['admin']) == 1 ? true : false;
        $this->email = $this->properties['email'];
        $this->properties['email'];
    }

    public function setOutput($async = false) {
        $out = core::$output;
    }

    public function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            return true;
        } else {
            return false;
        }
    }

}