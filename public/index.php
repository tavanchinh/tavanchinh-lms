<?php
require_once '../core/Router.php';
require_once '../core/Database.php'; // Đảm bảo đã có file này

$router = new Router();

// Định nghĩa các Route (Đường dẫn => Controller@Hàm)
$router->add('/', 'AuthController@showLogin');
$router->add('login', 'AuthController@showLogin');
$router->add('login-process', 'AuthController@login');
$router->add('logout', 'AuthController@logout');

$router->add('dashboard', 'AuthController@showDashboard'); // Bạn cần tạo hàm này

$router->add('assign-course', 'CourseController@showAssignForm');
$router->add('assign-process', 'CourseController@processAssign');

// Thêm dòng này vào phần đăng ký route
$router->add('dashboard', 'AdminController@index');
$router->add('assign-course', 'AdminController@assignCourse');

$router->add('admin/courses/create', 'AdminController@createCourse');
$router->add('admin/courses/store', 'AdminController@storeCourse');
$router->add('store-assign', 'AdminController@storeAssign');
$router->add('admin/students', 'AdminController@students');
$router->add('admin/students/store', 'AdminController@storeStudent');

// Lấy URL hiện tại từ biến $_GET['url'] do .htaccess tạo ra
$url = isset($_GET['url']) ? $_GET['url'] : '/';
$router->dispatch($url);