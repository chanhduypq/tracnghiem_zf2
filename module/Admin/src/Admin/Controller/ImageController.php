<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class ImageController extends AbstractActionController 
{



    public function indexAction()
    {
        $itemLogo = new \Admin\Model\LogoMapper();
        $this->view->logo = $itemLogo->getInfo();

        $itemHinhnen = new \Admin\Model\HinhnenMapper();
        $this->view->hinhnen = $itemHinhnen->getInfo();
        
        $item=new \Admin\Model\HinhnentrangchuMapper();
        $this->view->bg = $item->getInfo();
        
        
    }

    public function saveAction() 
    {



        if (isset($_FILES['logo']) && isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
            $item = new \Admin\Model\LogoMapper();
            $item_image = $_FILES['logo']['name'];
            
            $extension = @explode(".", $item_image);
            $extension = $extension[count($extension) - 1];
            $item_image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
            $path = UPLOAD . "/public/images/database/logo/" . $item_image;
            $item_image = "/images/database/logo/" . $item_image;
            move_uploaded_file($_FILES['logo']['tmp_name'], $path);

            $resultLogo = $item->save($item_image, $this->getRequest()->getPost("dynamic"));
            if ($resultLogo['file_name'] != $item_image && trim($_FILES['logo']['name']) != "") {
                $path = UPLOAD . "/public" . $resultLogo['file_name'];
                unlink($path);
            }
        }




        if (isset($_FILES['hinhnen']) && isset($_FILES['hinhnen']['name']) && $_FILES['hinhnen']['name'] != '') {
            $item = new \Admin\Model\HinhnenMapper();
            $item_image = $_FILES['hinhnen']['name'];

            if (isset($item_image) && $item_image != "") {
                
                $extension = @explode(".", $item_image);
                $extension = $extension[count($extension) - 1];
                $item_image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
                $path = UPLOAD . "/public/images/database/hinhnen/" . $item_image;
                $item_image = "/images/database/hinhnen/" . $item_image;
                move_uploaded_file($_FILES['hinhnen']['tmp_name'], $path);
            }


            $result = $item->save($item_image);
            if ($result['file_name'] != $item_image && trim($_FILES['hinhnen']['name']) != "") {
                $path = UPLOAD . "/public" . $result['file_name'];
                unlink($path);
            }
        }
        
        if (isset($_FILES['bg']) && isset($_FILES['bg']['name']) && $_FILES['bg']['name'] != '') {
            $item = new \Admin\Model\HinhnentrangchuMapper();
            $item_image = $_FILES['bg']['name'];
            

            if (isset($item_image) && $item_image != "") {
                
                $extension = @explode(".", $item_image);
                $extension = $extension[count($extension) - 1];
                $item_image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
                $path = UPLOAD . "/public/images/database/bg/" . $item_image;
                $item_image = "/images/database/bg/" . $item_image;
                move_uploaded_file($_FILES['bg']['tmp_name'], $path);
            }


            $result = $item->save($item_image);
            if ($result['file_name'] != $item_image && trim($_FILES['bg']['name']) != "") {
                $path = UPLOAD . "/public" . $result['file_name'];
                @unlink($path);
            }
        }

        Core::message()->addSuccess('Lưu thành công');
        $this->_helper->redirector('index', 'image', 'admin');
    }

}
