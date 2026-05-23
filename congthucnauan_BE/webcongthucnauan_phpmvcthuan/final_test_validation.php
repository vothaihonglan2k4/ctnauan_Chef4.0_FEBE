<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Final Validation Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 30px; background: #f0f0f0; }
        .container { max-width: 600px; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎯 Final Test - Validation Production Fix</h2>
        <p>Test với dữ liệu giống như trong ảnh của bạn:</p>
        
        <div class="mb-3">
            <label>Tên phòng học (nhập 'fff' hoặc 'dddhhhhhhh...'):</label>
            <input type="text" class="form-control" id="test-name" value="" style="font-size: 16px; padding: 12px;">
            <div id="name-feedback" class="mt-2"></div>
        </div>
        
        <div class="mb-3">
            <label>Mô tả (nhập 'hhhhhhhhhh...'):</label>
            <textarea class="form-control" id="test-desc" rows="3" style="font-size: 16px; padding: 12px;"></textarea>
            <div id="desc-feedback" class="mt-2"></div>
        </div>
        
        <button class="btn btn-success" onclick="testBoth()">🧪 Test Both</button>
        <button class="btn btn-primary" onclick="testLikeImage()">📸 Test Like Your Image</button>
        <button class="btn btn-danger" onclick="clearBoth()">🗑️ Clear</button>
        
        <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 5px;">
            <h5>Debug Log:</h5>
            <div id="debug" style="font-family: monospace; font-size: 12px;"></div>
        </div>
        
        <div class="mt-3">
            <a href="/webcongthucnauan/admin/add_classroom" class="btn btn-warning">🔗 Test Real Add Form</a>
            <a href="/webcongthucnauan/admin/edit_classroom/5" class="btn btn-info">🔗 Test Real Edit Form</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function log(msg) {
            const debugDiv = document.getElementById('debug');
            debugDiv.innerHTML += new Date().toLocaleTimeString() + ': ' + msg + '<br>';
            debugDiv.scrollTop = debugDiv.scrollHeight;
        }
        
        function applyGreenBorder(element, feedbackDiv) {
            log(`Applying GREEN border to ${element.id}`);
            
            // Complete inline style override
            element.style.cssText = `
                border: 6px solid #28a745 !important;
                background: linear-gradient(45deg, #f0fff4, #e8f8e8) !important;
                box-shadow: 0 0 25px rgba(40, 167, 69, 0.8) !important;
                outline: none !important;
                transition: all 0.3s ease !important;
                padding: 12px !important;
                font-size: 16px !important;
                width: 100% !important;
            `;
            
            feedbackDiv.innerHTML = '<span style="color: #28a745; font-weight: bold;">✅ HỢP LỆ!</span>';
            log(`GREEN border applied to ${element.id}`);
        }
        
        function applyRedBorder(element, feedbackDiv, message) {
            log(`Applying RED border to ${element.id}: ${message}`);
            
            // Complete inline style override  
            element.style.cssText = `
                border: 6px solid #dc3545 !important;
                background: linear-gradient(45deg, #fff5f5, #ffe8e8) !important;
                box-shadow: 0 0 25px rgba(220, 53, 69, 0.8) !important;
                outline: none !important;
                transition: all 0.3s ease !important;
                padding: 12px !important;
                font-size: 16px !important;
                width: 100% !important;
            `;
            
            feedbackDiv.innerHTML = '<span style="color: #dc3545; font-weight: bold;">❌ ' + message + '</span>';
            log(`RED border applied to ${element.id}`);
        }
        
        function validateName() {
            const nameInput = document.getElementById('test-name');
            const nameFeedback = document.getElementById('name-feedback');
            const value = nameInput.value.trim();
            
            if (value.length === 0) {
                applyRedBorder(nameInput, nameFeedback, 'Trường này là bắt buộc');
            } else if (value.length < 3) {
                applyRedBorder(nameInput, nameFeedback, 'Phải có ít nhất 3 ký tự');
            } else {
                applyGreenBorder(nameInput, nameFeedback);
            }
        }
        
        function validateDesc() {
            const descInput = document.getElementById('test-desc');
            const descFeedback = document.getElementById('desc-feedback');
            const value = descInput.value.trim();
            
            if (value.length === 0) {
                applyRedBorder(descInput, descFeedback, 'Trường này là bắt buộc');
            } else if (value.length < 10) {
                applyRedBorder(descInput, descFeedback, 'Phải có ít nhất 10 ký tự');
            } else {
                applyGreenBorder(descInput, descFeedback);
            }
        }
        
        function testBoth() {
            log('=== TESTING VALIDATION ===');
            validateName();
            validateDesc();
        }
        
        function testLikeImage() {
            log('=== TESTING WITH YOUR IMAGE DATA ===');
            document.getElementById('test-name').value = 'dddhhhhhhhhhhhhhhhhhhhhh';
            document.getElementById('test-desc').value = 'hhhhhhhhhhhhhhhhhhhhhhhhhh';
            setTimeout(() => {
                validateName();
                validateDesc();
            }, 100);
        }
        
        function clearBoth() {
            document.getElementById('test-name').value = '';
            document.getElementById('test-desc').value = '';
            document.getElementById('test-name').style.cssText = '';
            document.getElementById('test-desc').style.cssText = '';
            document.getElementById('name-feedback').innerHTML = '';
            document.getElementById('desc-feedback').innerHTML = '';
            document.getElementById('debug').innerHTML = 'Cleared<br>';
        }
        
        // Auto validation on input
        $('#test-name').on('input', validateName);
        $('#test-desc').on('input', validateDesc);
        
        // Initial validation
        setTimeout(() => {
            log('Page loaded - Ready for testing');
            validateName();
            validateDesc();
        }, 200);
    </script>
</body>
</html> 