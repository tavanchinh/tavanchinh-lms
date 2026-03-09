<?php

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class HomeController extends BaseController {

    public function __construct() {
        //$this->checkLogin();
        //$this->checkRole(['admin', 'staff']);
    }

    public function index() {
        
        $courseModel = new CourseModel();
        $courses = $courseModel->getAllActive();

        // Truyền dữ liệu vào view home
        $this->view('frontend/home/index', ['courses' => $courses]);
    }
} 