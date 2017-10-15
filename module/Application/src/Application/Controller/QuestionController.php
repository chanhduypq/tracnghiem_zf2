<?php

class QuestionController extends Core_Controller_Action {

    public function init() {
        parent::init();
        $this->view->headTitle('Ngân hàng câu hỏi', true);
    }

    

    public function indexAction() 
    {

        $nganhNgheId = $this->_getParam('nganhNgheId');
        $level = $this->_getParam('level');

        if (\Zend\Common\Numeric::isInteger($level) === FALSE) {
            $level = 1;
        }
        if (\Zend\Common\Numeric::isInteger($nganhNgheId) === FALSE) {
            $nganhNgheId = 0;
        }

        $this->view->questionArray = \Application\Model\Question::getQuestionsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level);
    }

    
}
