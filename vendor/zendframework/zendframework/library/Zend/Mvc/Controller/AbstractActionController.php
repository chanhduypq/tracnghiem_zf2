<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller;

use Zend\Http\Response as HttpResponse;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ConsoleModel;
use Zend\View\Model\ViewModel;

/**
 * Basic action controller
 */
abstract class AbstractActionController extends AbstractController
{
    /**
     * @var string
     */
    protected $eventIdentifier = __CLASS__;
    
    /**
     * 
     * @var string
     */
    public $language;

    /**
     * 
     * @var integer
     */
    public $page;

    /**
     *
     * @var integer
     */
    public $limit;

    /**
     *
     * @var integer
     */
    public $start;

    /**
     *
     * @var integer
     */
    public $order;

    /**
     *
     * @var integer
     */
    public $total;

    /**
     *
     * @var Core_Form
     */
    public $form = null;

    /**
     *
     * @var Core_Db_Table_Abstract
     */
    public $model = null;

    /**
     *
     * @var array
     */
    public $formData = null;

    /**
     *
     * @var string
     */
    public $renderScript = NULL;
    
    private function setLayout() {
        if (strpos($this->params('controller'), 'Application') !== FALSE) {
            $this->layout('layout/index');           
        }
    }
    
    private function redirectIfNotLogin() {
        if (strpos($this->params('controller'), 'Admin') !== FALSE) {
            if (strpos($this->params('controller'), 'Index') === FALSE) {
                $session = new \Zend\Session\Container('base');
                if (!$session->offsetExists('user')) {
                    $this->turnSessionPrevController();
                    return $this->redirect()->toUrl('/admin/index'); 
                } else {
                    $identity = $session->offsetGet('user');
                    if (!isset($identity['user']) || $identity['user'] != 'admin') {
                        $this->turnSessionPrevController();
                        return $this->redirect()->toUrl('/admin/index'); 
                    }
                }
            }
        } else {
            if (strpos($this->params('controller'), 'Index') === FALSE && strpos($this->params('controller'), 'question') === FALSE) {
                $session = new \Zend\Session\Container('base');
                if (!$session->offsetExists('user')) {
                    return $this->redirect()->toUrl('/application_index'); 
                }
            }
        }
    }
    
    public function initPaginator() {
        $this->page = $this->params()->fromQuery('page', 1);
        $this->limit = $this->params()->fromQuery('limit', 10);
        $this->start = $this->params()->fromQuery('start', 0);
        $this->order = $this->params()->fromQuery('filter_order', 'id') . ' ' . $this->params()->fromQuery('filter_order_Dir', 'DESC');
    }
    
    private function turnSessionPrevController() {        
        
        $session = new \Zend\Session\Container('base');
        $controller=$this->params('controller');
        $temp= explode('\\', $controller);
        $controller= strtolower($temp[count($temp)-1]);
        $session->offsetSet('controller', $controller);
        
    }
    
    public function download($path, $fileName = null) {
//        tuetc
//        Core_Common_Download::download($path, $fileName);
    }

    public function createFilePdf($html, $filename, $title_header = '') {
//        tuetc
//        Core_Common_Pdf::createFilePdf($html, $filename, $title_header);
    }
    
    public function init() {
        parent::init();
        

        set_time_limit(2000);

//        tuetc
//        $this->view->headMeta()->appendName('author', 'Trần Công Tuệ email:chanhduypq@gmail.com');
//        $this->view->headMeta()->appendName('copyright', 'Công ty TNHH VietAgar  website: http://vietagar.com.vn');
//        $this->view->headMeta()->appendName('description', 'Chúng tôi không ngừng nổ lực phát triển website');
//        $this->view->headMeta()->appendName('keywords', 'Trần Công Tuệ, chanhduypq@gmail.com');

        $this->initPaginator();

        if ($this->params('action') == 'index') {
            $this->limit = $this->params()->fromQuery('limit', 5);
            $this->page = $this->params()->fromQuery('page', 1);
            if (\Zend\Common\Numeric::isInteger($this->page) == FALSE) {
                $this->page = 1;
            }

            $this->start = (($this->page - 1) * $this->limit);
        } else if ($this->params('action') == 'add' || $this->params('action') == 'edit') {
            $this->formData = $this->params()->fromPost();
        }
    }
    
    public function dispatch(\Zend\Stdlib\RequestInterface $request, \Zend\Stdlib\ResponseInterface $response = null) {
        parent::dispatch($request, $response);
        if ($this->params('action') == 'index') {
            $this->processForIndexAction();
        } else if ($this->params('action') == 'add') {
            $this->processForAddAction();
        } else if ($this->params('action') == 'edit') {
            $this->processForEditAction();
        } else if ($this->params('action') == 'delete') {
            $this->processForDeleteAction();
        }
    }
    
    private function processForIndexAction() {
//        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($this->total));
//
//        $paginator->setDefaultScrollingStyle();
//        $paginator->setItemCountPerPage($this->limit);
//        $paginator->setCurrentPageNumber($this->page);
//
//        $this->view->paginator = $paginator;
//        $this->view->limit = $this->limit;
//        $this->view->total = $this->total;
//        $this->view->page = $this->page;
//        if (!isset($this->view->message)) {
//            $this->view->message = $this->getMessage();
//        }
    }

    private function processForAddAction() {
//        if ($this->model == NULL || $this->form == NULL) {
//            return;
//        }
//        if ($this->params()->fromPost()) {
//            if ($this->form->isValid($this->formData)) {
//                Core_Common_Form::processSpecialInput($this->form, $this->formData);
//                if ($this->model->createRow($this->formData)->save()) {
//                    Core::message()->addSuccess('Thêm mới thành công');
//                    $this->_helper->redirector('index', $this->_request->getControllerName(), $this->_request->getModuleName(), array('page' => $this->_getParam('page')));
//                } else {
//                    $this->view->message = 'Lỗi. Xử lý thất bại.';
//                    $this->form->populate($this->formData);
//                }
//            } else {
//                $this->form->populate($this->formData);
//            }
//        }
//        if (!isset($this->view->form)) {//nếu trong addAction, chưa có dòng code này: $this->view->form = $this->form;
//            $this->view->form = $this->form;
//        }
//        if ($this->renderScript == NULL) {//nếu trong addAction, k chỉ định renderScript đến .phtml nào
//            try {
//                $this->render('add');
//            } catch (Exception $e) {
//                if ($e->getCode() == 0) {
//                    $this->renderScript('common/add.phtml');
//                }
//            }
//        } else {
//            $this->renderScript($this->renderScript);
//        }
    }

    private function processForEditAction() {
//        tuetc
//        if ($this->model == NULL || $this->form == NULL) {
//            return;
//        }
//        if ($this->params()->fromPost()) {            
//            if ($this->form->isValid($this->formData)) {
//                Core_Common_Form::processSpecialInput($this->form, $this->formData);
//                $this->model->update($this->formData, 'id=' . $this->formData['id']);
//                Core::message()->addSuccess('Sửa thành công');
//                $this->_helper->redirector('index', $this->_request->getControllerName(), $this->_request->getModuleName(), array('page' => $this->_getParam('page')));
//            } else {
//                $this->form->populate($this->formData);
//            }
//        } else {
//            $row = $this->model->fetchRow("id=" . $this->_getParam('id'))->toArray();
//            $this->form->setDefaults($row);
//        }
//        if (!isset($this->view->form)) {//nếu trong editAction, chưa có dòng code này: $this->view->form = $this->form;
//            $this->view->form = $this->form;
//        }
//        if ($this->renderScript == NULL) {//nếu trong editAction, k chỉ định renderScript đến .phtml nào
//            try {
//                $this->render('add');
//            } catch (Exception $e) {
//                if ($e->getCode() == 0) {
//                    $this->renderScript('common/add.phtml');
//                }
//            }
//        } else {
//            $this->renderScript($this->renderScript);
//        }
    }

    private function processForDeleteAction() {
//        if ($this->model == NULL) {
//            return;
//        }
//        $id = $this->_getParam('id');
//        if (Core_Common_Numeric::isInteger($id) == FALSE) {
//            $this->_helper->redirector('index', $this->_request->getControllerName(), $this->_request->getModuleName());
//            return;
//        }
//        $this->model->delete("id=$id");
//        Core::message()->addSuccess('Xóa thành công');
//        $this->_helper->redirector('index', $this->_request->getControllerName(), $this->_request->getModuleName());
    }

    /**
     * Default action if none provided
     *
     * @return array
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'content' => 'Placeholder page'
        ));
    }

    /**
     * Action called if matched action does not exist
     *
     * @return array
     */
    public function notFoundAction()
    {
        $response   = $this->response;
        $event      = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        $routeMatch->setParam('action', 'not-found');

        if ($response instanceof HttpResponse) {
            return $this->createHttpNotFoundModel($response);
        }
        return $this->createConsoleNotFoundModel($response);
    }

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    public function onDispatch(MvcEvent $e)
    {
        
        $this->setLayout();
        $this->redirectIfNotLogin();
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
        }

        $action = $routeMatch->getParam('action', 'not-found');
        $method = static::getMethodFromAction($action);
        if (!method_exists($this, $method)) {
            $method = 'notFoundAction';
        }

        $actionResponse = $this->$method();

        $e->setResult($actionResponse);

        return $actionResponse;
    }

    /**
     * Create an HTTP view model representing a "not found" page
     *
     * @param  HttpResponse $response
     * @return ViewModel
     */
    protected function createHttpNotFoundModel(HttpResponse $response)
    {
        $response->setStatusCode(404);
        return new ViewModel(array(
            'content' => 'Page not found',
        ));
    }

    /**
     * Create a console view model representing a "not found" action
     *
     * @param  \Zend\Stdlib\ResponseInterface $response
     * @return ConsoleModel
     */
    protected function createConsoleNotFoundModel($response)
    {
        $viewModel = new ConsoleModel();
        $viewModel->setErrorLevel(1);
        $viewModel->setResult('Page not found');
        return $viewModel;
    }
    
    public function getUserId() {
        $session = new \Zend\Session\Container('base');
        if (!$session->offsetExists('user')) {
            return -1;
        } else {
            $identity = $session->offsetGet('user');
            return $identity['id'];
        }
    }
    
    /**
     * khởi tạo lại session ban đầu
     * có nghĩa là 
     *     ban đầu khi login, lưu thông tin session nào thi bây giờ chỉ lấy lại những thông tin đó, 
     *     những thông tin session mới thêm vào sau này thi hủy đi
     */
    public function resetSession() {
        $session = new \Zend\Session\Container('base');
        $identity = $session->offsetGet('user');

        foreach ($identity as $key => $value) {
            if (!in_array($key, array(
                        'id',
                        'danh_xung',
                        'full_name',
                        'email',
                        'phone',
                        'password',
                        'is_admin',
                        'user'
                            )
                    )
            ) {
                unset($identity["$key"]);
            }
        }

        $session->offsetSet('user', $identity);
    }

    public function getMessage() {
        $session = new \Zend\Session\Container('base');
        if ($session->offsetExists('message')) {            
            return $session->offsetGet('message');
        } else {
            return '';
        }
    }
}
