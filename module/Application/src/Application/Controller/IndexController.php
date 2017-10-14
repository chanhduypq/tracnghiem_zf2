<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController {

    public function indexAction() {
        $model = new \Application\Model\Table('home_content');
        $rows = $model->getAll();
        $row = $rows[0];
        return new ViewModel(array('content' => $row['content']));
    }

    public function guideAction() {
        \Zend\Common\Download::download("guide/");
    }

    public function loginAction() {
        $username = $this->getRequest()->getPost('username', null);
        $password = $this->getRequest()->getPost('password', null);
        $model = new \Application\Model\Table('user');
        $rows = $model->select("email = '$username' and password='". sha1($password)."'")->toArray();
        if (is_array($rows)&&count($rows)>0) {
            echo '';
            $session = new Container('base');
            $session->offsetSet('user', $rows[0]);
        } else {
            echo 'error';
        }
        return $this->getResponse();
    }

    public function logoutAction() {
        $session = new Container('base');
        $session->offsetSet('user', NULL);
        return $this->redirect()->toUrl('/');   
    }

    public function logoutajaxAction() {
        $session = new Container('base');
        $session->offsetSet('user', NULL);
        exit;
    }

}
