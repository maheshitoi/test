<?php

namespace Core\Controllers;

class Home extends BaseController
{
    public function __construct()
    {
        $this->initializeFunction();
    }
    public function index()
    {
        return 
             view('pages/become-a-member');
    }

    public function login()
    {
        return view('pages/login');
    }
    public function login_view()
    {
        return view('pages/login_view');
    }
    public function logout()
    {

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_destroy();
        return view('pages/become-a-member');
    }
    
}
