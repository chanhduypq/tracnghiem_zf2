<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class UserController extends AbstractActionController {

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {        
        $this->model = new \Application\Model\User();
        $this->form = new \Admin\Form\User();
        parent::onDispatch($e);
    }

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
     
        $rows = $this->model->getUsers($this->total, $this->limit, $this->start);
        $params['items'] = $rows;
        return new ViewModel($params);
    }

    public function addAction() {
        $params= array();
//        if (is_array($this->formData) && count($this->formData) > 0) {
//            $this->formData['password'] = sha1($this->formData['email']);
//        }
//        $this->view->page = $this->params()->fromQuery('page');
//
//        $this->renderScript = 'user/add.phtml';
        $params['form']= $this->form;
        return new ViewModel($params);
    }

    public function editAction() {
        $this->view->page = $this->params()->fromQuery('page');

        
        $id = $this->params()->fromQuery('id');
        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select
                ->from("user_exam",array(
                    "allow_re_exam" => "allow_re_exam",
                    "user_id" => "user_id",
                    "nganh_nghe_id" => "nganh_nghe_id",
                    "level" => "level",
                    "id" => "id",
                    "exam_date" => "exam_date"
                    ))
                ->join("nganh_nghe", "nganh_nghe.id=user_exam.nganh_nghe_id",array("title" => "title"))
                ->join("user_pass", "user_pass.user_exam_id=user_exam.id",array("user_exam_id" => "user_exam_id"),\Zend\Db\Sql\Select::JOIN_LEFT)
                ->where("user_exam.user_id=$id")
                ->order("user_exam.exam_date ASC")
                ;
        $history = $model->selectWith($select)->toArray();
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
        return $this->redirect()->toRoute('/admin/user/edit?id='.$user_id); 
    }

    public function cancelreexamAction() {
        $user_id = $this->params()->fromQuery('user_id', null);
        $exam_id = $this->params()->fromQuery('exam_id', null);
        
        $adapter = new \Zend\Db\Adapter\Adapter();
        $adapter->createStatement('UPDATE user_exam SET allow_re_exam=0 WHERE id=' . $exam_id)->execute();
        
        return $this->redirect()->toRoute('/admin/user/edit?id='.$user_id); 
    }

    public function ketquathiAction() {
        $user_exam_id = $this->params()->fromQuery('user_exam_id');
        $html = \Application\Model\Userexam::getHtmlForExamResult($user_exam_id, $title_header);

        
        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select
                ->from("user_exam",array(
                    "date" => new \Zend\Db\Sql\Expression("DATE_FORMAT(user_exam.exam_date,'%Y_%m_%d')")                    
                    ))
                ->join("user", "user.id=user_exam.user_id",array("id" => "id"))                
                ->where("user_exam.id=$user_exam_id")
                ;
        $row = $model->selectWith($select)->toArray();
        $row=$row[0];

        Core_Common_Pdf::createFilePdf(Core_Common_Pdf::DOWNLOAD, $html, $row['id'] . '___' . $row['date'] . '.pdf', $title_header);
    }

}
