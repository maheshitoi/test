<?php

namespace Core\Controllers;

use Core\Libraries\EmailConetentGenerator;
use Core\Libraries\PDFGenerator;

class EmailController extends BaseController
{

    private $repository;
    private $emailGen;
    public function __construct()
    {
        helper('Core\Helpers\Utility');
        $this->initializeFunction();
        $this->emailGen = new EmailConetentGenerator();
    }

    public function generateEmail($id = '')
    {
        $data      = $this->getDataFromUrl('json');
        $sendEmail = false;
        if (checkValue($data, 'send_email') == 'true' && !empty($result)) {
            $sendEmail = true;
        }
        if (checkValue($data, 'is_pdf_print')) {
            return $this->preview_pdf($id);
        }
        if (!empty($id) && $sendEmail) {
            $result = $this->emailGen->genContentSentEmail($id, $data);
        } else {
            $result = $this->emailGen->genContentSentEmail($id, $data, [], false);
            if (is_object($result)) {
                $result = (array) $result;
            }
        }
        
        return $this->message(200, $result, 'success');
    }

    public function preview_pdf($id = '')
    {
        $data = $this->getDataFromUrl('json');
        if (!$id && !checkValue($data, 'pdf_content')) {
            return $this->message(400, null, 'plesae verify Pdf_content Or id ');
        }
        $pdfGen     = new PDFGenerator();
        $pdfContent = $data['pdf_content'] ?? '';
        if (!empty($id)) {
            $data = $this->emailGen->genContentSentEmail($id, $data, [], false);
        }
        return $pdfGen->genByTemp((array)$data, $output = 'd', $doc_name = 'attchment');
    }

    protected function getContent($id, $data)
    {
        return $this->emailGen->generateEmail($id, $data);
    }
}
