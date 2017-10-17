<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class QuestionController extends AbstractActionController {

    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {        
//        $this->view->headTitle('Ngân hàng câu hỏi', true);
        parent::onDispatch($e);
    }

    

    public function indexAction() 
    {

        $nganhNgheId = $this->params()->fromQuery('nganhNgheId');
        $level = $this->params()->fromQuery('level');

        if (\Zend\Common\Numeric::isInteger($level) === FALSE) {
            $level = 1;
        }
        if (\Zend\Common\Numeric::isInteger($nganhNgheId) === FALSE) {
            $nganhNgheId = 0;
        }

        return new ViewModel(array('questionArray' => \Application\Model\Question::getQuestionsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level)));
    }

    
}
