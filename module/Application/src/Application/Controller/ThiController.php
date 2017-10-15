<?php

class ThiController extends Core_Controller_Action 
{

    public function init() 
    {
        parent::init();
        $this->view->headTitle('Thi trắc nghiệm', true);
    }

    public function viewresultAction() 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $row = $db->fetchRow("SELECT * FROM user_exam WHERE user_id=" . $this->getUserId() . " ORDER BY exam_date DESC LIMIT 1");
        if (!is_array($row) || count($row) == 0) {
            $this->_helper->redirector('index', 'thi', 'default');
            return;
        }
        $html = \Application\Model\Userexam::getHtmlForExamResult($row['id'], $title_header);

        $date = explode(' ', $row['exam_date']);
        $date = explode('-', $date[0]);
        Core_Common_Pdf::createFilePdf(Core_Common_Pdf::DOWNLOAD, $html, $date[0] . '_' . $date[1] . '_' . $date[2] . '.pdf', $title_header);
    }

    public function indexAction() 
    {

        $this->view->i = intval(date('i'));
        $this->view->h = intval(date('H'));
        $data = $this->_request->getPost();
        $this->setParams($data, $nganhNgheId, $level, $questionIds, $questions, $nganhNghes, $showFormNganhNgheCapBac);
        $this->setupExamingSession($data, $nganhNgheId, $level, $questionIds);
        if ($this->isReExam()) {
            $this->processReExam($data);
        } else {
            $this->processExam($data);
        }

        $this->view->success = $this->getMessage();
        $this->view->nganhNgheId = $nganhNgheId;
        $this->view->level = $level;
        $this->view->questions = $questions;
        $this->view->nganhNghes = $nganhNghes;
        $this->view->showFormNganhNgheCapBac=$showFormNganhNgheCapBac;
    }
    
    private function saveDB($data) 
    {
        $date = date('Y-m-d');
        $h = $data['h'];
        $m = $data['i'];
        if($m==0){
            $m=59;
            $h--;
        }
        else{
            $m--;
        }
        $db = Core_Db_Table::getDefaultAdapter();
        $row = $db->fetchRow("select * from user_exam where DATE(exam_date)='" . $date . "' AND user_id=" . $this->getUserId());
        if (is_array($row) && count($row) > 0) {
            return;
        }

        $db->beginTransaction();
        try {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $sh = $identity['sh'];
            $sm = $identity['sm'];
            $modelUserExam = new \Application\Model\Userexam();
            $userExamId = $modelUserExam->insert(
                    array(
                        'user_id' => $this->getUserId(),
                        'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                        'level' => $data['level_form2'],
                        'exam_date' => date("Y-m-d $h:$m:s"),
                        'sh' => $sh,
                        'sm' => $sm,
                        'eh' => $h,
                        'em' => $m,
                        'es'=> rand(1, 59),
                    )
            );
            $i = 0;
            $questionIds = $data['question_id'];
            $answerIds = $data['answer_id'];
            $answerSigns = $data['answer_sign'];
            $dapanSigns = $data['dapan_sign'];
            $answersJsons = $data['answers_json'];
            $count_correct = 0;
            $user_exam_detail = new \Application\Model\Userexamdetail();
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }

                $user_exam_detail->insert(array(
                    'user_exam_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i]=='Z'?' ':$answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                    'answers_json' => $answersJsons[$i],
                ));
            }

            $config_exam = $db->fetchRow("SELECT * FROM config_exam");

            if ($count_correct >= $config_exam['phan_tram'] * count($questionIds)) {
                $user_pass = new \Application\Model\Userpass();
                $user_pass->insert(array(
                    'user_id' => $this->getUserId(),
                    'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                    'level' => $data['level_form2'],
                    'user_exam_id' => $userExamId,
                ));
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    private function saveDBAgain($data) 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $user_exam = $db->fetchRow("select * from user_exam where user_id=" . $this->getUserId() . ' ORDER BY exam_date DESC LIMIT 1');
            if (is_array($user_exam) && count($user_exam) > 0) {
                $userExamId = $user_exam['id'];
            } else {
                $userExamId = -1;
            }
            $i = 0;
            $questionIds = $data['question_id'];
            $answerIds = $data['answer_id'];
            $answerSigns = $data['answer_sign'];
            $dapanSigns = $data['dapan_sign'];
            $answersJsons = $data['answers_json'];
            $count_correct = 0;
            $user_exam_detail = new \Application\Model\Userexamdetail();
            $user_exam_detail->delete('user_exam_id=' . $userExamId);
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }

                $user_exam_detail->insert(array(
                    'user_exam_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i]=='Z'?' ':$answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                    'answers_json' => $answersJsons[$i],
                ));
            }

            $modelUserExam = new \Application\Model\Userexam();

            $config_exam = $db->fetchRow("SELECT * FROM config_exam");

            if ($count_correct >= $config_exam['phan_tram'] * count($questionIds)) {
                $user_pass = new \Application\Model\Userpass();
                $user_pass->insert(array(
                    'user_id' => $this->getUserId(),
                    'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                    'level' => $data['level_form2'],
                    'user_exam_id' => $userExamId,
                ));
                $allow_re_exam = 0;
            } else {
                $allow_re_exam = 1;
            }
            $modelUserExam->update(array('allow_re_exam' => $allow_re_exam, 'nganh_nghe_id' => $data['nganh_nghe_id_form2'], 'level' => $data['level_form2']), 'id=' . $userExamId);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
    }

    
    private function submitReExam($data) 
    {
        $this->saveDBAgain($data);
        $this->resetSession();
        Core::message()->addSuccess('Chúc mừng bạn đã hoàn thành kỳ thi lần này.');
        $this->_helper->redirector('index', 'thi', 'default');
        exit;
    }

    private function submitExam($data) 
    {
        $this->saveDB($data);
        $this->resetSession();
        Core::message()->addSuccess('Chúc mừng bạn đã hoàn thành kỳ thi lần này.');
        $this->_helper->redirector('index', 'thi', 'default');
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
        $db = Core_Db_Table::getDefaultAdapter();
        $user_exam = $db->fetchRow("select * from user_exam where user_id=" . $this->getUserId() . ' ORDER BY exam_date DESC LIMIT 1');
        if (is_array($user_exam) && count($user_exam) > 0 && $user_exam['allow_re_exam'] == '1') {
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
    private function setParams($data, &$nganhNgheId, &$level, &$questionIds, &$questions, &$nganhNghes, &$showFormNganhNgheCapBac) 
    {
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $db = Core_Db_Table::getDefaultAdapter();
        if (isset($identity['examing']) && $identity['examing'] == true) {
            $nganhNgheId = $identity['nganh_nghe_id'];
            $level = $identity['level'];
            $questionIds = $identity['questionIds'];
        } else {
            $nganhNgheId = (count($data) > 0 && isset($data['nganh_nghe_id'])) ? $data['nganh_nghe_id'] : 0;
            $level = (count($data) > 0 && isset($data['level'])) ? $data['level'] : 0;            
            $config_exam = $db->fetchRow("SELECT * FROM config_exam");
            $questionIds = \Application\Model\Question::getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam['number']);
        }
        
        $date = date('Y-m-d');
        $h = $this->_getParam('h', date('H'));
        $m = $this->_getParam('i', date('i'));
        $exam_time = $db->fetchRow("select DATE(`date`) AS date,sh,sm,eh,em from exam_time where DATE(`date`)='$date' AND ($h>sh OR ($h=sh AND $m>=sm)) AND ($h < eh OR ($h=eh AND $m<=em))");
        if (is_array($exam_time) && count($exam_time) > 0) {
            $user_exam = $db->fetchRow("select * from user_exam where DATE(exam_date)='" . $exam_time['date'] . "' AND user_id=" . $this->getUserId());
        } else {
            $user_exam = array();
        }

        if (
                (count($data) == 0 && !isset($identity['examing'])) 
                || (!is_array($exam_time) || count($exam_time) == 0)//nằm ngoài thời gian thi
                || (is_array($user_exam) && count($user_exam) > 0 && $user_exam['allow_re_exam'] != '1')//đã thi rồi
        ) {
            $questions = array();
        } else {
            $questions = \Application\Model\Question::getQuestionsByQuestionIds($questionIds);
        }

        if (
                (count($data) > 0 && isset($data['question_id']))//nhấn nút hoàn tất
                || (!is_array($exam_time) || count($exam_time) == 0)//nằm ngoài thời gian thi
                || (is_array($user_exam) && count($user_exam) > 0 && $user_exam['allow_re_exam'] != '1')//đã thi rồi
        ) {
            $nganhNghes = array();
        } else {
            $nganhNghes = $db->fetchAll('SELECT * FROM nganh_nghe');
        }

        if (
                count($data) > 0 
                || (!is_array($exam_time) || count($exam_time) == 0)//nằm ngoài thời gian thi
                || (is_array($user_exam) && count($user_exam) > 0 && $user_exam['allow_re_exam'] != '1')//đã thi rồi
        ) {
            $showFormNganhNgheCapBac = FALSE;
        } else {
            if (isset($identity['examing']) && $identity['examing'] == true) {
                $showFormNganhNgheCapBac = FALSE;
            } else {
                $showFormNganhNgheCapBac = true;
            }
        }
    }

    /**
     * nếu session đang thi chưa được bật thi sẽ bật
     * @param array $data
     * @param int|string $nganhNgheId
     * @param int|string $level
     * @param array $questionIds
     */
    private function setupExamingSession($data, $nganhNgheId, $level, $questionIds) 
    {
        if (count($data) > 0) {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            if (!isset($identity['examing']) || $identity['examing'] == FALSE) {
                $auth->clearIdentity();
                $this->turnOnExamingSession($nganhNgheId, $level, $questionIds, $identity);
                $auth->getStorage()->write($identity);
            }
        }
    }

    private function processReExam($data) 
    {
        if (count($data) > 0) {
            if (isset($data['question_id'])) {
                $this->submitReExam($data);
                exit;
            }
        }
        $this->view->miniutes = 0;
    }

    private function processExam($data) 
    {
        $db = Core_Db_Table::getDefaultAdapter();
        $date = date('Y-m-d');
        $h = $this->_getParam('h', date('H'));
        $m = $this->_getParam('i', date('i'));
        $row = $db->fetchRow("select DATE(`date`) AS date,sh,sm,eh,em from exam_time where DATE(`date`)='$date' AND ($h>sh OR ($h=sh AND $m>=sm)) AND ($h < eh OR ($h=eh AND $m<=em))");
        if (is_array($row) && count($row) > 0) {
            $start = new \DateTime($row['date'] . ' ' . $row['sh'] . ':' . $row['sm'] . ':00');
            $current = new \DateTime(date('Y-m-d H:i:00'));
            $diff = $current->diff($start);
            $this->view->eh = $row['eh'];
            $this->view->em = $row['em'];
        }
        if ((!is_array($row) || count($row) == 0) && (count($data) == 0 || (count($data) > 0 && !isset($data['question_id'])))) {
            $this->view->miniutes = 0;
            $this->view->message = 'Thời điểm này không nằm trong thời gian thi hoặc bạn đã hết giờ thi.';
        } else {
            $row = $db->fetchRow("select * from user_exam where DATE(exam_date)='" . $row['date'] . "' AND user_id=" . $this->getUserId());
            if (is_array($row) && count($row) > 0) {
                $this->view->miniutes = 0;
                $this->view->message = '';
            } else {
                if (count($data) > 0) {
                    if (isset($data['question_id'])) {
                        $this->submitExam($data);
                        exit;
                    }
                }
                $this->view->miniutes = $diff->h * 60 + $diff->i;
            }
        }
    }

}
