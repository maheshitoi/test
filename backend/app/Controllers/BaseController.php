<?php
namespace Core\Controllers;
use CodeIgniter\API\ResponseTrait;
require_once APPPATH . 'Auth/jwt.php';
require_once APPPATH . 'Auth/authorization.php';

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 * class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;
use Config\Services;
//require_once(APPPATH.'Helpers/jwt_helper.php');

class BaseController extends Controller {

	use ResponseTrait;
	private $timestamp;
	private $email;
	private $user_id;
	protected $reqMethod;
	protected $userData;
	protected $sessionData;
/**
 * An array of helpers to be loaded automatically upon
 * class instantiation. These helpers will be available
 * to all other controllers that extend BaseController.
 *
 * @var array
 */

	protected $helpers = ['Utility', 'url'];

/**
 * Constructor.
 */
	public $AuthObj;
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
		$AuthObj = new \AUTHORIZATION(\Config\Services::request());
		// parent::getReqMethod();
		helper('Core/helpers/Log');
		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();
	}

	public function initializeFunction() {
		$this->getReqMethod();
		$this->getTokenData();
		$this->sessionData = \Config\Services::session(); 
	}

	public function getReqMethod() {
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			$this->reqMethod = strtolower($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
			return strtolower($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']);
		} else {
			$this->reqMethod = strtolower($_SERVER['REQUEST_METHOD']);
			return strtolower($_SERVER['REQUEST_METHOD']);
		}

	}
	public function getTokenData() {
		$this->userData = \AUTHORIZATION::getTokenData();
	}

	public function message($status = 200, $data = null, $message = "") {
		$message = array(
			'statusCode' => $status,
			'message' => $message,
			'result' => $data);
		$d = $this->getDataFromUrl('json');
		if (!empty($d) && checkValue($d, 'modify_request_id')) {
			$message['user_id'] = $this->userData->user_id ?? 1;
			$message['modify_request_id'] = $d['modify_request_id'];
		}
		// $message = gzencode($content, 9);
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			return $this->response->setStatusCode(200)->setJSON($message);
		} else {
			return $this->response->setStatusCode($status)->setJSON($message);
		}

	}

	public function createToken($tokenData) {
		$CI = new \Config\AppConstant();
		$tokenData['expire'] = time() + ($CI->expireTimeInteravel);
		$tokenData['environment'] = ENVIRONMENT;
		$tokenData['create_at'] = time();
		return \AUTHORIZATION::generateToken($tokenData);
	}

	public function getDataFromUrl($methods, $variableName = false) {
		$request = \Config\Services::request();
		$userId = isset($request->modify_create_by) ? $request->modify_created_by : ($this->userData->user_id) ?? '';
		switch ($methods) {
		case 'json':
			$data = $request->getJSON(true);
			if (checkValue($data, 'id')) {
				$data['last_modify_by'] = $userId;
			} else {
				$data['created_by'] = $userId;
			}
			if (checkValue($data, 'modify_request_id')) {
				$data['modify_request'] = 0;
				$data['last_approved_by'] = $this->userData->user_id ?? 1;
			}
			break;
		case 'get':
			$data = $request->getGet();
			break;
		default:
			$data = $request->getVar($variableName);
			break;
		}

		return $data;
	}

}
