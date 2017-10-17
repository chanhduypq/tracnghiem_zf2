<?php 
namespace Admin\Form;
use Zend\Form\Form;
class User extends Form 
{

    public function init() 
    {
        parent::init();
        $this->buildElementsAutoForFormByTableName('user');
        
        $this->remove("password");
        
        $this->remove("is_admin");
        
        $this->remove("danh_xung");
        $danh_xung=new \Zend\Form\Element\Select('danh_xung');
        $danh_xung->setValue('Anh');
        $danh_xung->setOptions(array('Anh'=>'Anh','Chị'=>'Chị'))->setLabel('Danh xưng:')->setValue('Anh')->setSeparator('')->setRequired();
        $this->add($danh_xung);
       
        $this->get('full_name')->setLabel('Họ và tên:');
        
        $this->get('phone')->setLabel('Phone:');
    }
    

}
