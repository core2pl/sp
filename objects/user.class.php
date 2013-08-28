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
        'login' => 'string',
        'password' => 'string',
        'email' => 'string'
    );

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
        if (isset($vars[':action']) && $vars[':action'] == 'login' && isset($_POST['email']) && isset($_POST['email'])
            && $_POST['email'] == $this->properties['email'] && md5($_POST['password']) == $this->properties['password']) {
            $_SESSION['user_id'] = $this->ID;
        }
        if (isset($vars[':action']) && $vars[':action'] == 'logout') {
            unset($_SESSION['user']);
            session_unset();
            session_destroy();
            header('Location: /');
        }
    }

    public function setOutput($async = false) {
        $out = core::$output;
        $out->set('user', array(
            'logged' => $this->isLoggedIn(),
            'email' => $this->properties['email'],
            'action' => $this->vars[':action']
        ));
    }

    public function isLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            return true;
        } else {
            return false;
        }
    }

}