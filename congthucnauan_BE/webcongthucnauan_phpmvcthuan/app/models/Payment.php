<?php
class Payment {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Add forwarding methods to the Database object
    public function query($sql) {
        return $this->db->query($sql);
    }

    public function bind($param, $value, $type = null) {
        return $this->db->bind($param, $value, $type);
    }

    public function execute() {
        return $this->db->execute();
    }

    public function resultSet() {
        return $this->db->resultSet();
    }

    public function single() {
        return $this->db->single();
    }

    public function rowCount() {
        return $this->db->rowCount();
    }

    // Add payment record
    public function addPayment($data) {
        $this->db->query('INSERT INTO payments (user_id, amount, payment_method, status, transaction_id) 
                        VALUES (:user_id, :amount, :payment_method, :status, :transaction_id)');
        // Bind values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':payment_method', $data['payment_method']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':transaction_id', $data['transaction_id']);

        // Execute
        if($this->db->execute()) {
            return $this->db->lastInsertId(); // Sửa từ $this->db->dbh->lastInsertId() thành $this->db->lastInsertId()
        } else {
            return false;
        }
    }

    // Update payment status
    public function updatePaymentStatus($id, $status) {
        $this->db->query('UPDATE payments SET status = :status WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get payment by ID
    public function getPaymentById($id) {
        $this->db->query('SELECT * FROM payments WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Get payments by user
    public function getPaymentsByUser($user_id) {
        $this->db->query('SELECT * FROM payments WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $user_id);

        $results = $this->db->resultSet();

        return $results;
    }

    // Get all payments (for admin)
    public function getAllPayments() {
        $this->db->query('SELECT payments.*, users.name as user_name 
                        FROM payments 
                        INNER JOIN users ON payments.user_id = users.id 
                        ORDER BY payments.created_at DESC');
        
        $results = $this->db->resultSet();

        return $results;
    }

    // Lấy tổng doanh thu
    public function getTotalRevenue() {
        $this->db->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'");
        $row = $this->db->single();
        return $row->total ?? 0;
    }
    
    // Get revenue by month for dashboard
    public function getRevenueByMonth($yearMonth) {
        $this->db->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed' AND DATE_FORMAT(created_at, '%Y-%m') = :yearMonth");
        $this->db->bind(':yearMonth', $yearMonth);
        $row = $this->db->single();
        return $row->total ?? 0;
    }
    
    // Update payment status by transaction ID
    public function updatePaymentStatusByTransactionId($transaction_id, $status, $gateway_transaction_id = null) {
        if($gateway_transaction_id) {
            $this->db->query('UPDATE payments SET status = :status, transaction_id = :gateway_transaction_id, updated_at = NOW() WHERE transaction_id = :transaction_id');
            $this->db->bind(':gateway_transaction_id', $gateway_transaction_id);
        } else {
            $this->db->query('UPDATE payments SET status = :status, updated_at = NOW() WHERE transaction_id = :transaction_id');
        }
        
        $this->db->bind(':status', $status);
        $this->db->bind(':transaction_id', $transaction_id);
        
        return $this->db->execute();
    }
    
    // Get payment by transaction ID
    public function getPaymentByTransactionId($transaction_id) {
        $this->db->query('SELECT * FROM payments WHERE transaction_id = :transaction_id');
        $this->db->bind(':transaction_id', $transaction_id);
        return $this->db->single();
    }
    
    // Get payment details for invoice (with user and courses info)
    public function getPaymentForInvoice($payment_id) {
        $this->db->query('SELECT 
                            payments.*,
                            users.name as user_name,
                            users.email as user_email
                        FROM payments 
                        INNER JOIN users ON payments.user_id = users.id 
                        WHERE payments.id = :payment_id');
        $this->db->bind(':payment_id', $payment_id);
        $payment = $this->db->single();
        
        if($payment) {
            // Get courses enrolled with this payment
            $this->db->query('SELECT 
                                courses.title,
                                courses.price,
                                course_enrollments.enrollment_date
                            FROM course_enrollments
                            INNER JOIN courses ON course_enrollments.course_id = courses.id
                            WHERE course_enrollments.payment_id = :payment_id');
            $this->db->bind(':payment_id', $payment_id);
            $payment->courses = $this->db->resultSet();
        }
        
        return $payment;
    }
}
?>
