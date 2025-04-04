<?php
namespace Core\Libraries;

use Mpdf\Mpdf;

class PDFMaker
{
    private $response;
    protected $pdf;
    private $header;
    public $footer;
    public $section = [];
    public $dateCol = [];
    public $imgCol  = [];
    private $html   = '';
    public $pageSize;
    public function __construct($html = '', $pageSize = 'L', $margin = [], $section = [], $dateCol = [], $imgCol = ['logo_img', 'profile_img'])
    {
        require_once APPPATH . '/ThirdParty/mpdf/vendor/autoload.php';
        $this->pageSize = $pageSize;
        $this->pdf      = new \Mpdf\Mpdf(['mode' => 'UTF-8', 'format' => $pageSize, 'margin_left' => $margin[0] ?? 15,
            'margin_right'                           => $margin[1] ?? 15,
            'margin_top'                             => $margin[2] ?? 16,
            'margin_bottom'                          => $margin[3] ?? 16,
        ]);
        $this->section = $section;
        $this->dateCol = $dateCol;
        $this->imgCol  = $imgCol;
        $this->html    = $html;
        $this->pdf->autoPageBreak = true;
        $this->pdf->AddPage();
        $this->pdf->SetDefaultFont('arial');
        $this->response = \Config\Services::response();
    }
    public function setImgCol($col)
    {
        $this->imgCol = $col;
    }
    public function setDateCol($col)
    {
        $this->dateCol = $col;
    }
    public function setSection($col)
    {
        $this->section = $col;
    }
    public function setHtml($col)
    {
        $this->html = $col;
    }

    public function create($data, $doc_name = 'doc', $output = 'd', $waterMark = '')
    {
        header('content-type: text/html; charset=utf-8');
        if (!empty($this->html)) {
            if (is_array($this->section)) {
                foreach ($this->section as $key => $v) {
                    $this->html = findReplaceWithData($this->html, $v, $data[$v] ?? [], $this->dateCol, $this->imgCol);
                    if (isset($data[$v])) {
                        unset($data[$v]);
                    }
                }
            }
            if (is_array($data)) {
                $this->html = parameterReplace($data,  $this->html);
                
            } else {
                $this->html;
            }
        }
        ob_start();
        if ($waterMark) {
            $this->pdf->showWatermarkImage = true;
            $this->pdf->SetWatermarkImage($waterMark, '0.1', ["150", "100"], 'P');
        }
        $this->html = htmlspecialchars_decode('<html>' . $this->html . '</html>');
        $this->pdf->autoScriptToLang = true;
        $this->pdf->autoLangToFont   = true;
        // $this->pdf->SetFont('freeserif', '', 20);
        $this->pdf->jSWord=100;
        $this->pdf->jSmaxChar=5;
        $this->pdf->WriteHTML($this->html, 0, true, false);
        $this->pdf->SetCompression(true);
        // $this->pdf->SetFont('ArialBold', '', 9);
        ob_clean();
        if ($output == 'd') {
            $p = $this->pdf->Output($doc_name . '.pdf', 'd');
            $this->response->setHeader('Content-Type', 'application/pdf')->noCache();
            return $this->response->download('', $p);
        } else if ($output == 's') {
            return $this->pdf->Output($doc_name . '.pdf', 'S');
        } else if ($output == 'f') {
            $savePath = 'uploads/temp/' . str_replace(" ", "_", $doc_name) . '.pdf';
            $this->pdf->Output($savePath, 'F');
            return $savePath;
        }
    }
    
    public function genFooter($data, $html = '')
    {
        $res =$this->replaceHtml($html, $data);
        $this->pdf->SetFooter($res);
    }

    public function genHeader($data, $html = '')
    {
        $res =$this->replaceHtml($html, $data);
        //$this->pdf->SetHeader($res);
    }

    protected function replaceHtml($html, $data)
    {
        if (empty($html)) {
            return false;
        }
        return parameterReplace(array_keys($data), $data, $html);
    }

    public function genHeaderOld($type = 2)
    {
//         $this->pdf->autoPageBreak = true;
//         $this->pdf->AddPage();
//         $this->pdf->shrink_tables_to_fit = 1;
//         $this->pdf->Image(getRealPath("asset/img/gems_logo.jpeg"), 11, 6.5, 40, 27.5);
//         $this->pdf->SetDefaultFont('arial');
//         $this->pdf->SetFont('comicsansms', 'mono', 18);
//         if ($type == 1) {
//             $this->pdf->SetXY(50, 5);
//             $this->pdf->SetTextColor(5, 106, 162);
//             $this->pdf->Cell(0, 15, 'GOSPEL ECHOING MISSIONARY SOCIETY (GEMS)', 0, false, 'C', 0, '', 0, false, 'M', 'M');
//             $this->pdf->SetXY(50, 15);
//             $this->pdf->SetTextColor(230, 0, 0);
//             $this->pdf->SetFont('comicsansms', 'mono', 11);
//             $this->pdf->Cell(140, 5, 'Transforming Peoples to Transform Nations', 0, 1, 'C');
//             $this->pdf->Ln();
//             $this->pdf->SetXY(50, 20);
//             $this->pdf->SetFont('ArialBold', '', 9);
//             $this->pdf->SetTextColor(0);
//             $this->pdf->Cell(145, 10, 'GEMS, Sikaria, Indrapuri PO, Dehri On Sone, Rohtas Dist. Bihar 821308', 0, 1, 'C');
//             $this->pdf->Ln();
//             $this->pdf->SetXY(50, 25);
//             $this->pdf->SetFont('ArialBold', '', 9);
//             $this->pdf->Cell(145, 10, '+916184 234567 - gems@gemsbihar.org', 0, 1, 'C');
//             $this->pdf->Ln();
//             $this->pdf->SetXY(50, 28);
//             $this->pdf->SetLineWidth(0.8);
//             $this->pdf->SetDrawColor(10, 10, 10);
//             $this->pdf->Line(0, 35, $this->pdf->w, 35);
//             $this->pdf->Ln();
//         } else {
//             $this->pdf->SetXY(60, 10);
//             $this->pdf->SetTextColor(5, 106, 162);
//             $this->pdf->Cell(0, 15, 'GOSPEL ECHOING MISSIONARY SOCIETY (GEMS)', 0, false, 'C', 0, '', 0, false, 'M', 'M');

//             $this->pdf->SetXY(60, 23);
//             //$this->pdf->addFont('MonotypeCoversia', '', 'MonotypeCoversia.php');
//             //$this->pdf->SetFont('MonotypeCoversia', '', 11);
//             $this->pdf->SetTextColor(230, 0, 0);
//             $this->pdf->SetFont('comicsansms', 'mono', 12);
//             $this->pdf->Cell(140, 5, 'Transforming Peoples to Transform Nations', 0, 1, 'C');
//             $this->pdf->Ln();

//             $this->pdf->SetXY(60, 30);
//             // $this->pdf->addFont('Arial', '', 'Arial.php');
//             // $this->pdf->SetFont('Arial', 'B', 11);
//             //$this->pdf->addFont('ArialBold', '', 'ArialBold.php');
//             $this->pdf->SetFont('ArialBold', '', 11);
//             $this->pdf->SetTextColor(0);
//             $this->pdf->Cell(145, 10, 'GEMS, Sikaria, Indrapuri PO, Dehri On Sone, Rohtas Dist. Bihar 821308', 0, 1, 'C');
//             $this->pdf->Ln();
//             $this->pdf->SetXY(60, 36);
//             $this->pdf->SetFont('ArialBold', '', 9);
//             $this->pdf->Cell(145, 10, '+916184 234567 - gems@gemsbihar.org | +917091 198382 sponsors@gemsbihar.org', 0, 1, 'C');

//             $this->pdf->Ln();

//             //$this->pdf->addFont('Times', '', 'times.php');
//             //$this->pdf->SetFont('Times', 'I', 12);
//             $this->pdf->SetXY(10, 34);
//             $this->pdf->Cell(45, 5, 'D. Augustine Jebakumar ', 0, 1, 'C');
//             $this->pdf->SetFont('Times', 'B', 12);
//             $this->pdf->Cell(40, 5, 'General Secretary ', 0, 1, 'C');
//             $this->pdf->Ln();
//         }

// // set font
//         $this->pdf->autoScriptToLang = true;
//         $this->pdf->autoLangToFont   = true;
//         $this->pdf->backupSubsFont   = ['dejavusanscondensed'];
//         $this->pdf->SetFont('freeserif', '', 20);
//         //$this->pdf->setPrintFooter(true);
//         $this->pdf->jSWord    = 100;
//         $this->pdf->jSmaxChar = 5;
    }

}
