import { useState } from 'react';
import { useAuth } from '../../contexts/AuthContext';

const ChatbotWidget = () => {
    const { isAuthenticated, user, token } = useAuth();
    const [isOpen, setIsOpen] = useState(false);
    const [input, setInput] = useState('');
    const [messages, setMessages] = useState([
        {
            role: 'assistant',
            content: 'Xin chào! Tôi có thể gợi ý món ăn, nguyên liệu và mẹo nấu nướng cho bạn.',
        },
    ]);
    const [isLoading, setIsLoading] = useState(false);

    const sendMessage = async (event) => {
        event.preventDefault();

        const trimmedInput = input.trim();
        if (!trimmedInput || isLoading) {
            return;
        }

        const userMessage = { role: 'user', content: trimmedInput };
        const recentHistory = messages
            .filter((message) => !message.isError)
            .slice(-10)
            .map(({ role, content }) => ({ role, content }));

        setMessages((currentMessages) => [...currentMessages, userMessage]);
        setInput('');
        setIsLoading(true);

        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch('/api/v1/chatbot', {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    message: trimmedInput,
                    history: recentHistory,
                }),
            });

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Backend chưa trả JSON. Vui lòng kiểm tra server Laravel.');
            }

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Chatbot không thể phản hồi lúc này.');
            }

            setMessages((currentMessages) => [
                ...currentMessages,
                {
                    role: 'assistant',
                    content: data.data?.reply || 'Xin lỗi, tôi chưa có câu trả lời phù hợp.',
                },
            ]);
        } catch (error) {
            setMessages((currentMessages) => [
                ...currentMessages,
                {
                    role: 'assistant',
                    content: error.message || 'Có lỗi xảy ra. Vui lòng thử lại sau.',
                    isError: true,
                },
            ]);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div className="chatbot-widget">
            {isOpen && (
                <div className="chatbot-panel shadow-lg">
                    <div className="chatbot-header bg-primary text-white">
                        <div>
                            <strong>Trợ lý nấu ăn AI</strong>
                            <div className="small opacity-75">Hỏi món ngon, nguyên liệu, cách nấu</div>
                        </div>
                        <button
                            type="button"
                            className="btn btn-sm btn-link text-white text-decoration-none"
                            onClick={() => setIsOpen(false)}
                            aria-label="Đóng chatbot"
                        >
                            <i className="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div className="chatbot-messages">
                        {messages.map((message, index) => (
                            <div
                                key={`${message.role}-${index}`}
                                className={`chatbot-message chatbot-message-${message.role}${message.isError ? ' chatbot-message-error' : ''}`}
                            >
                                {message.content}
                            </div>
                        ))}
                        {isLoading && (
                            <div className="chatbot-message chatbot-message-assistant">
                                Đang suy nghĩ...
                            </div>
                        )}
                    </div>

                    <form className="chatbot-form" onSubmit={sendMessage}>
                        <input
                            type="text"
                            className="form-control"
                            value={input}
                            onChange={(event) => setInput(event.target.value)}
                            placeholder="Bạn muốn nấu món gì?"
                            maxLength="1000"
                            disabled={isLoading}
                        />
                        <button
                            type="submit"
                            className="btn btn-primary"
                            disabled={isLoading || !input.trim()}
                            aria-label="Gửi tin nhắn"
                        >
                            <i className="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            )}

            <button
                type="button"
                className="chatbot-toggle btn btn-primary rounded-circle shadow"
                onClick={() => setIsOpen((currentValue) => !currentValue)}
                aria-label="Mở chatbot AI"
            >
                <i className={`bi ${isOpen ? 'bi-x-lg' : 'bi-robot'}`}></i>
            </button>
        </div>
    );
};

export default ChatbotWidget;
