<?php

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;

class Question extends AbstractTableGateway {

    const BAC1 = '1';
    const BAC2 = '2';
    const BAC3 = '3';
    const BAC4 = '4';
    const BAC5 = '5';

    public $table = "question";

    public function __construct($tableName = null) {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }

    public function getQuestions(&$total, $limit = null, $start = null) {

        $select = new \Zend\Db\Sql\Select();


        if (\Zend\Common\Numeric::isInteger($limit) && \Zend\Common\Numeric::isInteger($start)) {
            $select->from('question')->order('id ASC')->offset($start)->limit($limit);
        } else {
            $select->from('question')->order('id ASC');
        }

        $items = $this->selectWith($select)->toArray();

        $select = new \Zend\Db\Sql\Select();
        $select->columns(array('num' => new \Zend\Db\Sql\Expression('COUNT(*)')),FALSE)->from('question');
        $total = $this->selectWith($select)->toArray();
        $total = $total[0]['num'];


        for ($i = 0, $n = count($items); $i < $n; $i++) {
            $items[$i]['answers'] = $this->getAnswers($items[$i]['id']);
        }
        return $items;
    }

    public function getAnswers($parent_id) {
        if (\Zend\Common\Numeric::isInteger($parent_id) == FALSE) {
            return array();
        }
        $mapper = new \Application\Model\Answer();
        $items = $mapper->getAnswers($parent_id);
        return $items;
    }

    /**
     * lấy thông tin cả câu hỏi lẫn câu trả lời, đáp án cho mỗi câu hỏi đó
     * @param Core_Db_Table $db
     * @param array $questionIds
     * @return array
     */
    public static function getFullQuestions($db, $questionIds) {
        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select
                ->from("question",array("question_content" => "content"))
                ->join("answer", "question.id=answer.question_id",array("answer_content" => "content","answer_sign" => "sign"))
                ->join("dap_an", "dap_an.question_id=answer.question_id",array("dap_an_sign" => "sign"))
                ->where("question.id IN (" . implode(',', $questionIds) . ")");
        $questions = $model->selectWith($select)->toArray();

        $returnQuestions = array();
        foreach ($questions as $question) {
            $returnQuestions[$question['id']]['question_content'] = $question['question_content'];
            $returnQuestions[$question['id']]['answers'][] = array('answer_sign' => $question['answer_sign'], 'answer_content' => $question['answer_content'], 'is_dap_an' => ($question['answer_sign'] == $question['dap_an_sign']));
        }

        return $returnQuestions;
    }

    public static function getQuestionsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) {
        $questionIds = self::getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number);
        return self::getQuestionsByQuestionIds($questionIds);
    }

    public static function getQuestionsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level) {
        $questionIds = self::getQuestionIdsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level);
        return self::getQuestionsByQuestionIds($questionIds);
    }

    public static function getQuestionIdsByLevelAndNganhNgheId($nganhNgheId, $level, $config_exam_number) {
        $model = new \Application\Model\Table('');
        if ($level == '1') {//nếu là bậc 1
            $select = new \Zend\Db\Sql\Select();
            $select
                    ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                    ->join("question", "question.id=nganhnghe_question.question_id",array())
                    ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1")
                    ->order(new \Zend\Db\Sql\Expression('RAND()'))
                    ->limit($config_exam_number)
                    ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
            ;
            $rows = $model->selectWith($select)->toArray();
        } else if ($level == '2' || $level == '3') {//nếu là bậc 2/3
            $select->columns(array("data" => "data"),FALSE)
                    ->from("config_exam_level")
                    ->where("level=$level")
            ;
            $rows = $model->selectWith($select)->toArray();
            $levelJsonString = $rows[0]['data'];
            $levelJsonArray = json_decode(html_entity_decode($levelJsonString), true);
            if ($levelJsonArray['b2'] == '100') {//nếu hệ thống muốn lấy 100% câu b2 cho bậc 2/3
                $select = new \Zend\Db\Sql\Select();
                $select
                         ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                        ->join("question", "question.id=nganhnghe_question.question_id",array())
                        ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=2")
                        ->order(new \Zend\Db\Sql\Expression('RAND()'))
                        ->limit($config_exam_number)
                        ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                ;
                $rows = $model->selectWith($select)->toArray();
                if (count($rows) < $config_exam_number) {//nếu lấy chưa đủ thi phải lấy thêm b1 bù vào cho đủ $config_exam_number
                    $number = $config_exam_number - count($rows);
                    $select = new \Zend\Db\Sql\Select();
                    $select
                            ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                            ->join("question", "question.id=nganhnghe_question.question_id",array())
                            ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1")
                            ->order(new \Zend\Db\Sql\Expression('RAND()'))
                            ->limit($number)
                            ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                    ;
                    $rows = array_merge($rows, $model->selectWith($select)->toArray());
                }
            } else {
                $b2Number = intval($config_exam_number * $levelJsonArray['b2'] / 100);

                $select = new \Zend\Db\Sql\Select();
                $select
                        ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                        ->join("question", "question.id=nganhnghe_question.question_id",array())
                        ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=2")
                        ->order(new \Zend\Db\Sql\Expression('RAND()'))
                        ->limit($b2Number)
                        ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                ;
                $rows = $model->selectWith($select)->toArray();

                if ($b2Number > count($rows)) {//nếu trong db chỉ có 50 câu b2 mà config lại muốn lấy 60 câu b2
                    $b1Number = $config_exam_number - count($rows);
                } else {
                    $b1Number = $config_exam_number - $b2Number;
                }

                $select = new \Zend\Db\Sql\Select();
                $select
                         ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                        ->join("question", "question.id=nganhnghe_question.question_id",array())
                        ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1")
                        ->order(new \Zend\Db\Sql\Expression('RAND()'))
                        ->limit($b1Number)
                        ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                ;
                $rows = array_merge($rows, $model->selectWith($select)->toArray());
            }
        } else {//nếu là bậc 4/5
            $select = new \Zend\Db\Sql\Select();
            $select->columns(array("data" => "data"),FALSE)
                    ->from("config_exam_level")
                    ->where("level=$level")
            ;
            
            $rows = $model->selectWith($select)->toArray();
            $levelJsonString = $rows[0]['data'];
            $levelJsonArray = json_decode(html_entity_decode($levelJsonString), true);

            if ($levelJsonArray['b3'] == '100') {//nếu hệ thống muốn lấy 100% câu b3 cho bậc 4/5
                $select = new \Zend\Db\Sql\Select();
                $select
                         ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                        ->join("question", "question.id=nganhnghe_question.question_id",array())
                        ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=3")
                        ->order(new \Zend\Db\Sql\Expression('RAND()'))
                        ->limit($b2Number)
                        ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                ;
                $rows = $model->selectWith($select)->toArray();
                if (count($rows) < $config_exam_number) {//nếu lấy chưa đủ thi phải lấy thêm b1,b2 bù vào cho đủ $config_exam_number
                    $number = $config_exam_number - count($rows);

                    $select = new \Zend\Db\Sql\Select();
                    $select
                             ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                            ->join("question", "question.id=nganhnghe_question.question_id",array())
                            ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=2")
                            ->order(new \Zend\Db\Sql\Expression('RAND()'))
                            ->limit($number)
                            ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                    ;
                    $rows = array_merge($rows, $model->selectWith($select)->toArray());
                }
            } else {
                $b3Number = intval($config_exam_number * $levelJsonArray['b3'] / 100);
                $select = new \Zend\Db\Sql\Select();
                $select
                         ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                        ->join("question", "question.id=nganhnghe_question.question_id",array())
                        ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=3")
                        ->order(new \Zend\Db\Sql\Expression('RAND()'))
                        ->limit($b3Number)
                        ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                ;
                $rows = $model->selectWith($select)->toArray();

                $b2Number = intval($config_exam_number * $levelJsonArray['b2'] / 100);

                $select = new \Zend\Db\Sql\Select();
                $select
                         ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                        ->join("question", "question.id=nganhnghe_question.question_id",array())
                        ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=2")
                        ->order(new \Zend\Db\Sql\Expression('RAND()'))
                        ->limit($b2Number)
                        ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                ;
                $rows = array_merge($rows, $model->selectWith($select)->toArray());

                if ($b2Number + $b3Number > count($rows)) {//nếu trong db chỉ có 50 câu b2,b3 mà config lại muốn lấy 60 câu b2,b3
                    $b1Number = $config_exam_number - count($rows);
                } else {
                    $b1Number = $config_exam_number - $b2Number - $b3Number;
                }

                $select = new \Zend\Db\Sql\Select();
                $select
                         ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                        ->join("question", "question.id=nganhnghe_question.question_id",array())
                        ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level=1")
                        ->order(new \Zend\Db\Sql\Expression('RAND()'))
                        ->limit($b1Number)
                        ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                ;
                $rows = array_merge($rows, $model->selectWith($select)->toArray());

                if (count($rows) < $config_exam_number) {
                    $tempIds = array();
                    foreach ($rows as $row) {
                        $tempIds[] = $row['id'];
                    }
                    if (count($tempIds) > 0) {
                        $select = new \Zend\Db\Sql\Select();
                        $select
                                 ->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                                ->join("question", "question.id=nganhnghe_question.question_id",array())
                                ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=3 AND question.id NOT IN (" . implode(",", $tempIds) . ")")
                                ->order(new \Zend\Db\Sql\Expression('RAND()'))
                                ->limit($config_exam_number - count($rows))
                                ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
                        ;
                        $rows = array_merge($rows, $model->selectWith($select)->toArray());
                    }
                }
            }
        }

        $questionIds = array();
        foreach ($rows as $row) {
            $questionIds[] = $row['id'];
        }

        return $questionIds;
    }

    public static function getQuestionIdsByLevelAndNganhNgheIdForPageQuestion($nganhNgheId, $level) {
        if ($level == '1') {
            $level = '1';
        } else if ($level == '2' || $level == '3') {
            $level = '2';
        } else if ($level == '4' || $level == '5') {
            $level = '3';
        }

        $model = new \Application\Model\Table('');
        
        $select = new \Zend\Db\Sql\Select();
        $select->columns(array("id" => "question_id"),FALSE)->from("nganhnghe_question")
                ->join("question", "question.id=nganhnghe_question.question_id",array())
                ->where("nganhnghe_question.nganhnghe_id=$nganhNgheId AND question.level<=$level")
                ->order("question.id ASC")
                ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT)
        ;

        
        $rows = $model->selectWith($select)->toArray();
        
        $questionIds = array();
        foreach ($rows as $row) {
            $questionIds[] = $row['id'];
        }

        return $questionIds;
    }

    public static function getQuestionsByQuestionIds($questionIds) {
        if (!is_array($questionIds) || count($questionIds) == 0) {
            return array();
        }
        $newQuestions = array();

        $model = new \Application\Model\Table('');
        $select = new \Zend\Db\Sql\Select();
        $select
                ->from("question",array('is_dao', 'content'))
                ->join("nganhnghe_question", "question.id = nganhnghe_question.question_id",array('id'=> 'question_id'))
                ->join("answer", "answer.question_id=nganhnghe_question.question_id",array('answer_id'=> 'id','sign'=> 'sign','answer_content'=> 'content'))
                ->join("dap_an", "dap_an.question_id=nganhnghe_question.question_id",array('dapan_sign'=> 'sign'))
                ->where("question.id IN (" . implode(',', $questionIds) . ")")
                ->order("question.id ASC,answer.sign ASC")
        ;
        $questions = $model->selectWith($select)->toArray();

        foreach ($questions as $question) {
            $newQuestions[$question['id']]['id'] = $question['id'];
            $newQuestions[$question['id']]['content'] = $question['content'];
            $newQuestions[$question['id']]['answers'][$question['answer_id']] = array('content' => $question['answer_content'], 'sign' => $question['sign'], 'id' => $question['answer_id']);
            $newQuestions[$question['id']]['dapan_sign'] = $question['dapan_sign'];
            $newQuestions[$question['id']]['is_dao'] = $question['is_dao'];
        }
        return $newQuestions;
    }

}

?>