<?php

require_once APP_PATH . '/controllers/BaseController.php';

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('index');
    }
}
