<?php
class Router {
    protected $routes = [];

    // Đăng ký đường dẫn
    public function add($url, $controllerAction) {
        $this->routes[$url] = $controllerAction;
    }

    // Xử lý yêu cầu
    public function dispatch($url) {
        // Xóa dấu gạch chéo ở cuối (nếu có)
        $url = rtrim($url, '/');
        if ($url == '') $url = '/';

        if (array_key_exists($url, $this->routes)) {
            $handler = explode('@', $this->routes[$url]);
            $controllerName = $handler[0];
            $action = $handler[1];

            // SỬA TẠI ĐÂY: Sử dụng __DIR__ để xác định đường dẫn tuyệt đối từ thư mục core
            $controllerFile = __DIR__ . "/../app/controllers/" . $controllerName . ".php";

            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $action)) {
                        $controller->$action();
                    } else {
                        die("Lỗi: Hàm <b>{$action}</b> không tồn tại trong class <b>{$controllerName}</b>");
                    }
                } else {
                    die("Lỗi: Class <b>{$controllerName}</b> không tìm thấy trong file controller.");
                }
            } else {
                die("Lỗi: Không tìm thấy file Controller tại đường dẫn: <br> <b>{$controllerFile}</b>");
            }
        } else {
            die("404 - Trang không tồn tại!");
        }
    }
}