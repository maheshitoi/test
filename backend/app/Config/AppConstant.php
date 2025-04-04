<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AppConstant extends BaseConfig
{

	public $password_encrept_key = 'abcdefghtjklmnopqrstuvwxyz1234567890';
	public $jwt_key = 'ddfgdjgjiodniduo';
	public $expireTimeInteravel = 4 * 3600; // 4h
	public $paginationPerPage = 15;
	public $app_name = ['ADMIN_APP_WEB', 'MOBILE_APP_STAFF', 'MOBILE_APP_SPONSOR', 'API', 'CLIENT_SERVER'];
	public $trustLogoUploadPath = 'uploads/trust/logo';
	public $trustWaterMarkUploadPath = 'uploads/trust/watermark';
	public $e_learningImgPath = 'uploads/e_learning/img';
	public $eventImgPath = 'uploads/event/';
	public $speaker_profilePath = 'uploads/speaker/';
	public $eventMultiImgPath = 'uploads/event/';
	public $bookImgPath = 'uploads/book/img';
	public $memberprofileImgPath = 'uploads/member/profile';
	public $paymentDocument = 'uploads/payment/doc';
	public $uploadPath = 'uploads/';
	public $assetPath = 'uploads/asset/';
	public $accountSettlementUploadPath = 'uploads/account/settlement/';
	public $activityPath = 'uploads/activity/';
	public $serverPathUpload;
	public $TEM_PATH = 'uploads/temp/';
	public $TOKEN_REMOTE_ACCESS = 'YMsIVVsthyRIknXFpOnqkxnqqOYxW6nv_duANIEdB_g';
	public $LOG_STATUS = array('ERROR' => '0', 'INFO' => '1', 'CRITICAL_ERROR' => '4', 'ALERT' => '3', 'WARNING' => '2');
	public $ERROR_LOG_ENABLE = true;
	public $INFO_LOG_ENABLE = true;
	public $WARNING_LOG_ENABLE = true;
	public $OTP_TRIES = 3;
	public $OTP_VALID = 5; // 5mints
	public $mobileAppPath = 'uploads/app/apk';
	public $KEY_ID='rzp_test_3eVIB91QVNpqhz';
	public $KEY_SECRET='f97KYA8esSWGqy1z1rsGkmGI';
	// public $KEY_ID=ENVIRONMENT=='development'?'rzp_live_cAXS7ZwY8q1RGY':'rzp_test_3eVIB91QVNpqhz';	
	// public $KEY_SECRET=ENVIRONMENT=='development'?'PYUHgSRA6AuT7DI66zM6LH4i':'f97KYA8esSWGqy1z1rsGkmGI';

	// payroll
	public $staffLeaveUploadPath = 'uploads/staff/leave/';
	public $headConstValue = ['basic', 'is_epf', 'is_esi', 'saving', 'allowance', 'loan', 'slot', 'increment', 'is_welfare', 'hra_percentage_value', 'deduction', 'year_of_experience'];
}
define('APPKEY', '');
define('AUTHKEY', '');

