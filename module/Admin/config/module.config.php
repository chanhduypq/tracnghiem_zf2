<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonAdmin for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'admin_index' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/admin/index[/:loginResult]',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_index_logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/index/logout',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'logout',
                    ),
                ),
            ),
            'admin_index_login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/index/login',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action'     => 'login',
                    ),
                ),
            ),
            'admin_exam' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/exam',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Exam',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_excel' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/excel',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Excel',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_guide' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/guide',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Guide',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_headerpdf' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/headerpdf',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Headerpdf',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_headerfooter' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/headerfooter',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Headerfooter',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_image' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/image',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Image',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_homecontent' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/homecontent',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Homecontent',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_menu' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/menu',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Menu',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_nganhnghe' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/nganhnghe',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Nganhnghe',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_question' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/question',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Question',
                        'action'     => 'index',
                    ),
                ),
            ),
            'admin_user' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/user',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'index',
                    ),
                ),                
            ),
            'admin_user_add' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/admin/user/add',
                    'defaults' => array(
                        'controller' => 'Admin\Controller\User',
                        'action'     => 'add',
                    ),
                ),                
            ),
            
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Admin\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index' => 'Admin\Controller\IndexController',
            'Admin\Controller\Exam' => 'Admin\Controller\ExamController',
            'Admin\Controller\Excel' => 'Admin\Controller\ExcelController',
            'Admin\Controller\Guide' => 'Admin\Controller\GuideController',
            'Admin\Controller\Headerfooter' => 'Admin\Controller\HeaderfooterController',
            'Admin\Controller\Headerpdf' => 'Admin\Controller\HeaderpdfController',
            'Admin\Controller\Homecontent' => 'Admin\Controller\HomecontentController',
            'Admin\Controller\Image' => 'Admin\Controller\ImageController',
            'Admin\Controller\Menu' => 'Admin\Controller\MenuController',
            'Admin\Controller\Nganhnghe' => 'Admin\Controller\NganhngheController',
            'Admin\Controller\Question' => 'Admin\Controller\QuestionController',
            'Admin\Controller\User' => 'Admin\Controller\UserController',
            
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'admin/index/index' => __DIR__ . '/../view/admin/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
