<?php
namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
class Userreview extends AbstractTableGateway {

    public $table = "user_review";

    public function __construct($tableName=null) 
    {
        if ($this->table == NULL) {
            $this->table = $tableName;
        }
    }

    public static function getHtmlForReviewResult($user_review_id, &$title_header) {        
        $select = new \Zend\Db\Sql\Select();
        $select->columns(array(
                    "sh" => "sh",
                    "sm" => "sm",
                    "eh" => "eh",
                    "em" => "em",
                    "es" => "es",
                    "question_id" => "user_review_detail.question_id",           
                    "nganh_nghe_id" => "user_review.nganh_nghe_id",
                    "level" => "user_review.level",
                    "date" => "DATE_FORMAT(user_review.review_date,'%d/%m/%Y')",
                    "year" => "DATE_FORMAT(user_review.review_date,'%Y')",
                    "danh_xung" => "user.danh_xung",
                    "full_name" => "user.full_name",
                    "is_correct" => "user_review_detail.is_correct",
                    "dapan_sign" => "user_review_detail.dapan_sign",
                    "answer_sign" => "user_review_detail.answer_sign",
                    "answer_id" => "user_review_detail.answer_id",
                    "answers_json" => "user_review_detail.answers_json",
                    "question_content" => "question.content",
                    "title" => "nganh_nghe.title"
                ))
                ->from("user_review")
                ->join("user_review_detail", "user_review.id=user_review_detail.user_review_id")
                ->join("user", "user.id=user_review.user_id")
                ->join("nganh_nghe", "nganh_nghe.id=user_review.nganh_nghe_id")
                ->join("question", "question.id=user_review_detail.question_id")
                ->where("user_review.id=$user_review_id")
                ->order("user_review_detail.id ASC")
                ;
        $row = $this->selectWith($select)->toArray();
        $count_correct = 0;
        $count_incorrect = 0;
        $questionIds = array();
        foreach ($row as $r) {
            if ($r['is_correct'] == '1') {
                $count_correct++;
            } else {
                $count_incorrect++;
            }
            $questionIds[] = $r['question_id'];
            $questions[$r['question_id']]['question_content'] = $r['question_content'];
            $answers_json= json_decode(html_entity_decode($r['answers_json']),TRUE);
            foreach ($answers_json as $key=>$value){
                $questions[$r['question_id']]['answers'][] = array('answer_sign' => $key, 'answer_content' => $value['content'], 'is_dap_an' => $value['is_dapan']);
            }
            
        }


        $diem = round($count_correct * 10 / count($row), 1);
        
        
        $questionsHtml = \Application\Model\Pdfresult::getQuestionsHtml($questions);
        
        
        \Application\Model\Pdfresult::setTime($startTime, $endTime, $during, $row[0]);

        
        $level = \Application\Model\Pdfresult::getLevelHtml($row[0]['level']);
        $title_header = $row[0]['date'];
        $headers = json_decode(\Admin\Model\HeaderpdfMapper::getHeader(), TRUE);
        foreach ($headers as &$header) {
            $header = str_replace('{level}', $level, $header);
            $header = str_replace('{nam}', $row[0]['year'], $header);
        }
        
        $header = \Application\Model\Pdfresult::getHeaderHtml($headers);
        $css = \Application\Model\Pdfresult::getCss();
        \Application\Model\Pdfresult::setHtmlForDetailResult($div1, $div2, $div3, $row);
        $detailResultHtml = \Application\Model\Pdfresult::getDetailResultHtml($div1, $div2, $div3);
        $userInfoHtml = \Application\Model\Pdfresult::getUserInfoHtml($row[0]['full_name'], $row[0]['title'], $row[0]['date'], $startTime, $endTime, $during);
        $html = '<style>
                  ' . $css . '
                </style>
                <body>
                ' . $header . '
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    '.$userInfoHtml.'
                </table>
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    ' . $detailResultHtml . '
                </table>
                
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 1%;">&nbsp;</td>
                            <td style="width: 98%;text-align: left;border: 2px solid #cccccc;padding: 20px;">
                                <div>&nbsp;</div>
                                &nbsp;&nbsp;Số câu đúng:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $count_correct . '<br>
                                Điểm kiểm tra:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $diem . '<br>
                                
                            </td>
                            <td style="width: 1%;">&nbsp;</td>
                        </tr>                       
                        
                    </tbody>
                </table>
                <div>&nbsp;</div>
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                            <td colspan="3" style="width: 100%;text-align: center;font-size: 20px;">ĐÁP ÁN CHI TIẾT</td>
                            
                            
                        </tr>  
                        <tr>
                            <td style="width: 1%;">&nbsp;</td>
                            <td style="width: 98%;text-align: left;border: 2px solid #666666;">
                                ' . $questionsHtml . '
                            </td>
                            <td style="width: 1%;">&nbsp;</td>
                            
                        </tr>                       
                        
                    </tbody>
                </table>
                </body>
                ';

        return $html;
    }

}

?>