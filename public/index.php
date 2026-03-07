<?php
require_once '../core/Router.php';
require_once '../core/Database.php';

$router = new Router();

// ==========================================
// 1. AUTHENTICATION (ĐĂNG NHẬP / ĐĂNG XUẤT)
// ==========================================
$router->add('/', 'AuthController@showLogin');
$router->add('dang-nhap', 'AuthController@showLogin');
$router->add('login-process', 'AuthController@login');
$router->add('dang-xuat', 'AuthController@logout');

// ==========================================
// 2. ADMIN DASHBOARD & HỌC VIÊN
// ==========================================
// Trang chủ quản trị (Thống kê, danh sách nhanh)
$router->add('dashboard', 'AdminController@index');

// Quản lý học viên
$router->add('admin/students', 'AdminController@students');
$router->add('admin/students/store', 'AdminController@storeStudent');
$router->add('admin/students/get-courses/{id}', 'AdminController@getCourses');
$router->add('admin/students/update-ajax', 'AdminController@updateStudentAjax');

// ==========================================
// 3. QUẢN LÝ KHÓA HỌC (COURSE CONTROLLER)
// ==========================================
$router->add('admin/courses', 'CourseController@index');
$router->add('admin/courses/create', 'CourseController@create');
$router->add('admin/courses/store', 'CourseController@storeCourse');
$router->add('admin/courses/edit/{id}', 'CourseController@edit');
$router->add('admin/courses/update/{id}', 'CourseController@update');

// Gán khóa học cho học viên
$router->add('assign-course', 'CourseController@showAssignForm');
$router->add('assign-process', 'CourseController@processAssign');
$router->add('store-assign', 'CourseController@storeAssign');

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

// ==========================================
// 5. FRONTEND (GIAO DIỆN HỌC VIÊN)
// ==========================================
$router->add('my-courses', 'CourseController@myCourses');
$router->add('watch/{slug}/{id}', 'CourseController@watch');
$router->add('course/stream/{id}', 'CourseController@stream');

// ==========================================
// THỰC THI ROUTER
// ==========================================
$url = isset($_GET['url']) ? $_GET['url'] : '/';
$router->dispatch($url);