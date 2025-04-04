<?php

namespace Core\Controllers;

use App\Infrastructure\Persistence\Events\SQLEvent_locationRepository;
use App\Infrastructure\Persistence\Member\SQLMember_requestRepository;
use Config\Services;
use Core\Domain\Exception\RecordNotFoundException;
use Core\Domain\Role\User_role;
use Core\Domain\User\UserLogin;
use Core\Domain\User\UserLoginRepository;
use Core\Domain\User\UserRepository;
use Core\Infrastructure\Persistence\Role\SQLUser_roleRepository;
use Core\Libraries\EmailConetentGenerator;
use Core\Models\Logs\LogsModel;
use Mail\Libraries\Email;

class UsersController extends BaseController
{

	private $repository;
	private $role_repository;
	private $login_repository;
	private $logsModel;
	private $member_request;
	public function __construct()
	{
		$this->initializeFunction();
		$this->repository = Services::UserRepository();
		$this->login_repository = Services::UserLoginRepository();
		$this->role_repository = new SQLUser_roleRepository();
		$this->logsModel = new LogsModel();
		$this->member_request = new SQLMember_requestRepository();
	}

	public function index()
	{

		//$this->UserListing();
	}

	public function UserListing($tblLazy, $activeUser = true)
	{
		$ftbl;
		$isActive = ($activeUser === 'false') ? false : true;

		if ($tblLazy) {
			$ftbl = json_decode(utf8_decode(urldecode($tblLazy)));
		}
		$data = $this->login_repository->findAllUserPagination($ftbl, $isActive);
		return $this->message(200, $data);
	}

	public function addUser()
	{
		$userRole = array();
		$data = $this->getDataFromUrl('json');
		$userRole = $data['role'] ?? [];
		$userLogin = new UserLogin($data);
		try {
			$userId = isset($data['user_id']) ? $data['user_id'] : '';
			if (empty($userId)) {
				$userId          = $this->login_repository->addNewUser($userLogin);
				$data['user_id'] = (int) $userId;
			} else if ($userId) {
				$userLogin = $userLogin->toRawArray();
				if (checkValue($data, 'password')) {
					$updateData = ['password' => md5($data['password'])];
				} else {
					$updateData          = $this->login_repository->findAllByWhere(['user_id' => $userId])[0] ?? [];
				}
				$updateData['fname'] = $data['fname'] ?? '';
				$updateData['lname'] = $data['lname'] ?? '';
				$updateData['email_id'] = $data['email_id'] ?? '';
				$this->login_repository->update(array('user_id' => $userId), $updateData);
				if ($userId) {
					$this->role_repository->deleteOfId($userId);
					foreach ($userRole as $row) {
						$row['user_id'] = $userId;
						foreach ($row as $k => $value) {
							$row[$k] = strlen($row[$k]) ? $row[$k] : NULL;
						}
						$this->role_repository->insert(new User_role($row));
					}
				}
			}
		} catch (RecordNotFoundException $e) {
			return $this->message(404, $e->getMessage());
		}
		return $this->message(200, $data);
	}

	public function isEmailUnique($email)
	{
		$data = $this->member_request->findAllByWhere(['user_login.email_id' => $email])[0] ?? [];
		if (!empty($data)) {
			$data = false;
		} else {
			$data = true;
		}
		return $this->message(200, $data);
	}

	public function isUnique($field, $email)
	{
		$result = $this->login_repository->findUserUnique($field, $email);
		return $this->message(200, $result);
	}

	public function getUserDetail($id)
	{
		if (strtolower($this->reqMethod) == 'get') {
			$data = $this->login_repository->findById($id);
			$roleData = $this->role_repository->findByRoleId($id);
			$data['role'] = $roleData;
			return $this->message(200, $data);
		} else {
			return $this->message(400, null, 'Method Not Allowed');
		}
	}

	public function create()
	{

		$token = $this->token_get();
		try {
			//    $album = $this->repository->findAlbumOfId($id);
			$data = $this->repository->findUserOfId(1);
		} catch (RecordNotFoundException $e) {
			//throw new PageNotFoundException($e->getMessage());
			return $this->message(404, $e->getMessage());
		}
		return $this->message(200, $data);
	}

	public function deleteUser($id)
	{
		if ($id) {
			if ($this->login_repository->deleteOfId($id)) {
				return $this->message(200, $id, 'Success');
			} else {
				return $this->message(400, null, 'Failed to Delete');
			}
		}
	}

	public function makeActive($id)
	{
		if ($id) {
			if ($this->login_repository->update(array('user_id' => $id), array('deleted_at' => null))) {
				return $this->message(200, $id, 'Success');
			} else {
				return $this->message(400, null, 'Failed to Inactive');
			}
		}
	}

	public function show404()
	{
		return $this->message(400, null, 'Method Not Allowed');
	}
	public function sendMail()
	{
		$req = $this->getDataFromUrl('json');
		$res = false;
		$email = new EmailConetentGenerator();
		if (checkValue($req, 'event_location_code')) {
			$res = $email->genContentSentEmail(1, $req);
		} elseif (checkValue($req, 'alliance_code')) {
			$res = $email->genContentSentEmail(2, $req);
		} else {
			return $this->message(400, null, 'Distribution Center OR Alliance Code Required');
		}

		return $this->message($res ? 200 : 400, null, $res ? "Mail Send SuccessFull" : "Failed");
	}
}
