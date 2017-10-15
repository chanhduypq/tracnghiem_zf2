<?php

class Admin_NganhngheController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $mapper = new \Application\Model\Nganhnghe();
        $rows = $mapper->getNganhNghes();
        $this->view->items = $rows;
        
        
    }

    public function addAction() 
    {
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

                    Core::message()->addSuccess('Thêm mới thành công');
                    $this->_helper->redirector('index', 'nganhnghe', 'admin');
                } else {
                    $this->view->message = 'Lỗi. Xử lý thất bại.';
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData);
            }
        }

        $this->view->form = $form;
        $this->view->question_ids=$question_ids;
    }

    public function editAction() 
    {

        $id = $this->_getParam('id');

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

                Core::message()->addSuccess('Sửa thành công');
                $this->_helper->redirector('index', 'nganhnghe', 'admin');
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
