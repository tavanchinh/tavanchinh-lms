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
        $this->view('frontend/home/index', [
            'courses' => $courses,
            'meta_description' => 'Làm chủ quy trình CNC sau 15 buổi.Chủ động hơn trong việc vẽ và ra file CNC. Đồng hành trọn đời cùng đội ngũ chuyên nghiệp, yên tâm sản xuất.'
            ]);
    }

    public function documentation() {
        
        

        // Truyền dữ liệu vào view home
        $this->view('frontend/home/documentation', [
            'meta_description' => 'Tài liệu hướng dẫn sử dụng hệ thống quản lý học viên và khóa học CNC.'
            ]);
    }
} 