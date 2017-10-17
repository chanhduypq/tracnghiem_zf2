<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class NganhngheController extends AbstractActionController 
{



    public function indexAction() 
    {
        $mapper = new \Application\Model\Nganhnghe();
        $rows = $mapper->getNganhNghes();
        return new ViewModel(array('items'=>$rows));
        
    }

    public function addAction() 
    {
        $params=array();
        $form = new Admin_Form_Nganhnghe();

        $question_ids = array();
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if (isset($formData['question_id'])) {
                $question_ids = $formData['question_id'];
            } 
            if ($form->isValid($formData)) {
                $mapper = new \Application\Model\Nganhnghe();

                if ($id = $mapper->createRow($formData)->save()) {

                    if (is_array($question_ids) && count($question_ids) > 0) {
                        foreach ($question_ids as $question_id) {

                            $mapper_nganhnghe_question = new \Application\Model\NganhngheQuestion();
                            $mapper_nganhnghe_question->insert(array(
                                'nganhnghe_id' => $id,
                                'question_id' => $question_id,
                            ));
                        }
                    }

                    $session = new \Zend\Session\Container('base');$session->offsetSet('message', 'Thêm mới thành công');
                    
                    return $this->redirect()->toUrl('/admin/nganhnghe'); 
                } else {
                    $params['message'] = 'Lỗi. Xử lý thất bại.';
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData);
            }
        }

        $params['form'] = $form;
        $params['question_ids']=$question_ids;
        return new ViewModel($params);
    }

    public function editAction() 
    {

        $id = $this->params()->fromQuery('id');

        $where = "id=$id";
        $mapper = new \Application\Model\Nganhnghe();
        $row = $mapper->fetchRow($where)->toArray();
        $form = new Admin_Form_Nganhnghe();

        $question_ids = array();
        
        if ($this->_request->isPost()) {

            $formData = $this->_request->getPost();
            if (isset($formData['question_id'])) {
                $question_ids = $formData['question_id'];
            }

            if ($form->isValid($formData)) {

                $row = $mapper->fetchRow('id=' . $formData['id']);

                $mapper->update($formData, 'id=' . $formData['id']);

                Core_Db_Table::getDefaultAdapter()->query('delete from nganhnghe_question where nganhnghe_id=' . $formData['id'])->execute();
                if (is_array($question_ids) && count($question_ids) > 0) {
                    foreach ($question_ids as $question_id) {

                        $mapper_nganhnghe_question = new \Application\Model\NganhngheQuestion();
                        $mapper_nganhnghe_question->insert(array(
                            'nganhnghe_id' => $formData['id'],
                            'question_id' => $question_id,
                        ));
                    }
                }

                $session = new \Zend\Session\Container('base');$session->offsetSet('message', 'Sửa thành công');
                
                return $this->redirect()->toUrl('/admin/nganhnghe'); 
            } else {
                $form->populate($formData);
            }
        } else {
            $form->setDefaults($row);
            $temps = Core_Db_Table::getDefaultAdapter()->query("select question_id from nganhnghe_question  where nganhnghe_id='$id'")->fetchAll();
            if (is_array($temps) && count($temps) > 0) {
                foreach ($temps as $row1) {
                    $question_ids[] = $row1['question_id'];
                }
            }
        }
        $this->view->form = $form;
        $this->view->question_ids=$question_ids;
        $this->render('add');
    }

    public function deleteAction() 
    {
        $this->model = new \Application\Model\Nganhnghe();
    }

}
