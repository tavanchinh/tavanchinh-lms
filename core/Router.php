<?php
class Router {
    protected $routes = [];

    // Đăng ký đường dẫn (Nâng cấp để hỗ trợ {slug}, {id})
    public function add($url, $controllerAction) {
        // Chuyển đổi các tham số động {name} thành Regex
        // Ví dụ: watch/{slug}/{id} -> watch/(?P<slug>[a-z0-9-]+)/(?P<id>[0-9]+)
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $url);
        $route = "#^" . trim($route, '/') . "$#";
        
        $this->routes[$route] = $controllerAction;
    }

    // Xử lý yêu cầu
    public function dispatch($url) {
        // Chuẩn hóa URL: Xóa dấu gạch chéo ở đầu và cuối
        $url = trim($url, '/');
        if ($url == '') $url = '/';

        $found = false;

        // Duyệt qua danh sách các Route đã đăng ký dưới dạng Regex
        foreach ($this->routes as $route => $controllerAction) {
            if (preg_match($route, $url, $matches)) {
                $found = true;
                
                $handler = explode('@', $controllerAction);
                $controllerName = $handler[0];
                $action = $handler[1];

                // Xác định đường dẫn file Controller
                $controllerFile = __DIR__ . "/../app/controllers/" . $controllerName . ".php";

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        if (method_exists($controller, $action)) {
                            
                            // Lấy ra các tham số từ URL (như slug, id)
                            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                            
                            // Gọi hàm và truyền tham số vào (SỬA Ở ĐÂY)
                            call_user_func_array([$controller, $action], $params);
                            return;

                        } else {
                            die("Lỗi: Hàm <b>{$action}</b> không tồn tại trong class <b>{$controllerName}</b>");
                        }
                    } else {
                        die("Lỗi: Class <b>{$controllerName}</b> không tìm thấy.");
                    }
                } else {
                    die("Lỗi: Không tìm thấy file Controller tại: <b>{$controllerFile}</b>");
                }
            }
        }

        if (!$found) {
            header("HTTP/1.0 404 Not Found");
            die("404 - Trang không tồn tại!");
        }
    }
}