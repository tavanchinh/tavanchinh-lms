<?php

require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';

class FinanceController extends BaseController {

    public function __construct() {
        $this->checkLogin();
        //$this->checkRole(['admin']);
    }


    public function create() {
        $this->checkRole(['admin']);
        $userModel = new UserModel();

        // Lấy danh sách danh mục thu
        $incomeCategories = $userModel->query("SELECT * FROM finance_categories WHERE type = 'income'")->fetchAll();
        
        // Lấy danh sách danh mục chi
        $expenseCategories = $userModel->query("SELECT * FROM finance_categories WHERE type = 'expense'")->fetchAll();

        // Truyền dữ liệu vào view admin
        $this->view('admin/finance/create', [
            'incomeCategories'  => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'meta_title'        => 'Thêm giao dịch tài chính'
        ]);
    }

    public function addTransaction() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new UserModel(); // Hoặc FinanceModel tùy anh đặt tên
            
            // Làm sạch dữ liệu số (loại bỏ dấu phẩy nếu anh dùng định dạng tiền)
            $amount = (int)preg_replace('/[^0-9.]/', '', $_POST['amount']);
            $categoryId = (int)$_POST['category_id'];
            $transactionDate = $_POST['transaction_date'];
            $note = htmlspecialchars($_POST['note']);
            $paymentMethod = $_POST['payment_method'];

            $sql = "INSERT INTO finance_transactions (category_id, amount, payment_method, note, transaction_date) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $params = [$categoryId, $amount, $paymentMethod, $note, $transactionDate];
            
            if ($userModel->query($sql, $params)) {
                // Chuyển hướng về trang danh sách với thông báo thành công
                header("Location: /admin/finance?success=1");
                exit;
            }
        }
    }


    public function index() {
        $userModel = new UserModel();

        // 1. Lấy dữ liệu lọc từ URL (mặc định là tháng hiện tại)
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate   = $_GET['to_date'] ?? date('Y-m-t');
        $type     = $_GET['type'] ?? 'all';

        // KHỞI TẠO GIÁ TRỊ MẶC ĐỊNH (TRỐNG)
        $transactions = [];
        $totalIncome = 0;
        $totalExpense = 0;

        // 2. CHỈ XỬ LÝ TRUY VẤN NẾU LÀ ADMIN
        // Nếu không phải admin, bỏ qua toàn bộ logic SQL bên dưới
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            
            // Xây dựng điều kiện WHERE cho SQL
            $where = "WHERE ft.transaction_date BETWEEN ? AND ?";
            $params = [$fromDate, $toDate];

            if ($type !== 'all') {
                $where .= " AND fc.type = ?";
                $params[] = $type;
            }

            // 3. Tính toán tổng Thu/Chi dựa trên bộ lọc
            $incomeSql = "SELECT SUM(amount) as total FROM finance_transactions ft 
                        JOIN finance_categories fc ON ft.category_id = fc.id 
                        $where AND fc.type = 'income'";
            $totalIncome = $userModel->query($incomeSql, $params)->fetch()['total'] ?? 0;

            $expenseSql = "SELECT SUM(amount) as total FROM finance_transactions ft 
                        JOIN finance_categories fc ON ft.category_id = fc.id 
                        $where AND fc.type = 'expense'";
            $totalExpense = $userModel->query($expenseSql, $params)->fetch()['total'] ?? 0;

            // 4. Lấy danh sách giao dịch theo bộ lọc
            $transactions = $userModel->query("
                SELECT ft.*, fc.name as category_name, fc.type 
                FROM finance_transactions ft 
                JOIN finance_categories fc ON ft.category_id = fc.id 
                $where 
                ORDER BY ft.transaction_date DESC", $params)->fetchAll();
        }

        // 5. Truyền dữ liệu ra View
        // Nếu không phải Admin, View sẽ nhận được mảng transactions trống và các tổng bằng 0
        $this->view('admin/finance/index', [
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'profit'       => $totalIncome - $totalExpense,
            'transactions' => $transactions,
            'fromDate'     => $fromDate,
            'toDate'       => $toDate,
            'currentType'  => $type,
            'meta_title'   => 'Báo cáo tài chính chi tiết'
        ]);
    }

} // Kết thúc class (Đảm bảo không có dấu ; thừa sau dấu ngoặc này)