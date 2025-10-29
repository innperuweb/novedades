<?php

require_once APP_PATH . '/controllers/BaseController.php';

class BlogController extends BaseController
{
    public function index(): void
    {
        $this->render('blog');
    }

    public function ver(): void
    {
        $this->render('ver_blog');
    }
}
