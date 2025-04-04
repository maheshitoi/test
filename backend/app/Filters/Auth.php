<?php namespace Core\Filters;

require_once APPPATH . 'Auth/authorization.php';
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class Auth implements FilterInterface
{
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
    public function before(RequestInterface $request, $arguments = null)
    {
        // $message = array(
        //     'statusCode' => 401,
        //     'message'    => 'illegal Access',
        //     'result'     => '');
        // if (ENVIRONMENT == 'production') {
        //     if (!\AUTHORIZATION::checkAuthorized()) {
        //         return Services::response()->setStatusCode(401)->setJSON($message);
        //     }
        // }
    }

    //--------------------------------------------------------------------

    /**
     * We don't have anything to do here.
     *
     * @param RequestInterface|\CodeIgniter\HTTP\IncomingRequest $request
     * @param ResponseInterface|\CodeIgniter\HTTP\Response       $response
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
        //->setHeader('Access-Control-Allow-Origin', '*')
        // ->setHeader("Access-Control-Max-Age", "600")
        //->setHeader('Access-Control-Allow-Headers', '*')
        //->setHeader('Cache-Control', 'no-cache')
        //->setHeader('Access-Control-Allow-Methods', 'GET,POST, OPTIONS, PUT, DELETE, PATCH');
    }

    //--------------------------------------------------------------------
}
