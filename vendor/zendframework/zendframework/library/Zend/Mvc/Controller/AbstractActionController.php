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
