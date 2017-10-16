<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class GuideController extends AbstractActionController 
{



    public function indexAction() 
    {
        $file_name='';
        if(file_exists(UPLOAD . "/public/guide/")){
            $files = scandir(UPLOAD . "/public/guide/", 0);
            foreach ($files as $file){
                if ($file != '.' || $file != '..') {
                    $file_name=$file;
                }
            }   
        }
            

        $this->view->item = $file_name;
        
    }

    public function downloadAction() 
    {
        Core_Common_Download::download(UPLOAD . "/public/guide/");
    }

    public function saveAction() 
    {
        if (isset($_FILES['hinhnen']) && isset($_FILES['hinhnen']['name']) && $_FILES['hinhnen']['name'] != '') {
            $files = scandir(UPLOAD . "/public/guide/", 0);
            foreach ($files as $file){
                if ($file != '.' || $file != '..') {
                    @unlink(UPLOAD . "/public/guide/".$file);
                }
            }      

            
            $item_image = $_FILES['hinhnen']['name'];
            if (isset($item_image) && $item_image != "") {
                $extension = @explode(".", $item_image);
                $extension = $extension[count($extension) - 1];
//                $item_image = sprintf('_%s.' . $extension, uniqid(md5(time()), true));
                $item_image='huong_dan_su_dung.'.$extension;
                $path = UPLOAD . "/public/guide/" . $item_image;
                move_uploaded_file($_FILES['hinhnen']['tmp_name'], $path);
            }
            
            $session = new \Zend\Session\Container('base');$session->offsetSet('message', 'Lưu thành công');
        }

        
        return $this->redirect()->toUrl('/admin/guide'); 
    }

}
