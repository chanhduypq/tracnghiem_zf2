<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class ThiController extends AbstractActionController 
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function init() {
//        parent::init();
//        $this->view->headTitle('Thi trắc nghiệm', true);
//    }
    
    public $param = array();

    public function viewresult() 
    {

        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user_exam')->where("user_id=".$this->getUserId())->order("exam_date DESC");
        $row = $model->selectWith($select)->toArray();
        if (!is_array($row) || count($row) == 0) {
            return $this->redirect()->toUrl('/application/thi');   
        }
        $html = UserExam::getHtmlForExamResult($row['id'], $title_header);

        $date = explode(' ', $row[0]['exam_date']);
        $date = explode('-', $date[0]);
        Pdf::createFilePdf(Pdf::DOWNLOAD, $html, $date[0] . '_' . $date[1] . '_' . $date[2] . '.pdf', $title_header);
    }

    public function index() 
    {

        $this->param['i']=intval(date('i'));
        $this->param['h']=intval(date('H'));
        
        $request= $this->params()->fromPost();
        $this->setParams($request);
        $this->setupExamingSession($request, $this->param['nganhNgheId'] ,$this->param['level'] ,$this->param['questionIds'] );
        if ($this->isReExam()) {
            $this->processReExam($request);
        } else {
            $this->processExam($request);
        }

        $this->param['success'] = $this->getMessage();

        return new ViewModel($this->param); 
    }
    
    private function saveDB($request) 
    {
        $date = date('Y-m-d');
        $h = $request['h'];
        $m = $request['i'];
        if($m==0){
            $m=59;
            $h--;
        }
        else{
            $m--;
        }
        
        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user_exam')->where("DATE(exam_date)='" . $date . "' AND user_id=" . $this->getUserId());
        $row = $model->selectWith($select)->toArray();
        if (is_array($row) && count($row) > 0) {
            return;
        }

//        DB::beginTransaction();
        try {
            
            $session = new Container('base');
            $identity = $session->offsetGet('user');
            $sh = $identity['sh'];
            $sm = $identity['sm'];
            
            $modelUserExam = new \Application\Model\Userexam();
            $userExamId = $modelUserExam->insert(
                    array(
                        'user_id' => $this->getUserId(),
                        'nganh_nghe_id' => $request['nganh_nghe_id_form2'],
                        'level' => $request['level_form2'],
                        'exam_date' => date("Y-m-d $h:$m:s"),
                        'sh' => $sh,
                        'sm' => $sm,
                        'eh' => $h,
                        'em' => $m,
                        'es'=> rand(1, 59),
                    )
            );
            $i = 0;
            $questionIds = $request['question_id'];
            $answerIds = $request['answer_id'];
            $answerSigns = $request['answer_sign'];
            $dapanSigns = $request['dapan_sign'];
            $answersJsons = $request['answers_json'];
            $count_correct = 0;
            
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }

                $modelUserExamdetail = new \Application\Model\Userexamdetail();
                $modelUserExamdetail->insert(array(
                    'user_exam_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i]=='Z'?' ':$answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                    'answers_json' => $answersJsons[$i],
                ));
            }

            $model = new \Application\Model\Table('');
            $select = new \Zend\Db\Sql\Select();
            $select->from('config_exam');
            $config_exam = $model->selectWith($select)->toArray();
            $config_exam=$config_exam[0];
            

            if ($count_correct >= $config_exam['phan_tram'] * count($questionIds)) {
                $modelUserpass = new \Application\Model\Userpass();
                $modelUserpass->insert(array(
                    'user_id' => $this->getUserId(),
                    'nganh_nghe_id' => $request['nganh_nghe_id_form2'],
                    'level' => $request['level_form2'],
                    'user_exam_id' => $userExamId,
                ));
            }
//            DB::commit();
        } catch (Exception $e) {
//            DB::rollback();
        }
    }

    private function saveDBAgain($request) 
    {
        
//        DB::beginTransaction();

        try {
            
            
            $model = new \Application\Model\Table('');
            $select = new \Zend\Db\Sql\Select();
            $select->from('user_exam')->where("user_id=".$this->getUserId())->order('exam_date DESC');
            $user_exam = $model->selectWith($select)->toArray();
            $user_exam=$user_exam[0];
            if (is_array($user_exam) && count($user_exam) > 0) {
                $userExamId = $user_exam['id'];
            } else {
                $userExamId = -1;
            }
            $i = 0;
            
            $questionIds = $request['question_id'];
            $answerIds = $request['answer_id'];
            $answerSigns = $request['answer_sign'];
            $dapanSigns = $request['dapan_sign'];
            $answersJsons = $request['answers_json'];
            $count_correct = 0;
            
            $modelUserExamdetail = new \Application\Model\Userexamdetail();
            $modelUserExamdetail->delete('user_exam_id=' . $userExamId);
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }

                $modelUserExamdetail = new \Application\Model\Userexamdetail();
                $modelUserExamdetail->insert(array(
                    'user_exam_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i]=='Z'?' ':$answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                    'answers_json' => $answersJsons[$i],
                ));
            }

            

            
            $model = new \Application\Model\Table('');
            $select = new \Zend\Db\Sql\Select();
            $select->from('config_exam');
            $config_exam = $model->selectWith($select)->toArray();
            $config_exam=$config_exam[0];

            if ($count_correct >= $config_exam['phan_tram'] * count($questionIds)) {
                $modelUserpass = new \Application\Model\Userpass();
                $modelUserpass->insert(array(
                    'user_id' => $this->getUserId(),
                    'nganh_nghe_id' => $request['nganh_nghe_id_form2'],
                    'level' => $request['level_form2'],
                    'user_exam_id' => $userExamId,
                ));
                $allow_re_exam = 0;
            } else {
                $allow_re_exam = 1;
            }
            $modelUserexam = new \Application\Model\Userexam();
            $modelUserexam->update(array('allow_re_exam' => $allow_re_exam, 'nganh_nghe_id' => $request['nganh_nghe_id_form2'], 'level' => $request['level_form2']), 'id=' . $userExamId);
            
//            DB::commit();
        } catch (Exception $e) {
//            DB::rollback();
        }
    }

    
    private function submitReExam($request) 
    {
        $this->saveDBAgain($request);
        $this->resetSession();
        $session = new Container('base');
        $session->offsetSet('success', 'Chúc mừng bạn đã hoàn thành kỳ thi lần này.');
        return $this->redirect()->toUrl('/application/thi'); 
        exit;
    }

    private function submitExam($request) 
    {
        $this->saveDB($request);
        $this->resetSession();
        $session = new Container('base');
        $session->offsetSet('success', 'Chúc mừng bạn đã hoàn thành kỳ thi lần này.');        
        return $this->redirect()->toUrl('/application/thi');   
        exit;
    }

    /**
     * bật session đang thi
     * @param int|string $nganhNgheId
     * @param int|string $level
     * @param array $questionIds
     * @param array $identity
     */
    private function turnOnExamingSession($nganhNgheId, $level, $questionIds, &$identity) 
    {
        $identity['examing'] = true;
        $identity['nganh_nghe_id'] = $nganhNgheId;
        $identity['level'] = $level;
        $identity['questionIds'] = $questionIds;
        $identity['sh'] = date('H');
        $identity['sm'] = date('i');
    }

    /**
     * kiểm tra thử user hiện tại có phải là đang được phép thi lại hay không
     * @return boolean
     */
    private function isReExam() 
    {
        
        
        
        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select->from('user_exam')->where("user_id=".$this->getUserId())->order('exam_date DESC');
        $user_exam = $model->selectWith($select)->toArray();
        if (is_array($user_exam) && count($user_exam) > 0 && $user_exam[0]['allow_re_exam'] == '1') {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 
     * @param array $data
     * @param int|string $nganhNgheId
     * @param int|string $level
     * @param array $questionIds
     * @param array $questions
     * @param array $nganhNghes
     * @param bool $showFormNganhNgheCapBac
     */
    private function setParams($request) 
    {
         
        $session = new Container('base');
        $identity = $session->offsetGet('user');
        
        if (isset($identity['examing']) && $identity['examing'] == true) {
            $nganhNgheId = $identity['nganh_nghe_id'];
            $level = $identity['level'];
            $questionIds = $identity['questionIds'];
        } else {
            $nganhNgheId = $request['nganh_nghe_id'] ? $request['nganh_nghe_id'] : 0;
            $level = $request['level'] ? $request['level'] : 0;            
            
            $model = new \Application\Model\Table('');
            $select = new \Zend\Db\Sql\Select();
            $select->from('config_exam');
            $config_exam = $model->selectWith($select)->toArray();
            $config_exam=$config_exam[0];
            $questionIds = Question::getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam['number']);
        }
        
        $date = date('Y-m-d');

        $h = $request['h'] ? $request['h'] : date('H');
        $m = $request['i'] ? $request['i'] : date('i');
        
        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select->columns(array(
            "date"=>new \Zend\Db\Sql\Expression('DATE(`date`)'),
            "sh"=>"sh",
            "sm"=>"sm",
            "eh"=>"eh",
            "em"=>"em"
        ))->from('exam_time')->where("DATE(`date`)='$date' AND ($h>sh OR ($h=sh AND $m>=sm)) AND ($h < eh OR ($h=eh AND $m<=em))");
        $exam_time = $model->selectWith($select)->toArray();
        if (is_array($exam_time) && count($exam_time) > 0) {
            $model = new \Application\Model\Table('');
            $select = new \Zend\Db\Sql\Select();
            $select->from('user_exam')->where("DATE(exam_date)='" . $exam_time[0]['date'] . "' AND user_id=" . $this->getUserId());
            $user_exam = $model->selectWith($select)->toArray();
            
        } else {
            $user_exam = array();
        }

        if (
                ($request['_token'] == false && !isset($identity['examing'])) 
                || (!is_array($exam_time) || count($exam_time) == 0)//nằm ngoài thời gian thi
                || (is_array($user_exam) && count($user_exam) > 0 && $user_exam[0]['allow_re_exam'] != '1')//đã thi rồi
        ) {
            $questions = array();
        } else {
            $questions = Question::getQuestionsByQuestionIds($questionIds);
        }

        if (
                $request['question_id']//nhấn nút hoàn tất
                || (!is_array($exam_time) || count($exam_time) == 0)//nằm ngoài thời gian thi
                || (is_array($user_exam) && count($user_exam) > 0 && $user_exam[0]['allow_re_exam'] != '1')//đã thi rồi
        ) {
            $nganhNghes = array();
        } else {
            
            $model = new \Application\Model\Table('');
            $select = new \Zend\Db\Sql\Select();
            $select->from('nganh_nghe');
            $nganhNghes = $model->selectWith($select)->toArray();
        }

        if (
                $request['_token']
                || (!is_array($exam_time) || count($exam_time) == 0)//nằm ngoài thời gian thi
                || (is_array($user_exam) && count($user_exam) > 0 && $user_exam[0]['allow_re_exam'] != '1')//đã thi rồi
        ) {
            $showFormNganhNgheCapBac = FALSE;
        } else {
            if (isset($identity['examing']) && $identity['examing'] == true) {
                
                $showFormNganhNgheCapBac = FALSE;
            } else {
                
                $showFormNganhNgheCapBac = true;
            }
        }
        
        $this->param['questions'] = $questions;
        $this->param['showFormNganhNgheCapBac'] = $showFormNganhNgheCapBac;
        $this->param['nganhNgheId'] = $nganhNgheId;
        $this->param['level'] = $level;
        $this->param['questionIds'] = $questionIds;
        $this->param['nganhNghes'] = $nganhNghes;
    }

    /**
     * nếu session đang thi chưa được bật thi sẽ bật
     * @param array $data
     * @param int|string $nganhNgheId
     * @param int|string $level
     * @param array $questionIds
     */
    private function setupExamingSession($request, $nganhNgheId, $level, $questionIds) 
    {
        if ($request['_token']) {
            $session = new Container('base');
            $identity = $session->offsetGet('user');
            if (!isset($identity['examing']) || $identity['examing'] == FALSE) {                
                $this->turnOnExamingSession($nganhNgheId, $level, $questionIds, $identity);
                $session->offsetSet('user',$identity);    
            }
        }
    }

    private function processReExam($request) 
    {
        if ($request['_token'] && $request['question_id']) {
            $this->submitReExam($request);
            exit;
        }

        $this->param['miniutes']=0;
    }

    private function processExam($request) 
    {
        
        $date = date('Y-m-d');
        $h = $request['h'] ? $request['h'] : date('H');
        $m = $request['i'] ? $request['i'] : date('i');
        
        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select->columns(array(
            "date"=>new \Zend\Db\Sql\Expression('DATE(`date`)'),
            "sh"=>"sh",
            "sm"=>"sm",
            "eh"=>"eh",
            "em"=>"em"
        ))->from('exam_time')->where("DATE(`date`)='$date' AND ($h>sh OR ($h=sh AND $m>=sm)) AND ($h < eh OR ($h=eh AND $m<=em))");
        $row = $model->selectWith($select)->toArray();
        if (is_array($row) && count($row) > 0) {
            $row=$row[0];
            $start = new \DateTime($row['date'] . ' ' . $row['sh'] . ':' . $row['sm'] . ':00');
            $current = new \DateTime(date('Y-m-d H:i:00'));
            $diff = $current->diff($start);
            $this->param['eh']=$row['eh'];
            $this->param['em']=$row['em'];
        }
        if ((!is_array($row) || count($row) == 0) && ($request['_token'] == '' || ($request['_token']&& $request['question_id']==''))) {
            $miniutes = 0;
            $message = 'Thời điểm này không nằm trong thời gian thi hoặc bạn đã hết giờ thi.';
        } else {
            
            $model = new \Application\Model\Table('');
            $select = new \Zend\Db\Sql\Select();
            $select->from('user_exam')->where("DATE(exam_date)='" . $row['date'] . "' AND user_id=" . $this->getUserId());
            $row = $model->selectWith($select)->toArray();
            if (is_array($row) && count($row) > 0) {
                $miniutes = 0;
                $message = '';
            } else {
                if ($request['_token'] && $request['question_id']) {
                    $this->submitExam($request);
                    exit;
                }
                $miniutes = $diff->h * 60 + $diff->i;
            }
        }

        
        $this->param['miniutes']=$miniutes;
        $this->param['message']=$message;
    }

}
