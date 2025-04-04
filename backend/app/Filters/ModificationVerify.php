<?php namespace Core\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ModificationVerify implements FilterInterface {
	/**
	 * Do whatever processing this filter needs to do.
	 * By default it should not return anything during
	 * normal execution. However, when an abnormal state
	 * is found, it should return an instance of
	 * CodeIgniter\HTTP\Response. If it does, script
	 * execution will end and that Response will be
	 * sent back to the client, allowing for error pages,
	 * redirects, etc.
	 *
	 * @param RequestInterface|\CodeIgniter\HTTP\IncomingRequest $request
	 *
	 * @return mixed
	 */
	public function before(RequestInterface $request, $arguments = null) {
		$message = array('statusCode' => 400, 'result' => '');
		$data = $request->getJSON(true);
		if (checkValue($data, 'modify_request_id')) {
			$message['message'] = checkRequestActive($data['modify_request_id'] ?? '');
			if ((int) $message['message'] <= 0) {
				return Services::response()->setStatusCode(400)->setJSON($message);
			} else {
				$request->modify_created_by = $message['message'];
				$request->modify_request_id = $data['modify_request_id'];
			}
		}
		unset($data);
	}
	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
		$data = $request->getJSON(true);
		if (checkValue($data, 'modify_request_id') && $response->getStatusCode() == 200) {
			$body = $response->getBody(true);
			if (gettype($body) == 'string') {
				$body = json_decode($body);
				// echo gettype($body->user_id);
				if (isset($body->user_id)) {
					updateApprove($data['modify_request_id'], $body->user_id);
				}
				if (isset($body->user_id)) {
					unset($body->user_id);
				}
				if (isset($body->modify_request_id)) {
					unset($body->modify_request_id);
				}
				$response->setJSON($body);
			}
		}
		return $response;
	}
}