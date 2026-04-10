<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../core/Router.php';
require_once '../core/Database.php';

$router = new Router();

// ==========================================
// 1. AUTHENTICATION (ĐĂNG NHẬP / ĐĂNG XUẤT)
// ==========================================
$router->add('/', 'AuthController@showLogin');
$router->add('dang-nhap', 'AuthController@showLogin');
$router->add('dang-ky', 'AuthController@register');
$router->add('register-process', 'AuthController@registerProcess');
$router->add('login-process', 'AuthController@login');
$router->add('dang-xuat', 'AuthController@logout');


// ==========================================
// 2. ADMIN DASHBOARD & HỌC VIÊN
// ==========================================
// Trang chủ quản trị (Thống kê, danh sách nhanh)
$router->add('admin', 'AdminController@index');

// Quản lý học viên / nhân viên / user 
$router->add('admin/accounts', 'AdminController@accounts');
$router->add('admin/students', 'AdminController@students');
$router->add('admin/students/store', 'AdminController@storeStudent');
$router->add('admin/students/get-courses/{id}', 'AdminController@getCourses');
$router->add('admin/students/update-ajax', 'AdminController@updateStudentAjax');
$router->add('admin/staff', 'AdminController@staff');
$router->add('admin/users/delete/{id}', 'AdminController@deleteUser');
$router->add('admin/users/logs/{id}', 'AdminController@userLogs');

// ==========================================
// 3. QUẢN LÝ KHÓA HỌC (COURSE CONTROLLER)
// ==========================================
$router->add('admin/courses', 'CourseController@index');
$router->add('admin/courses/create', 'CourseController@create');
$router->add('admin/courses/store', 'CourseController@storeCourse');
$router->add('admin/courses/edit/{id}', 'CourseController@edit');
$router->add('admin/courses/update/{id}', 'CourseController@update');
$router->add('admin/courses/delete-doc/{id}', 'CourseController@deleteDoc');

// Gán khóa học cho học viên
$router->add('assign-course', 'CourseController@showAssignForm');
$router->add('assign-process', 'CourseController@processAssign');
$router->add('store-assign', 'CourseController@storeAssign');



// ==========================================
// 3. QUẢN LÝ TÀI CHÍNH (FINANCE CONTROLLER)
// ==========================================
$router->add('admin/finance', 'FinanceController@index');
$router->add('admin/finance/create', 'FinanceController@create');
$router->add('admin/finance/store', 'FinanceController@addTransaction');
$router->add('admin/finance/transactions', 'FinanceController@transactions');   

// ==========================================
// 4. QUẢN LÝ CHƯƠNG & BÀI HỌC (CHAPTER & LESSON)
// ==========================================
// Chương học
$router->add('admin/chapter/store', 'ChapterController@store');
$router->add('admin/chapter/update-ajax', 'ChapterController@updateAjax');

// Bài học
$router->add('admin/lesson/store', 'LessonController@store');
$router->add('admin/lesson/delete/{id}', 'LessonController@delete');
$router->add('admin/lesson/update-ajax', 'LessonController@updateAjax');
$router->add('admin/study', 'LessonController@adminStudy');
$router->add('admin/study/fast-complete', 'LessonController@adminFastComplete');

// ==========================================
// 5. FRONTEND (GIAO DIỆN HỌC VIÊN)
// ==========================================
$router->add('trang-chu', 'HomeController@index'); 
$router->add('', 'HomeController@index');
$router->add('tai-lieu', 'HomeController@documentation'); 
$router->add('trang-ca-nhan', 'UserController@index');
$router->add('khoa-hoc-cua-toi', 'UserController@index');
$router->add('cap-nhat-thong-tin', 'UserController@update');
$router->add('/thanh-toan', 'PaymentController@createPayment');
$router->add('/kiem-tra-trang-thai-don-hang', 'PaymentController@checkStatus');
$router->add('/user/keep-alive', 'UserController@keepAlive');
// Cấu hình Route cho chức năng Quên mật khẩu
$router->add('/quen-mat-khau', 'UserController@forgotPassword');
$router->add('/forgot-password-process', 'UserController@sendResetLink');
$router->add('/reset-password', 'UserController@resetPassword');
$router->add('/reset-password-process', 'UserController@updatePasswordAfterReset');

//$router->add('/test-email', 'PaymentController@testEmail'); // Route thử nghiệm gửi email
$router->add('/{slug}', 'CourseController@detail'); // Ví dụ: /khoa-hoc-lap-trinh


$router->add('learning/{slug}', 'CourseController@learning');
$router->add('course/stream/{id}', 'CourseController@stream');
$router->add('course/getStreamToken/{id}', 'CourseController@getStreamToken');
$router->add('lesson/complete', 'LessonController@completeLesson');


// ==========================================
// THỰC THI ROUTER
// ==========================================

// Lấy URL từ biến $_GET, nếu không có thì mặc định là chuỗi rỗng
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Cắt bỏ dấu gạch chéo ở đầu và cuối (ví dụ: "/trang-chu/" thành "trang-chu")
$url = trim($url, '/');

// Quan trọng: Nếu sau khi trim mà URL là "/" hoặc rỗng, hãy chắc chắn nó là ''
if ($url === '/') {
    $url = '';
}
//die($url);
$router->dispatch($url);