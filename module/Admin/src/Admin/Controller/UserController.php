<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class UserController extends AbstractActionController {

//    public function init() {
//        parent::init();
//        $this->model = new \Application\Model\User();
//        $this->form = new Admin_Form_User();
//    }

    public function indexAction() {

        $params= array();
//        $paginator=new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Null(0));
//        $paginator->setDefaultScrollingStyle();
//        $paginator->setDefaultScrollingStyle();
//        $paginator->setItemCountPerPage($this->limit);
//        $paginator->setCurrentPageNumber($this->page);
//
//        $params['paginator'] = $paginator;
//        $params['limit'] = $this->limit;
//        $params['total'] = $this->total;
//        $params['page'] = $this->page;
//        $params['message'] = $this->getMessage();
     
        $this->model = new \Application\Model\User();
        $rows = $this->model->getUsers($this->total, $this->limit, $this->start);
        $params['items'] = $rows;
        return new ViewModel($params);
    }

    public function addAction() {
        if (is_array($this->formData) && count($this->formData) > 0) {
            $this->formData['password'] = sha1($this->formData['email']);
        }
        $this->view->page = $this->params()->fromQuery('page');

        $this->renderScript = 'user/add.phtml';
    }

    public function editAction() {
        $this->view->page = $this->params()->fromQuery('page');

        
        $id = $this->params()->fromQuery('id');
        $history = $db->fetchAll("select user_exam.allow_re_exam,user_exam.user_id,user_exam.nganh_nghe_id,user_exam.level,user_exam.id,user_exam.exam_date,user_pass.user_exam_id,nganh_nghe.title from user_exam JOIN nganh_nghe ON nganh_nghe.id=user_exam.nganh_nghe_id LEFT JOIN user_pass ON user_pass.user_exam_id=user_exam.id WHERE user_exam.user_id=$id ORDER BY user_exam.exam_date ASC");
        $this->view->history = $history;

        $this->renderScript = 'user/add.phtml';
    }

    public function deleteAction() {
    }

    public function allowreexamAction() {
        $user_id = $this->params()->fromQuery('user_id', null);
        $exam_id = $this->params()->fromQuery('exam_id', null);
        
        
        $adapter = new \Zend\Db\Adapter\Adapter();
        $adapter->createStatement('UPDATE user_exam SET allow_re_exam=1 WHERE id=' . $exam_id)->execute();
        return $this->redirect()->toUrl('/admin/user/edit/?id='.$user_id); 
    }

    public function cancelreexamAction() {
        $user_id = $this->params()->fromQuery('user_id', null);
        $exam_id = $this->params()->fromQuery('exam_id', null);
        
        $adapter = new \Zend\Db\Adapter\Adapter();
        $adapter->createStatement('UPDATE user_exam SET allow_re_exam=0 WHERE id=' . $exam_id)->execute();
        
        return $this->redirect()->toUrl('/admin/user/edit/?id='.$user_id); 
    }

    public function ketquathiAction() {
        $user_exam_id = $this->params()->fromQuery('user_exam_id');
        $html = \Application\Model\Userexam::getHtmlForExamResult($user_exam_id, $title_header);

        
        $row = $db->fetchRow("select "
                . "DATE_FORMAT(user_exam.exam_date,'%Y_%m_%d') AS date,"
                . "user.id "
                . "from user_exam "
                . "JOIN user ON user.id=user_exam.user_id "
                . "WHERE user_exam.id=$user_exam_id");

        Core_Common_Pdf::createFilePdf(Core_Common_Pdf::DOWNLOAD, $html, $row['id'] . '___' . $row['date'] . '.pdf', $title_header);
    }

}
