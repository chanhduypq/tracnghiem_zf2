<?php 
namespace Zend\Common;
use Zend\Form\Form as Zend_Form;
class Form
{
    /**
     * function common
     * fix các input đặc biệt để lưu vào $formData cho đúng như:
     *           date,file...
     * @author Trần Công Tuệ <chanhduypq@gmail.com>         
     * @param \Zend\Form\Form $form
     * @param array $formData
     * @param string $path_for_upload
     * @return void
     */
    public static function processSpecialInput(Zend_Form $form, &$formData) 
    {
        if (!($form instanceof Zend_Form) || count($form->getElements()) == 0) {
            return;
        }
        if (!is_array($formData) || count($formData) == 0) {
            return;
        }

        try {
            foreach ($form->getElements() as $element) {
                if ($element instanceof \Zend\Form\Element\Date) {
                    if ($element->getValue() != '') {
                        $array = explode("/", $element->getValue());
                        if (count($array) == 3) {
                            $date = $array[2] . '-' . $array[1] . '-' . $array[0];
                            $formData[$element->getName()] = $date;
                        }
                    }
                } elseif ($element instanceof \Zend\Form\Element\File) {
                    if ($element->getForInsertDB() == false) {
                        if ($_FILES[$element->getName()]['name'] != "") {
                            $file_name = $element->getRandomFileName();
                            if ($element->isUploaded($file_name) && $element->isValid($file_name)) {
                                $element->receive();
                            }
                            if (isset($file_name) && $file_name != "") {
                                $file_name = $element->getPathStoreFile() . $file_name;
                            }
                            $formData[$element->getName()] = $file_name;
                        }
                    } else if ($element->getForInsertDB() == true) {
                        $file_key_array = array('type', 'size', 'name');
                        foreach ($this->getElements() as $key => $value) {
                            if (in_array($key, $file_key_array)) {
                                $formData[$key] = $_FILES[$element->getName()][$key];
                            }
                        }
                        $file = fopen($_FILES[$element->getName()]['tmp_name'], "r", 1);
                        if ($file) {
                            $fileContent = base64_encode(file_get_contents($_FILES[$element->getName()]['tmp_name']));
                        } else {
                            $fileContent = null;
                        }
                        $formData[$element->getName()] = $fileContent;
                    }
                }
            }
        } catch (\Exception $e) {
            
        }
    }

   

    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * upload tất cả các file có trong form
     * return array chứa các fileName
     * @param string $path
     * @return array
     */
    public static function upload($path) 
    {
        if ($path == null || (!is_string($path)) || trim($path) == '') {
            return array();
        }
        $file_names = array();
        try {
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            $adapter->addValidator('Count', false, array('min' => 1))
            ;
            $adapter->setDestination($path);

            $files = $adapter->getFileInfo();
            if (count($files) > 0) {
                foreach ($files as $fieldname => $fileinfo) {
                    if ($adapter->isUploaded($fileinfo['name']) && $adapter->isValid($fileinfo['name'])) {
                        $adapter->receive($fileinfo['name']);                        
                        $file_names[] = Core_Common_File::fixFileName($fileinfo['name']);
                    }
                }
            }
        } catch (\Exception $ex) {
            return array();
        }
        return $file_names;
    }
}