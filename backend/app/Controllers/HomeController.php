<?php

namespace Core\Controllers;

use Core\Controllers\DMLController;
use CodeIgniter\Controller;

class HomeController extends BaseController
{
    use DMLController;
    public function __construct()
    {
        helper(['view']);
        $this->initializeFunction();
    }
    public function home()
    {
        return view('include/header.php')
       . view('pages/become-a-member');
    }
    public function logout()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_destroy();
        return 
            view('pages/become-a-member');
    }
}