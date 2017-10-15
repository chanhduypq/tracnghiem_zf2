<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
class ReviewController extends AbstractActionController {

//    public function init() {
//        parent::init();
//        $this->view->headTitle('Thi thử - Ôn tập', true);
//    }

    private function saveDB($data) {
//        
//        $db->beginTransaction();
          
        try {
            $model = new \Application\Model\Table('home_content');
                
            /**
             *  xóa thông tin lần ôn tập trước
             */
            $delete = new \Zend\Db\Sql\Delete('user_review_detail');  
            $delete->where('user_review_id IN (SELECT id FROM user_review WHERE user_id='.$this->getUserId().')');            
            $model->deleteWith($delete);
            
            $delete = new \Zend\Db\Sql\Delete('user_review');  
            $delete->where('user_id='.$this->getUserId());            
            $model->deleteWith($delete);
            
            $session = new Container('base');
            $identity = $session->offsetGet('user');
            $sh = $identity['sh_review'];
            $sm = $identity['sm_review'];

            $h = intval(date('H'));
            $m = intval(date('i'));

            /**
             * insert table user_review
             */
            $modelUserExam = new \Application\Model\Userreview();
            $userExamId = $modelUserExam->insert(
                    array(
                        'user_id' => $this->getUserId(),
                        'nganh_nghe_id' => $data['nganh_nghe_id_form2'],
                        'level' => $data['level_form2'],
                        'review_date' => date("Y-m-d $h:$m:s"),
                        'sh' => $sh,
                        'sm' => $sm,
                        'eh' => $h,
                        'em' => $m,
                        'es' => rand(1, 59),
                    )
            );
            
            /**
             * insert table user_review_detail
             */
            $i = 0;
            $questionIds = $data['question_id'];
            $answerIds = $data['answer_id'];
            $answerSigns = $data['answer_sign'];
            $dapanSigns = $data['dapan_sign'];
            $answersJsons = $data['answers_json'];
            $count_correct = 0;
            $user_exam_detail = new \Application\Model\Userreviewdetail();
            for ($i = 0, $n = count($questionIds); $i < $n; $i++) {
                if ($answerSigns[$i] == $dapanSigns[$i]) {
                    $is_correct = 1;
                    $count_correct++;
                } else {
                    $is_correct = 0;
                }
                $user_exam_detail->insert(array(
                    'user_review_id' => $userExamId,
                    'question_id' => $questionIds[$i],
                    'answer_id' => ($answerIds[$i] == '' ? '-1' : $answerIds[$i]),
                    'is_correct' => $is_correct,
                    'answer_sign' => $answerSigns[$i] == 'Z' ? ' ' : $answerSigns[$i],
                    'dapan_sign' => $dapanSigns[$i],
                    'answers_json' => $answersJsons[$i],
                ));
            }
//            $db->commit();
        } catch (Exception $e) {
//            $db->rollBack();
        }
    }

    public function viewresultAction() {
        $modelUserExam = new \Application\Model\Userreview();
        $row =$modelUserExam->select("user_id=" . $this->getUserId() . " ORDER BY review_date DESC LIMIT 1")->toArray();        
        if (!is_array($row) || count($row) == 0) {
            return $this->redirect()->toUrl('/application/review');   
        }
        $row=$row[0];
        $html = \Application\Model\Userreview::getHtmlForReviewResult($row['id'], $title_header);

        $date = explode(' ', $row['review_date']);
        $date = explode('-', $date[0]);
        \Zend\Common\Pdf::createFilePdf(\Zend\Common\Pdf::DOWNLOAD, $html, $date[0] . '_' . $date[1] . '_' . $date[2] . '.pdf', $title_header);
    }

    public function indexAction() {
        $session = new Container('base');
        $identity = $session->offsetGet('user');

        $data = $this->params()->fromPost();
         
        if (count($data) > 0) {//submit
            if (isset($data['question_id'])) {//trả lời câu hỏi xong và nhấn nút hoàn tất
                $this->saveDB($data);
                $this->resetSession();
                return $this->redirect()->toUrl('/application/review');   
            } else {//hệ thống đang ở trạng thái submit của việc [chọn ngành nghề, cấp bậc; sau đó nhấn nút bắt đầu]. Có thể vừa nhấn nút bắt đầu hoặc reload page

                if (isset($identity['examing_review']) && $identity['examing_review'] == true) {//reload page
                    $nganhNgheId = $identity['nganh_nghe_id_review'];
                    $level = $identity['level_review'];
                    $questionIds = $identity['questionIds_review'];
                } else {//mới vừa làm việc [chọn ngành nghề, cấp bậc; sau đó nhấn nút bắt đầu]
                    $nganhNgheId = $data['nganh_nghe_id'];
                    $level = $data['level'];
                    
                    $model = new \Application\Model\Table('config_exam');
                    $config_exam = $model->first();
                    $questionIds = \Application\Model\Question::getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam['number']);
                }

                $newQuestions = \Application\Model\Question::getQuestionsByQuestionIds($questionIds);
                
                if (!isset($identity['examing_review']) || $identity['examing_review'] == FALSE) {
                    $identity['examing_review'] = true;
                    $identity['time_start_review'] = time();
                    $identity['nganh_nghe_id_review'] = $nganhNgheId;
                    $identity['level_review'] = $level;
                    $identity['questionIds_review'] = $questionIds;
                    $identity['sh_review'] = date('H');
                    $identity['sm_review'] = date('i');
                }
                $session->offsetSet('user', $identity);
            }
        } else {//user vào page này bằng việc click trên menu

            if (isset($identity['examing_review']) && $identity['examing_review'] == true) {//[chọn ngành nghề, cấp bậc; sau đó nhấn nút bắt đầu], việc này đã được làm
                $level = $identity['level_review'];
                $nganhNgheId = $identity['nganh_nghe_id_review'];
                $questionIds = $identity['questionIds_review'];
                $newQuestions = \Application\Model\Question::getQuestionsByQuestionIds($questionIds);
            } else {//
                $nganhNgheId = $level = 0;
                $newQuestions = array();
            }
        }

        if (isset($identity['examing_review']) && $identity['examing_review'] == true) {
            $miniutes = (time() - $identity['time_start_review']) / 60;
            $miniutes = round($miniutes, 0);
        } else {
            $miniutes = 0;
        }

        $model = new \Application\Model\Table('nganh_nghe');
        $nganhNghes = $model->getAll();
        
        return new ViewModel(array(
            'questions' => $newQuestions,
            'nganhNghes' => $nganhNghes,
            'nganhNgheId' => $nganhNgheId,
            'level' => $level,
            'miniutes' => $miniutes
                )); 
    }

    
}
