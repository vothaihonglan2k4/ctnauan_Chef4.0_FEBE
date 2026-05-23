import React, { useState } from 'react';
import ContactForm from '../Components/Contact/ContactForm';
import ContactInfo from '../Components/Contact/ContactInfo';
import WorkingHours from '../Components/Contact/WorkingHours';
import SocialLinks from '../Components/Contact/SocialLinks';
import ContactMap from '../Components/Contact/ContactMap';

export default function ContactPage() {
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (formData, setErrors, resetForm) => {
        // Validate
        const newErrors = {};
        if (!formData.name.trim()) {
            newErrors.name = 'Vui lòng nhập họ tên';
        }
        if (!formData.email.trim()) {
            newErrors.email = 'Vui lòng nhập email';
        } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
            newErrors.email = 'Email không hợp lệ';
        }
        if (!formData.subject.trim()) {
            newErrors.subject = 'Vui lòng nhập tiêu đề';
        }
        if (!formData.message.trim()) {
            newErrors.message = 'Vui lòng nhập nội dung';
        }

        if (Object.keys(newErrors).length > 0) {
            setErrors(newErrors);
            return;
        }

        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const response = await fetch('/api/v1/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok && data.success) {
                setSuccess(data.message || 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
                // Reset form after successful submission (like PHP MVC)
                resetForm();
            } else {
                // Handle validation errors from backend
                if (data.errors && typeof data.errors === 'object') {
                    setErrors(data.errors);
                } else {
                    setError(data.message || 'Có lỗi xảy ra khi gửi tin nhắn');
                }
            }
        } catch (err) {
            console.error('Error:', err);
            setError('Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="container py-5">
            <div className="row">
                {/* Contact Form */}
                <div className="col-lg-8 mb-4">
                    <div className="card border-0 shadow-sm rounded-3">
                        <div className="card-body p-4">
                            <h2 className="text-primary fw-bold mb-4">Liên hệ với chúng tôi</h2>
                            <p className="text-muted mb-4">
                                Chúng tôi rất mong nhận được phản hồi và góp ý từ bạn để cải thiện chất lượng dịch vụ.
                                Vui lòng điền thông tin vào mẫu dưới đây để liên hệ với chúng tôi.
                            </p>

                            {/* Success Message */}
                            {success && (
                                <div className="alert alert-success alert-dismissible fade show">
                                    <i className="fas fa-check-circle me-2"></i>{success}
                                    <button type="button" className="btn-close" onClick={() => setSuccess('')}></button>
                                </div>
                            )}

                            {/* Error Message */}
                            {error && (
                                <div className="alert alert-danger alert-dismissible fade show">
                                    <i className="fas fa-exclamation-circle me-2"></i>{error}
                                    <button type="button" className="btn-close" onClick={() => setError('')}></button>
                                </div>
                            )}

                            <ContactForm onSubmit={handleSubmit} loading={loading} />
                        </div>
                    </div>
                </div>

                {/* Sidebar */}
                <div className="col-lg-4">
                    <ContactInfo />
                    <WorkingHours />
                    <SocialLinks />
                </div>
            </div>

            {/* Google Maps */}
            <ContactMap />
        </div>
    );
}
