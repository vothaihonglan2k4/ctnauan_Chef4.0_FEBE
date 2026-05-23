import React, { useState } from 'react';

const ContactForm = ({ onSubmit, loading }) => {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        subject: '',
        message: ''
    });

    const [errors, setErrors] = useState({});

    const resetForm = () => {
        setFormData({
            name: '',
            email: '',
            subject: '',
            message: ''
        });
        setErrors({});
    };

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
        // Clear error when user starts typing
        if (errors[e.target.name]) {
            setErrors({
                ...errors,
                [e.target.name]: ''
            });
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        onSubmit(formData, setErrors, resetForm);
    };

    return (
        <form onSubmit={handleSubmit} className="needs-validation">
            <div className="row">
                <div className="col-md-6 mb-3">
                    <label htmlFor="name" className="form-label small text-muted">
                        Họ tên <span className="text-danger">*</span>
                    </label>
                    <div className="input-group">
                        <span className="input-group-text bg-white">
                            <i className="fas fa-user text-primary"></i>
                        </span>
                        <input
                            type="text"
                            className={`form-control border-start-0 ${errors.name ? 'is-invalid' : ''}`}
                            id="name"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            placeholder="Nhập họ tên của bạn"
                        />
                    </div>
                    {errors.name && <div className="invalid-feedback d-block">{errors.name}</div>}
                </div>
                <div className="col-md-6 mb-3">
                    <label htmlFor="email" className="form-label small text-muted">
                        Email <span className="text-danger">*</span>
                    </label>
                    <div className="input-group">
                        <span className="input-group-text bg-white">
                            <i className="fas fa-envelope text-primary"></i>
                        </span>
                        <input
                            type="email"
                            className={`form-control border-start-0 ${errors.email ? 'is-invalid' : ''}`}
                            id="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            placeholder="Nhập địa chỉ email"
                        />
                    </div>
                    {errors.email && <div className="invalid-feedback d-block">{errors.email}</div>}
                </div>
            </div>
            <div className="mb-3">
                <label htmlFor="subject" className="form-label small text-muted">
                    Tiêu đề <span className="text-danger">*</span>
                </label>
                <div className="input-group">
                    <span className="input-group-text bg-white">
                        <i className="fas fa-heading text-primary"></i>
                    </span>
                    <input
                        type="text"
                        className={`form-control border-start-0 ${errors.subject ? 'is-invalid' : ''}`}
                        id="subject"
                        name="subject"
                        value={formData.subject}
                        onChange={handleChange}
                        placeholder="Nhập tiêu đề liên hệ"
                    />
                </div>
                {errors.subject && <div className="invalid-feedback d-block">{errors.subject}</div>}
            </div>
            <div className="mb-4">
                <label htmlFor="message" className="form-label small text-muted">
                    Nội dung <span className="text-danger">*</span>
                </label>
                <div className="input-group">
                    <span className="input-group-text bg-white">
                        <i className="fas fa-comment-alt text-primary"></i>
                    </span>
                    <textarea
                        className={`form-control border-start-0 ${errors.message ? 'is-invalid' : ''}`}
                        id="message"
                        name="message"
                        rows="6"
                        value={formData.message}
                        onChange={handleChange}
                        placeholder="Nhập nội dung tin nhắn của bạn"
                    ></textarea>
                </div>
                {errors.message && <div className="invalid-feedback d-block">{errors.message}</div>}
            </div>
            <div className="d-grid gap-2">
                <button type="submit" className="btn btn-primary btn-lg" disabled={loading}>
                    {loading ? (
                        <>
                            <span className="spinner-border spinner-border-sm me-2"></span>
                            Đang gửi...
                        </>
                    ) : (
                        <>
                            <i className="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                        </>
                    )}
                </button>
            </div>
        </form>
    );
};

export default ContactForm;
