<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 06.07.13
 * Time: 16:37
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


class users extends \engine\object {

    public $core;

    public function __construct($core) {
        $this->core = $core;
        parent::__construct($core);
    }

    public function showAction() {
        if ($this->core->input->get('login_username') != false && $this->core->input->get('login_password') != false) {
            var_dump('login action');
            foreach ($users as $user) {
                if ($user->getName() == $this->core->input->get('login_username') && $user->getPassword() == md5($this->core->input->get('login_password'))) {
                    var_dump('login success');
                    $this->core->output->setSession('user', $user->getName());
                    $this->core->user = $user;
                }
            }
        }
    }

}