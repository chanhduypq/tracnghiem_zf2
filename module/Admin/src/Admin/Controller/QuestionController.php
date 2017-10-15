<?php

class Admin_QuestionController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }



    public function indexAction() 
    {       
        $mapper = new \Application\Model\Question();
        $rows = $mapper->getQuestions($this->total, $this->limit, $this->start);
        $this->view->items = $rows;       
    }

    public function addAction() 
    {
        $form = new Admin_Form_Question();

        $nganhnghe_ids = array();
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if (isset($formData['nganhnghe_id'])) {
                $nganhnghe_ids = $formData['nganhnghe_id'];
            } 
            if ($form->isValid($formData)) {
                $mapper = new \Application\Model\Question();


                if ($id = $mapper->createRow($formData)->save()) {

                    if (is_array($nganhnghe_ids) && count($nganhnghe_ids) > 0) {
                        foreach ($nganhnghe_ids as $nganhnghe_id) {

                            $mapper_nganhnghe_question = new \Application\Model\NganhngheQuestion();
                            $mapper_nganhnghe_question->insert(array(
                                'nganhnghe_id' => $nganhnghe_id,
                                'question_id' => $id,
                            ));
                        }
                    }


                    Core::message()->addSuccess('Thêm mới thành công');
                    $this->_helper->redirector('index', 'question', 'admin',array('page'=> $this->_getParam('page')));
                } else {
                    $this->view->message = 'Lỗi. Xử lý thất bại.';
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData);
            }
        }

        $this->view->form = $form;
        $this->view->page= $this->_getParam('page');
        $this->view->nganhnghe_ids=$nganhnghe_ids;
        $this->render('add-question');
    }

    public function addanswerAction() 
    {
        $question_id = $this->_getParam("question_id", "");

        $form = new Admin_Form_Answer();
        $form->getElement('question_id')->setValue($question_id);

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                $mapper = new \Application\Model\Answer();

                $formData['sign'] = strtoupper($formData['sign']);

                if ($mapper->createRow($formData)->save()) {
                    Core::message()->addSuccess('Thêm mới thành công');
                    $this->_helper->redirector('index', 'question', 'admin',array('page'=> $this->_getParam('page')));
                } else {
                    $this->view->message = 'Lỗi. Xử lý thất bại.';
                    $form->populate($formData);
                }
            } else {
                $form->populate($formData);
            }
        }
        $this->view->form = $form;
        $this->view->page= $this->_getParam('page');
        $this->render('add-answer');
    }

    public function editAction() 
    {

        $id = $this->_getParam('id');
        $where = "id=$id";
        $mapper = new \Application\Model\Question();
        $row = $mapper->fetchRow($where)->toArray();
        $form = new Admin_Form_Question();
        
        $nganhnghe_ids = array();
        if ($this->_request->isPost()) {

            $formData = $this->_request->getPost();
            if (isset($formData['dap_an'])) {
                $dap_ans = explode('_', $formData['dap_an']);
                $dap_an = $dap_ans[0];
                $dap_an_sign = $dap_ans[1];
            } else {
                $dap_an = NULL;
            }

            if (isset($formData['nganhnghe_id'])) {
                $nganhnghe_ids = $formData['nganhnghe_id'];
            } 

            if ($form->isValid($formData)) {
                $row = $mapper->fetchRow('id=' . $formData['id']);


                $mapper->update($formData, 'id=' . $formData['id']);
                if ($dap_an != NULL) {
                    Core_Db_Table::getDefaultAdapter()->query('delete from dap_an where question_id=' . $formData['id'])->execute();
                    $mapper_dapan = new \Application\Model\Dapan();
                    $mapper_dapan->insert(array(
                        'answer_id' => $dap_an,
                        'question_id' => $formData['id'],
                        'sign' => $dap_an_sign
                    ));
                }

                Core_Db_Table::getDefaultAdapter()->query('delete from nganhnghe_question where question_id=' . $formData['id'])->execute();
                if (is_array($nganhnghe_ids) && count($nganhnghe_ids) > 0) {
                    $mapper_nganhnghe_question = new \Application\Model\NganhngheQuestion();
                    foreach ($nganhnghe_ids as $nganhnghe_id) {

                        $mapper_nganhnghe_question->insert(array(
                            'nganhnghe_id' => $nganhnghe_id,
                            'question_id' => $formData['id'],
                        ));
                    }
                }

                Core::message()->addSuccess('Sửa thành công');
                $this->_helper->redirector('index', 'question', 'admin',array('page'=> $this->_getParam('page')));
            } else {
                $form->populate($formData);
                if ($dap_an != NULL) {
                    $this->view->dap_an = $dap_an;
                }
            }
        } else {
            $form->setDefaults($row);
            $rows = Core_Db_Table::getDefaultAdapter()->query("select * from nganhnghe_question where question_id='$id'")->fetchAll();
            
            if (is_array($rows) && count($rows) > 0) {
                foreach ($rows as $row1) {
                    $nganhnghe_ids[] = $row1['nganhnghe_id'];
                }
            }
        }
        $this->view->form = $form;
        $this->view->nganhnghe_ids=$nganhnghe_ids;
        $this->view->page = $this->_getParam('page');
        $this->render('add-question');
    }

    public function editanswerAction() 
    {

        $id = $this->_getParam('id');
        $where = "id=$id";
        $mapper = new \Application\Model\Answer();
        $row = $mapper->fetchRow($where)->toArray();
        $form = new Admin_Form_Answer();
        if ($this->_request->isPost()) {

            $formData = $this->_request->getPost();

            if ($form->isValid($formData)) {

                $row = $mapper->fetchRow('id=' . $formData['id']);

                $formData['sign'] = strtoupper($formData['sign']);
                $mapper->update($formData, 'id=' . $formData['id']);
                Core::message()->addSuccess('Sửa thành công');
                $this->_helper->redirector('index', 'question', 'admin',array('page'=> $this->_getParam('page')));
            } else {
                $form->populate($formData);
            }
        } else {
            $form->setDefaults($row);
        }
        $this->view->form = $form;
        $this->view->page = $this->_getParam('page');
        $this->render('add-answer');
    }

    public function deleteAction() 
    {
        $answer_id = $this->_request->getParam('answer_id', null);
        $question_id = $this->_request->getParam('question_id', null);

        
        if (\Zend\Common\Numeric::isInteger($answer_id) == FALSE && \Zend\Common\Numeric::isInteger($question_id) == FALSE) {
            $this->_helper->redirector('index', 'question', 'admin');
            return;
        }

        if (\Zend\Common\Numeric::isInteger($question_id)) {
            $where = "id=$question_id";
            $mapper = new \Application\Model\Question();

            $mapper->delete($where);

            $mapper = new \Application\Model\Answer();
            $where = "question_id=$question_id";
            $mapper->delete($where);

            $mapper = new \Application\Model\NganhngheQuestion();
            $mapper->delete("question_id=$question_id");
        } else if (\Zend\Common\Numeric::isInteger($answer_id)) {
            $mapper = new \Application\Model\Answer();

            $where = "id=$answer_id";
            $row = $mapper->fetchRow($where);
            $mapper->delete($where);
            $answers = $mapper->fetchAll('question_id=' . $row['question_id']);
        }

        Core::message()->addSuccess('Xóa thành công');
        $this->_helper->redirector('index', 'question', 'admin');
    }

}
