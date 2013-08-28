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
        'password' => 'password',
        'email' => 'string'
    );

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
        if (isset($vars[':action'])) {
            switch ($vars[':action']) {
                case 'login': {
                    if (isset($_POST['email']) && isset($_POST['password']) ){
                        if ($_POST['email'] == $this->properties['email'] && md5($_POST['password']) == $this->properties['password']) {
                            $_SESSION['user_id'] = $this->ID;
                        } elseif ($_POST['email'] == $this->properties['email']) {
                            header('Location: /user/login-failure');
                        }
                    }
                }
                    break;
                case 'logout': {
                        unset($_SESSION['user']);
                        session_unset();
                        session_destroy();
                        header('Location: /');
                }
                    break;
            }
        }
    }

    public function setOutput($async = false) {
        $out = core::$output;
        $out->set('user', array(
            'id' => $this->ID,
            'logged' => $this->isLoggedIn(),
            'email' => $this->properties['email'],
            'login_failure' => (($this->vars[':action'] == 'login-failure') ? true : false)
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