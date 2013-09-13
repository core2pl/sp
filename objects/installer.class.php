<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marian
 * Date: 12.09.13
 * Time: 21:23
 * To change this template use File | Settings | File Templates.
 */

namespace engine\objects;


use engine\core;

class installer {

    public $template = 'installer.html.twig';
    public $content;
    public $vars;
    public $error = false;
    public $themes = array();
    public $config;

    public function __construct($ID = null, $vars = null, $routing = null) {
        $this->vars = $vars;
        switch ($vars[':action']) {
            case 'database': {
                if (isset($_POST['db_type'])) { //TODO check all post vars
                    if (!core::$db->connect(array('db' => $_POST))) {
                        $this->error = true;
                    } else {
                        $db = array(
                            'db_type' => $_POST['db_type'],
                            'db_host' => $_POST['db_host'],
                            'db_user' => $_POST['db_user'],
                            'db_pass' => $_POST['db_pass'],
                            'db_name' => $_POST['db_name']
                        );
//                        var_dump(core::$config);
                        if (!file_exists('./config.json')) {
                            file_put_contents('./config.json', json_encode(array(
                                'db' => $db,
                                'theme' => core::$config['theme'],
                                'routings' => core::$config['routings'],
                                'site' => array(
                                    'root_directory' => core::$config['site']['root_directory']
                                )
                            ), \JSON_PRETTY_PRINT));
                        }
                        header('Location: /install/addones');
                    }
                }
            } break;
            case 'addones' : {
                if (isset($_POST['theme']) && !file('./themes/'.str_replace('theme_', '', $_POST['theme'].'/'))) {
                    var_dump(exec('git clone http://github.com/core2pl/'.$_POST['theme'].'.git ./themes/'.str_replace('theme_', '', $_POST['theme'])));
                }
                if (isset($_POST['theme'])) {
                    core::$config['theme'] = str_replace('theme_', '', $_POST['theme']);
                    file_put_contents('./config.json', json_encode(core::$config, \JSON_PRETTY_PRINT));
                    header('Location: /install/user');
                }
            } break;
            case 'user': {
                if (isset($_POST['email'])) {
                    core::$db->_createTable('objects')
                        ->_addCol('id', 'integer', true, true)
                        ->_addCol('type_id', 'integer')
                        ->_addCol('priority', 'integer')
                        ->_addCol('name', 'string')
                        ->_addKey('id', true)
                        ->_addKey('type_id', false, true)
                        ->_execute();

                    core::$db->_createTable('routings')
                        ->_addCol('id', 'integer', true, true)
                        ->_addCol('object_id', 'integer')
                        ->_addCol('routing', 'string')
                        ->_addCol('priority', 'integer')
                        ->_addKey('id', true)
                        ->_addKey('object_id', false, true)
                        ->_execute();

                    core::$db->_createTable('types')
                        ->_addCol('id', 'integer', true, true)
                        ->_addCol('class', 'string')
                        ->_addKey('id', true)
                        ->_execute();

                    core::$objects->install('\engine\objects\page');
                    core::$objects->install('\engine\objects\user');
                    core::$objects->install('\engine\objects\users');

                    core::$db->_insert('objects')
                            ->_value('type_id', ':type')->_bind(':type', 1)
                            ->_value('priority', ':prior')->_bind(':prior', 2)
                            ->_value('name', ':name')->_bind(':name', 'Home')
                            ->_execute();

                    core::$db->_insert('objects')
                        ->_value('type_id', ':type')->_bind(':type', 2)
                        ->_value('priority', ':prior')->_bind(':prior', 1)
                        ->_value('name', ':name')->_bind(':name', '')
                        ->_execute();

                    core::$db->_insert('objects')
                        ->_value('type_id', ':type')->_bind(':type', 3)
                        ->_value('priority', ':prior')->_bind(':prior', 1)
                        ->_value('name', ':name')->_bind(':name', '')
                        ->_execute();

                    core::$db->_insert('routings')
                        ->_value('object_id', ':oid')->_bind(':oid', 1)
                        ->_value('routing', ':routing')->_bind(':routing', '/')
                        ->_value('priority', ':prior')->_bind(':prior', 2)
                        ->_execute();

                    core::$db->_insert('routings')
                        ->_value('object_id', ':oid')->_bind(':oid', 2)
                        ->_value('routing', ':routing')->_bind(':routing', '/:*')
                        ->_value('priority', ':prior')->_bind(':prior', 1)
                        ->_execute();

                    core::$db->_insert('routings')
                        ->_value('object_id', ':oid')->_bind(':oid', 3)
                        ->_value('routing', ':routing')->_bind(':routing', '/:*')
                        ->_value('priority', ':prior')->_bind(':prior', 1)
                        ->_execute();

                    core::$db->_insert('pages')
                         ->_value('object_id', ':oid')->_bind(':oid', 1)
                         ->_value('template', ':tpl')->_bind(':tpl', 'page.html.twig')
                         ->_execute();

                    core::$db->_insert('users')
                        ->_value('object_id', ':oid')->_bind(':oid', 2)
                        ->_value('email', ':email')->_bind(':email', $_POST['email'])
                        ->_value('password', ':pass')->_bind(':pass', md5($_POST['password']))
                        ->_value('admin', ':admin')->_bind(':admin', '1')
                        ->_execute();
                    header('Location: /');
                }
            }
        }
    }

    public function setOutput() {
//        var_dump($this);
        switch ($this->vars[':action']) {
            case 'database': {
                if (isset(core::$config['db'])) {
                    $this->config = core::$config['db'];
                    $this->config['mysql'] = ((core::$config['db']['db_type'] == 'mysql') ? true : false);
                    $this->config['sqlite'] = ((core::$config['db']['db_type'] == 'sqlite') ? true : false);
                }
            } break;
            case 'addones': {
                $repos = json_decode(file_get_contents('https://api.github.com/users/core2pl/repos'));
                foreach($repos as $repo) {
                    if (preg_match('#^theme_.*#', $repo->name)) {
                        $this->themes[] = $repo;
                    }
                }
            } break;
        }
        $this->content = core::$output->render($this->template, array(
            'action' => $this->vars[':action'],
            'error' => $this->error,
            'themes' => $this->themes,
            'config' => $this->config
        ));
        core::$output->set('page', $this);
    }

}