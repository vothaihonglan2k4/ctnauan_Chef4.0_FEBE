import React from 'react';
import AboutMission from '../Components/About/AboutMission';
import AboutContact from '../Components/About/AboutContact';

export default function AboutPage() {
    return (
        <div className="container mt-3 py-4">
            <div className="row">
                <div className="col-md-8 mx-auto">
                    <h1 className="mb-4">Về Chúng Tôi</h1>

                    <AboutMission />
                    <AboutContact />
                </div>
            </div>
        </div>
    );
}
