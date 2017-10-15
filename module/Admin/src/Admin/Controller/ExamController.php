<?php

class Admin_ExamController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
    }

    public function indexAction() 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $data = $this->_request->getPost();
        $error_config_exam = '';
        $message= $this->getMessage();
        if (count($data) > 0) {
            if (ctype_digit($data['phut']) && ctype_digit($data['number'])) {
                $this->saveDB($data);
                Core::message()->addSuccess('Lưu thành công');
                $this->_helper->redirector('index', 'exam', 'admin');                                
            }
            else{
                $row_config_exam = $db->fetchRow('select * from config_exam');
                $row_exam_time = $db->fetchRow("select sh,sm,eh,em,DATE_FORMAT(date,'%d/%m/%Y') AS date from exam_time");
                
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
            $row_config_exam = $db->fetchRow('select * from config_exam');
            $row_exam_time = $db->fetchRow("select sh,sm,eh,em,DATE_FORMAT(date,'%d/%m/%Y') AS date from exam_time");
            
            $dateForRender = $row_exam_time['date'];
        }
        
        $row = $db->fetchRow("select DATE_FORMAT(exam_date,'%Y-%m-%d') AS exam_date from user_exam ORDER BY exam_date DESC LIMIT 1");
        if (is_array($row) && count($row) > 0) {            
            $exam_date = new \DateTime($row['exam_date']);
            $exam_date->add(new DateInterval('P1D'));
            $this->view->minDate=$exam_date->format('d/m/Y');
        }

        $this->view->row_exam_time = $row_exam_time;
        $this->view->row_config_exam = $row_config_exam;
        $this->view->message = $message;
        $this->view->error_config_exam = $error_config_exam;
        $this->view->date = $dateForRender;
        $levelModel = new \Application\Model\Configexamlevel();
        $this->view->levels = $levelModel->getConfigExamLevels();
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

            $db = Core_Db_Table::getDefaultAdapter();

            $db->query("update exam_time set `date`='" . $data['date'] . "',sh=" . $data['sh'] . ",sm=" . $data['sm'] . ",eh=$eh,em=$em")->execute();

            $db->query("update config_exam set phan_tram=" . $data['phan_tram'] . ",phut=" . $data['phut'] . ",number=" . $data['number'])->execute();

            $db->query("update user_exam set allow_re_exam=0")->execute();
            
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
