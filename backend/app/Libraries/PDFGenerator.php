<?php

namespace Core\Libraries;

class PDFGenerator
{
    private $response;
    protected $pdf;

    public function __construct()
    {
        helper('Core\Helpers\Date');
        $this->response = \Config\Services::response();
    }

    public function create($type, $data, $doc_name = 'doc')
    {
        $html = '';
        $section = [];
        $dateCol = [];
        $imgCol = ['profile_img', 'pan_document', 'aadhar_document', 'bank_document', 'others_document'];
        $paperFormat = 'A4-L';

        // Template selection
        switch ($type) {
            case 'event_certificate':
                $j_url = BASEURL . '/templates/event_certificate.txt';
                break;
            default:
                return "Invalid type"; // Handle invalid types
        }
        

        // Load HTML template
        $html = file_get_contents($j_url);
        if (!$html) {
            return "Unable to load the template file."; // Error handling if template doesn't load
        }

        // Generate the footer (optional)
        $pdf = new PDFMaker($html, $paperFormat, '', $section, $dateCol, $imgCol);
        $pdf->genFooter($data, $data['pdf_footer'] ?? '');

        // Create and return the PDF
        return $pdf->create($data, $doc_name);
    }

    public function genByTemp($type, $data, $output = 'f', $doc_name = '')
    {
        switch ($type) {
            case 'member_certificate':
                $j_url = BASEURL . '/templates/member_certificate.txt';
                break;
            case 'member_invoice':
                $j_url = BASEURL . '/templates/member_invoice.txt';
                break;
            case 'certificate':
                $j_url ='D:/xampp/htdocs/iaoi/iaoi_full/backend/templates/certificate.txt';
                break;
            default:
                return "Invalid type";
        }
        $html = file_get_contents($j_url);
        if (!$html) {
            return "Unable to load the template file.";
        }
        $placeholders = [
            '{name}' => $data['name'] ?? '',
            '{member_id}' => $data['member_id'] ?? '',
            '{first_name}' => $data['first_name'] ?? '',
            '{last_name}' => $data['last_name'] ?? '',
            '{address}' => $data['address'] ?? '',
            '{country}' => $data['country'] ?? '',
            '{state}' => $data['state'] ?? '',
            '{pincode}' => $data['pincode'] ?? '',
            '{city}' => $data['city'] ?? '',
            '{transaction_id}' => $data['transaction_id'] ?? '',
            '{payment_date}' => $data['payment_date'] ?? ''

        ];
        $replaced_html = str_replace(array_keys($placeholders), array_values($placeholders), $html);
        $pdf_content = $replaced_html;
        $pdf = new PDFMaker($pdf_content, 'A4-P');
        return $pdf->create($pdf_content, $doc_name, $output);
    }


    public function genPdfHtmlContentWithHeader($body, $header = '', $footer = '')
    {
        // Replace content placeholders with actual data (header and footer)
        return $this->mapContentWithHtml($body, $header, $footer);
    }

    protected function mapContentWithHtml($body, $header = '', $footer = '')
    {
        // Prepare the HTML structure with a basic stylesheet
        $st = '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" />  
            <meta charset="utf-8"><style media="all">
            html, body, p, h1, h2, h3, td, span {
                font-family: freesans;
                letter-spacing: 0px;
                color: black;
                font-size: 18px;
                text-align: justify;
                word-spacing: 3px;
            }
            font-comicsansms {
                font-family: comicsansms !important;
            }
            </style></head><body>';

        // Combine header, body, and footer into a single HTML content
        $content = $st . $header . $body . $footer . '</body></html>';

        // Decode any special characters in the content
        $content = htmlspecialchars_decode($content);

        // Remove any figure tags from the content
        $pattern = [
            "/<\/figure>/",
            "/<figure[^>]*>/"
        ];
        $content = preg_replace($pattern, '', $content);

        // Replace base64 image data with placeholders
        $content = str_replace([
            'data:image/jpeg;base64,',
            'data:image/png;base64,',
            'data:image/gif;base64,',
            'data:image/bmp;base64,'
        ], '@', $content);

        return $content;
    }
}
