<?php 

namespace Application\Model;

class Pdfresult {

    public static function getQuestionsHtml($questions) {
        $questionsHtml = '';
        $i = 1;
        foreach ($questions as $key => $question) {
            if ($i > 1) {
                $questionsHtml .= '<div>&nbsp;</div>';
            }
            $questionsHtml .= '<div class="span12" style="color: blue;">
                    ' . $i . '. ' . $question['question_content'] . '
                </div>';
            foreach ($question['answers'] as $temp) {
                if ($temp['is_dap_an']) {
                    $questionsHtml .= '<div class="span12">
                            <strong><i><u>' . $temp['answer_sign'] . '. ' . $temp['answer_content'] . ' (*)</u></i></strong>
                        </div>';
                } else {
                    $questionsHtml .= '<div class="span12">
                            ' . $temp['answer_sign'] . '. ' . $temp['answer_content'] . '
                        </div>';
                }
            }
            $i++;
        }

        return $questionsHtml;
    }

    public static function getCss() {
        return 'html, body {
                    width: 210mm;
                    height: 297mm;
                  } 
                    
                  .span12 {
                    width: 100%;
                    *width: 99.94680851063829%;
                  }
                  
                  table.chitiet{
                      width: 80%;
                      border-collapse: collapse;
                  }
                  table.chitiet td{
                      width: 20%;
                      border: 2px solid #666666;
                text-align: center;
                vertical-align: middle;
                  }
                  tr.header td{
                      color: #cccccc;
                  }';
    }

    public static function getDetailResultHtml($div1, $div2, $div3) {
        return '<tbody>
                    <tr>
                        <td style="width: 33%;text-align: center;">
                            <table class="chitiet">
                                <tbody>
                                    <tr class="header">
                                        <td>Câu hỏi</td>
                                        <td>Câu đã chọn</td>
                                        <td>Đáp án đúng</td>
                                        <td>Kết quả</td>
                                    </tr>
                                    ' . $div1 . '
                                </tbody>
                            </table>
                        </td>
                        <td style="width: 33%;text-align: center;">
                            <table class="chitiet">
                                <tbody>
                                    <tr class="header">
                                        <td>Câu hỏi</td>
                                        <td>Câu đã chọn</td>
                                        <td>Đáp án đúng</td>
                                        <td>Kết quả</td>
                                    </tr>
                                    ' . $div2 . '
                                </tbody>
                            </table>
                        </td>
                        <td style="width: 33%;text-align: center;">
                            <table class="chitiet">
                                <tbody>
                                    <tr class="header">
                                        <td>Câu hỏi</td>
                                        <td>Câu đã chọn</td>
                                        <td>Đáp án đúng</td>
                                        <td>Kết quả</td>
                                    </tr>
                                    ' . $div3 . '
                                </tbody>
                            </table>
                        </td>
                    </tr>                       

                </tbody>';
    }

    public static function getUserInfoHtml($fullName, $title, $date, $startTime, $endTime, $during) {
        return '<tbody>
                    <tr>
                        <td style="width: 20%;text-align: left;">
                            <strong>Họ và tên:</strong>
                        </td>
                        <td style="width: 20%;text-align: left;">
                            ' . $fullName . '
                        </td>
                        <td style="width: 60%;text-align: left;">                            
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: left;">
                            <strong>Nghề dự thi:</strong>
                        </td>
                        <td style="width: 70%;text-align: left;">
                            ' . $title . '
                        </td>
                        <td style="width: 10%;text-align: left;">
                            <!--<strong>Năm sinh:</strong>-->
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;text-align: left;">
                            <strong>Ngày kiểm tra:</strong>
                        </td>
                        <td style="width: 20%;text-align: left;">
                            ' . $date . '
                        </td>
                        <td style="width: 60%;text-align: left;">
                            <strong>Bắt đầu:</strong>&nbsp;&nbsp;' . $startTime . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <strong>Kết thúc:</strong>&nbsp;&nbsp;' . $endTime . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <strong>Thời gian:</strong>&nbsp;&nbsp;' . $during . '   
                        </td>
                    </tr>

                </tbody>';
    }

    public static function getHeaderHtml($headers) {
        return '<table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 50%;text-align: center;">
                                <h3>' . $headers[0] . '</h3>
                        <h3>' . $headers[2] . '</h3>
                            </td>
                            <td style="width: 50%;text-align: center;">
                                <h3>' . $headers[1] . '</h3>
                        <h3>' . $headers[3] . '</h3>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="width: 100%;text-align: center;">
                                <h3>' . $headers[4] . '</h3>
                            </td>                            
                        </tr>
                    </tbody>
                </table>';
    }

    public static function setHtmlForDetailResult(&$div1, &$div2, &$div3, $result) {
        $motPhan = intval(ceil(count($result) / 3));
        $div1 = $div2 = $div3 = '';
        for ($i = 0; $i < $motPhan; $i++) {
            $div1 .= '<tr>
                        <td>Câu ' . ($i + 1) . '</td>
                        <td>' . ($result[$i]['answer_id'] == '-1' ? '' : $result[$i]['answer_sign']) . '</td>
                        <td>' . $result[$i]['dapan_sign'] . '</td>
                        <td>' . ($result[$i]['is_correct'] == '1' ? 'Đúng' : 'Sai') . '</td>
                    </tr>';
        }
        for (; $i < $motPhan * 2; $i++) {
            if (isset($result[$i])) {
                $div2 .= '<tr>
                        <td>Câu ' . ($i + 1) . '</td>
                        <td>' . ($result[$i]['answer_id'] == '-1' ? '' : $result[$i]['answer_sign']) . '</td>
                        <td>' . $result[$i]['dapan_sign'] . '</td>
                        <td>' . ($result[$i]['is_correct'] == '1' ? 'Đúng' : 'Sai') . '</td>
                    </tr>';
            }
        }
        for (; $i < count($result); $i++) {
            $div3 .= '<tr>
                        <td>Câu ' . ($i + 1) . '</td>
                        <td>' . ($result[$i]['answer_id'] == '-1' ? '' : $result[$i]['answer_sign']) . '</td>
                        <td>' . $result[$i]['dapan_sign'] . '</td>
                        <td>' . ($result[$i]['is_correct'] == '1' ? 'Đúng' : 'Sai') . '</td>
                    </tr>';
        }
    }

    public static function setTime(&$startTime, &$endTime, &$during, $data) {
        /**
         * chưa tìm được nguyên nhân tại sao save vào db user_review.em=null
         * khi xảy ra lỗi như vay thi $data['em']=''=>dòng code $endTime = new DateTime(date('Y-m-d ' . $data['eh'] . ':' . $data['em'] . ':' . $data['es'])); sẽ bị lỗi
         * do đó phải code thêm đoạn sau
         */
        if ($data['em'] == NULL || trim($data['em']) == '') {
            $data['em'] = $data['sm'];
        }
        $startTime = new \DateTime(date('Y-m-d ' . $data['sh'] . ':' . $data['sm'] . ':00'));
        $endTime = new \DateTime(date('Y-m-d ' . $data['eh'] . ':' . $data['em'] . ':' . $data['es']));
        $diff = $endTime->diff($startTime);
        $during = \Zend\Common\Numeric::convert($diff->h, 2) . ':' . (($diff->h == 0 && $diff->i == 0) ? '00' : \Zend\Common\Numeric::convert($diff->i, 2)) . ':' . \Zend\Common\Numeric::convert($diff->s, 2); // . ':00';
        if ($data['sh'] > 12) {
            $startTime = ($data['sh'] - 12) . ':' . \Zend\Common\Numeric::convert($data['sm'], 2) . ' PM';
        } else {
            $startTime = $data['sh'] . ':' . \Zend\Common\Numeric::convert($data['sm'], 2) . ' AM';
        }
        if ($data['eh'] > 12) {
            $endTime = ($data['eh'] - 12) . ':' . \Zend\Common\Numeric::convert($data['em'], 2) . ' PM';
        } else {
            $endTime = $data['eh'] . ':' . \Zend\Common\Numeric::convert($data['em'], 2) . ' AM';
        }
    }

    public static function getLevelHtml($level) {
        if ($level == '1') {
            return strtoupper('BẬC 1');
        } else if ($level == '2') {
            return strtoupper('BẬC 2');
        } else if ($level == '3') {
            return strtoupper('BẬC 3');
        } else if ($level == '4') {
            return strtoupper('BẬC 4');
        } else if ($level == '5') {
            return strtoupper('BẬC 5');
        }
    }

}
