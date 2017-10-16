<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class ExamController extends AbstractActionController 
{


    public function indexAction() 
    {
        
        $data = $this->params()->fromPost();
        $error_config_exam = '';
        $message= $this->getMessage();
        
        if (count($data) > 0) {
            if (ctype_digit($data['phut']) && ctype_digit($data['number'])) {
                $this->saveDB($data);
                $session = new \Zend\Session\Container('base');$session->offsetSet('message', 'Lưu thành công');
                return $this->redirect()->toUrl('/admin/exam'); 
            }
            else{
                
                
        
                
                $model = new \Application\Model\Table('config_exam');
                $row_config_exam = $model->first();
                $select = new \Zend\Db\Sql\Select();
                $select->columns(array(
                            "sh" => "sh",
                            "sm" => "sm",
                            "eh" => "eh",
                            "em" => "em",
                            "date" => new \Zend\Db\Sql\Expression("DATE_FORMAT(date,'%d/%m/%Y')")
                        ))
                        ->from("exam_time")                        
                        ;
                $row_exam_time = $model->selectWith($select)->toArray();
                $row_exam_time=$row_exam_time[0];
                
                $row_config_exam['phut'] = $data['phut'];
                $row_config_exam['number'] = $data['number'];
                $row_exam_time['sh'] = $data['sh'];
                $row_exam_time['sm'] = $data['sm'];
                
                $temp = explode('-', $data['date']);
                if (is_array($temp) && count($temp) == 3) {
                    $dateForRender = $temp[2] . '/' . $temp[1] . '/' . $temp[0];
                } else {
                    $dateForRender = $data['date'];
                }
                
                $error_config_exam = 'Vui lòng nhập [Thời gian để hoàn thành một câu hỏi(phút)] và [Tổng số câu hỏi cho một lần thi] bằng số nguyên.';
            }            
            
            
        } else {
            
            $model = new \Application\Model\Table('config_exam');
            $row_config_exam = $model->first();
            
            $select = new \Zend\Db\Sql\Select();
            $select->columns(array(
                        "sh" => "sh",
                        "sm" => "sm",
                        "eh" => "eh",
                        "em" => "em",
                        "date" => new \Zend\Db\Sql\Expression("DATE_FORMAT(date,'%d/%m/%Y')")
                    ))
                    ->from("exam_time")                        
                    ;
           
            $row_exam_time = $model->selectWith($select)->toArray();
            $row_exam_time=$row_exam_time[0];
            
            $dateForRender = $row_exam_time['date'];
             
        }
        $model = new \Application\Model\Table('config_exam');
        $select = new \Zend\Db\Sql\Select();
        $select->columns(array(
                    "exam_date" => new \Zend\Db\Sql\Expression("DATE_FORMAT(exam_date,'%Y-%m-%d')")
                ))
                ->from("user_exam")
                ->order("exam_date DESC")
                ->limit(1)
                ;
        
        $row = $model->selectWith($select)->toArray();
        $row=$row[0];
        $params=array();
        if (is_array($row) && count($row) > 0) {            
            $exam_date = new \DateTime($row['exam_date']);
            $exam_date->add(new \DateInterval('P1D'));
            $params['minDate']=$exam_date->format('d/m/Y');
        }

        $params['row_exam_time'] = $row_exam_time;
        $params['row_config_exam'] = $row_config_exam;
        $params['message'] = $message;
        $params['error_config_exam'] = $error_config_exam;
        $params['date'] = $dateForRender;
        $levelModel = new \Application\Model\Configexamlevel();
        $params['levels'] = $levelModel->getConfigExamLevels();
        return new ViewModel($params);
    }

    private function saveDB($data) 
    {
        if (count($data) > 0) {
            $temp = explode('/', $data['date']);
            $data['date'] = $temp[2] . '-' . $temp[1] . '-' . $temp[0];;  
            $newtimestamp = strtotime($data['date'] . ' ' . $data['sh'] . ':' . $data['sm'] . ':00 + ' . ($data['phut'] * $data['number']) . ' minute');
            $end_time = date('Y-m-d H:i:s', $newtimestamp);
            $temp = explode(' ', $end_time);
            $temp = explode(':', $temp[1]);
            $eh = $temp[0];
            $em = $temp[1];

            $adapter = new \Zend\Db\Adapter\Adapter(array(
                'driver' => 'Mysqli',
                'database' => 'tracnghiem',
                'username' => 'root',
                'password' => ''
            ));
            $adapter->createStatement("update exam_time set `date`='" . $data['date'] . "',sh=" . $data['sh'] . ",sm=" . $data['sm'] . ",eh=$eh,em=$em")->execute();
            $adapter->createStatement("update config_exam set phan_tram=" . $data['phan_tram'] . ",phut=" . $data['phut'] . ",number=" . $data['number'])->execute();
            $adapter->createStatement("update user_exam set allow_re_exam=0")->execute();

         
            
            $levelIds = $data['level_id'];
            $b1 = $data['b1'];
            $b2 = $data['b2'];
            $b3 = $data['b3'];
            $model = new \Application\Model\Configexamlevel();
            for ($i = 0; $i < count($levelIds); $i++) {
                $temp=100-$b3[$i];
                if($b2[$i]>$temp){
                    $b2[$i]=$temp;
                }
                $b1[$i]=$temp-$b2[$i];
                $dataJson = json_encode(array(
                    'b1' => $b1[$i],
                    'b2' => $b2[$i],
                    'b3' => $b3[$i],
                ));
                $model->update(array('data' => $dataJson), 'id=' . $levelIds[$i]);
            }
        }
    }

}
