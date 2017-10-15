<?php
namespace Zend\Common;

class Pdf {

    const DOWNLOAD = 'D';
    const VIEW = 'I';

    /**
     * function common
     * @author Trần Công Tuệ <chanhduypq@gmail.com>
     * @param string $type
     * @param string $html
     * @param string $filename
     * @param string $title_header
     * @return void
     */
    public static function createFilePdf($type, $html, $filename, $title_header = '') {

        require_once 'tcpdf/tcpdf_autoconfig.php';
        require_once 'tcpdf/tcpdf.php';
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);




        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetPrintHeader(true);

        $pdf->SetHeaderData('', 0, $title_header, '');

// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

// ---------------------------------------------------------
// set font
        $pdf->SetFont('dejavusans', '', 10);

// add a page
        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');




// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// reset pointer to the last page
        $pdf->lastPage();

// ---------------------------------------------------------
//Close and output PDF document
        ob_end_clean();
        $pdf->Output($filename, $type);
        exit;
    }

}
