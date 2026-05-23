import React from 'react';

const ContactMap = () => {
    return (
        <div className="row mt-5">
            <div className="col-12">
                <div className="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div className="card-body p-0">
                        <div className="ratio ratio-21x9">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1917.2555175659147!2d108.2416236384948!3d16.03894959615902!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314217056218e8f3%3A0xee870d6af1db6de8!2zMjA5IE5nxakgSMOgbmggU8ahbiwgQuG6r2MgTeG7uSBQaMO6LCBOZ8WpIEjDoG5oIFPGoW4sIMSQw6AgTuG6tW5nLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1757313960072!5m2!1svi!2s"
                                width="600"
                                height="450"
                                style={{ border: 0 }}
                                allowFullScreen=""
                                loading="lazy"
                                referrerPolicy="no-referrer-when-downgrade"
                                title="Google Maps Location"
                            ></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ContactMap;
