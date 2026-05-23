<?php

class ApiResponse {
    
    public function success($data = null, $message = 'Thành công', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    public function error($message = 'Có lỗi xảy ra', $code = 400, $errors = null) {
        http_response_code($code);
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    public function validation($errors, $message = 'Dữ liệu không hợp lệ') {
        $this->error($message, 422, $errors);
    }
    
    public function notFound($message = 'Không tìm thấy dữ liệu') {
        $this->error($message, 404);
    }
    
    public function unauthorized($message = 'Không có quyền truy cập') {
        $this->error($message, 401);
    }
    
    public function forbidden($message = 'Truy cập bị từ chối') {
        $this->error($message, 403);
    }
} 