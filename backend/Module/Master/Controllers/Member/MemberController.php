<?php

namespace App\Controllers\Member;

use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use Core\Controllers\BaseController;
use App\Domain\Member\Member;
use App\Domain\Member\Member_request;
use Core\Controllers\DMLController;
use App\Infrastructure\Persistence\Member\SQLMemberRepository;
use App\Infrastructure\Persistence\Payment\SQLPayment_detailsRepository;
use Core\Infrastructure\Persistence\User\SQLUserRepository;
use Core\Libraries\EmailConetentGenerator;
use App\Libraries\Email;
use Core\Libraries\PDFGenerator;
use Config\Services;
use Core\Domain\User\UserLogin;
use Exception;

class MemberController extends BaseController
{
    use DMLController;
    private $repository;
    private $userAgentHepler;
    private $login_repository;
    private $memberReqRepo;
    private $pdfGenerate;
    private $paymentRepo;
    private $email;
    private $userRepo;
    public function __construct()
    {
        helper('Core\Helpers\Utility');
        $this->initializeFunction();
        $this->repository       = new SQLMemberRepository();
        $this->memberReqRepo    = new SQLMember_requestRepository();
        $this->paymentRepo      = new SQLPayment_detailsRepository();
        $this->pdfGenerate = new PDFGenerator();
        $this->email            = new Email;
        $this->login_repository = Services::UserLoginRepository();
    }

    public function memberApprove()
    {
        $password = rand(99, 9999);
        $data = $this->getDataFromUrl('json');
        $user_id = $this->userData->user_id ?? 1;
        $paymentData = $this->paymentRepo->findAllByWhere(['mem_req_fk_id' => $data['id']])[0] ?? [];

        // if (!checkValue($data, 'id') || $paymentData['payment_status'] == 1) {
        //     return $this->message(400, null, 'Please Verify Payment Details');
        // }

        if (!empty($paymentData)) {
            if ($data) {
                $reqData = $this->memberReqRepo->findById($data['id']);
            }
            if (!$reqData) {
                return $this->message(404, null, 'Member request not found');
            }

            $reqData['mem_req_fk_id'] = $reqData['id'];
            $reqData['member_id'] = generateKey('MEMBER');
            $reqData['approved_by'] = $user_id;
            $inserted = $this->repository->insert(new Member($reqData));

            // Prepare PDF data
            $pdfData = [
                'name' => strtoupper($reqData['first_name'].' '.$reqData['last_name']),
                'member_id' => $reqData['member_id']
            ];
            $payment_data = [
                'payemnt_mode_fk_id' => $data['payment_mode_fk_id'],
                'amount' => $data['amount'],
                'payment_status' => $data['payment_status'],
                'member_fk_id' => $inserted,
                'transaction' => $data['transaction_ref'],
                'transaction_date' => date('Y-m-d')
            ];

            // Generate PDF
            $pdf = $this->pdfGenerate->genByTemp('member_certificate', $pdfData);
            $upload_pdf = FCPATH . $pdf;

            // Only if member data is inserted, continue with further steps
            if ($inserted) {
                // Update member request status
                $this->memberReqRepo->updateById($reqData['id'], ['status' => 1, 'approved_by' => $user_id]);

                // Update payment details
                $this->paymentRepo->updateById($paymentData['id'],  $payment_data);

                // Prepare login data
                $userlogin = [
                    'user_name' => $reqData['email_id'],
                    'email_id' => $reqData['email_id'],
                    'mobile_no' => $reqData['mobile_no'],
                    'password' => md5($password),
                    'fname' => $reqData['first_name'],
                    'lname' => $reqData['last_name'],
                    'member_fk_id' => $reqData['id']
                ];
                $this->login_repository->insert(new UserLogin($userlogin));
                $firstNameLower = strtolower($reqData['first_name']);
                $lastNameLower = strtolower($reqData['last_name']);

                if (preg_match('/^dr./', $firstNameLower)) {
                    $formattedName = ucwords($firstNameLower);
                } else {
                    $formattedName = 'Dr. ' . ucwords( $firstNameLower);
                }

                $emailData = [
                    'first_name' => ucwords($formattedName),
                    'last_name' => ucwords($lastNameLower),
                    'username' => $reqData['email_id'],
                    'password' => $password,
                ];
                $this->email->mapWithContent(3, $emailData, true, $reqData['email_id'], $upload_pdf);
                return $this->message(200, $reqData, 'Member data approved');
            }

            return $this->message(400, null, 'Failed to approve member data');
        }
    }
    public function mail_send($id = false)
    {
        // Generate a random password
        $password = rand(99, 9999);
        $user_id = $this->userData->user_id ?? 1;
    
        // Fetch member request and payment data
        $reqData = $this->repository->findById($id);
        // print_r($reqData);
        if (!$reqData) {
            return $this->message(404, [], 'Request data not found');
        }
        $mem_req = $this->memberReqRepo->findById(['id' => $reqData['mem_req_fk_id']]);
        if (!$mem_req) {
            return $this->message(404, [], 'Member request not found');
        }
        
        // Update the request data with member request info
        $reqData['mem_req_fk_id'] = $mem_req[0]['id'];
        $reqData['approved_by'] = $user_id;
        
        // Generate the PDF certificate
        $pdfData = [
            'name' => strtoupper($reqData['first_name']),
            'member_id' => $reqData['member_id'],
        ];
        $pdf = $this->pdfGenerate->genByTemp('member_certificate', $pdfData);
        $upload_pdf = FCPATH . $pdf;
        
        // Update user password
        $this->login_repository->updateById(['member_req_fk_id' => $id], ['password' => $password]);
        
        // Format name with "Dr." prefix
        $firstName = strtolower($reqData['first_name']);
        $lastName = strtolower($reqData['last_name']);
        $formattedName = stripos($firstName, 'dr.') === 0 ? ucwords($firstName) : 'Dr. ' . ucwords($firstName);
    
        // Prepare email data
        $emailData = [
            'first_name' => $formattedName,
            'last_name' => ucwords($lastName),
            'username' => $reqData['email_id'],
            'password' => $password,
        ];
        
        // Send the email with the PDF attached
        try {
            $this->email->mapWithContent(3, $emailData, true, $reqData['email_id'], $upload_pdf);
            $this->repository->updateById(['id'=>$reqData['id']],['last_sent_mail_on'=>date('Y-m-d')]);
            return $this->message(200, $emailData, 'Mail Sent');
        } catch (Exception $e) {
            return $this->message(500, [], 'Error sending email: ' . $e->getMessage());
        }
    }
    
    public function memberReject($id)
    {
        $memberReqData = $this->memberReqRepo->findById($id);
        if (!$memberReqData) {
            return $this->message(400, null, 'Member request data not found');
        }

        $updatedData = $this->memberReqRepo->updateById($id, ['status' => 0]);
        if (!$updatedData) {
            return $this->message(500, null, 'Failed to update');
        }

        $emailData = [
            'first_name' => $memberReqData['first_name'],
            'last_name' => $memberReqData['last_name'],
        ];

        $this->email->mapWithContent('5', $emailData, true, $memberReqData['email_id']);

        return $this->message(200, $updatedData, 'Successfully rejected');
    }


    public function getAllMember()
    {
        $memberData = $this->repository->findAll();
        return $this->message(200, $memberData, 'Success');
    }


    public function Search($terms, $where)
    {
        if ($this->reqMethod == 'get') {
            $data = [];
            $ftbl = [];
            if ($where) {
                $ftbl = json_decode(utf8_decode(urldecode($where)));
            }
            $terms = $terms == 'null' ? '' : $terms;
            $data = $this->repository->search($terms, $ftbl);
            return $this->message(200, $data, 'Success');
        } else {
            return $this->message(400, null, 'Method Not Allowed');
        }
    }

    public function getList($tblLazy, $active = true)
    {
        $isActive = ($active === 'false') ? false : true;
        if ($tblLazy) {
            $ftbl = json_decode((urldecode($tblLazy)));
        }
        $data = $this->repository->findAllPagination($ftbl, $isActive);
        return $this->message(200, $data, 'Success');
    }

    public function get($id = false)
    {
        $id = $id ? $id : 0;
        $data = [];
        if ($id === 0) {
            $data = $this->repository->findAll();
        } else {
            $data = $this->repository->findById($id);
            $data['payment'] = $this->paymentRepo->findById(['member_fk_id' => $id])[0] ?? [];
        }
        return $this->message(200,  $data,'');
    }

    public function password()
    {
        echo md5('admin$123');
    }
    public function email_send()
    {
        $filePath = 'https://iaoi.in/uploads/book.txt';
        if (!file_exists($filePath)) {
            echo "File not found!";
            return;
        }
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            while (($email = fgets($handle)) !== FALSE) {
                $email = trim($email);
                if (!empty($email)) {
                    $this->email->mapWithContent(6, false, true, $email, []);
                    echo "Processed email: " . $email . "\n";
                }
            }
            fclose($handle);
        } else {
            echo "Unable to open the file!";
        }
    }
    public function decryptMd5($hash)
    {
        // Initialize cURL session
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.hash-decrypt.io/v1/md5/" . $hash, // The API endpoint
            CURLOPT_RETURNTRANSFER => true, // Return the result as a string
            CURLOPT_TIMEOUT => 30, // Set timeout
            CURLOPT_HTTPGET => true, // Use GET method
        ]);

        // Execute the request
        $response = curl_exec($curl);

        // Check for errors
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return json_encode(["error" => $error_msg]);
        }

        // Close cURL session
        curl_close($curl);

        // Return the response
        return $this->response->setJSON(json_decode($response, true));
    }
    public function findMember($member_id)
    {
        $data = $this->repository->findAllByWhere(['member_id' => $member_id]);
        
        if ($data) {
            return $this->message(200, $data, 'Member Retrieved Successfully');
        }
    
        return $this->message(404, null, 'Member Not Found');
    }
    
}
