<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 06.07.13
 * Time: 16:37
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


use engine\core;

class users extends \engine\object {

    public $user = null;
    public $content;
    public $table = false;
    public $action = false;
    public $template = 'users.html.twig';

    public $showInAdminPanel = false;
    public $adminPanelName = 'Użytkownicy';

    public function __construct($ID = null, $vars = null, $routing = null) {
        parent::__construct($ID, $vars, $routing);
        $db = core::$db;
        if (isset($vars[':action'])) {
            $this->action = $vars[':action'];
            switch ($vars[':action']) {
                case 'login' : {
                    if (isset($_POST['email']) && isset($_POST['password'])) {
                        $usr = $db->_select('users')
                                   ->_where('email=:email')->_bind(':email', $_POST['email'])
                                   ->_and('password=:pwd')->_bind(':pwd', md5($_POST['password']))
                                   ->_execute(false);
                        if (!$usr) {
                            header('Location: /login-failure');
                            return false;
                        }
                        $obj = $db->_select('objects')
                                  ->_where('id=:id')->_bind(':id', $usr['object_id'])
                                  ->_execute(false);
                        $type = $db->_select('types')
                                   ->_where('id=:id')->_bind(':id', $obj['type_id'])
                                   ->_execute(false);
                        $this->user =  new $type['class']($obj['id'], null, null);
                        $_SESSION['user'] = $this->user;
                        return $this->user;
                    }
                }
                    break;
                case 'logout': {
                    $this->user = null;
                    unset($_SESSION['user']);
                    session_unset();
                    session_destroy();
//                    header('Location: /');
                }
                    break;
            }
        }
        if (isset($_SESSION['user'])) {
            $this->user = $_SESSION['user'];
            return true;
        }
    }

    public function setOutput($async = false) {
        parent::setOutput($async);
        $out = core::$output;
        $out->set('users', $this);
        if ($this->vars[':action'] == 'login-failure') {
            $this->login_failure = true;
        }
        $out->set('box', $out->render('login.html.twig', array('users' => $this)));
    }

}