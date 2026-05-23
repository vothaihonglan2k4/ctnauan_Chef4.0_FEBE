import React, { useState } from 'react';
import ScheduleTable from './ScheduleTable';

const ScheduleTabs = ({ schedules }) => {
    const [activeTab, setActiveTab] = useState('upcoming');

    return (
        <div className="card shadow-sm">
            {/* Tabs Header */}
            <div className="card-header bg-white">
                <ul className="nav nav-tabs card-header-tabs" role="tablist">
                    <li className="nav-item" role="presentation">
                        <button
                            className={`nav-link ${activeTab === 'upcoming' ? 'active' : ''}`}
                            onClick={() => setActiveTab('upcoming')}
                            type="button"
                            role="tab"
                        >
                            Lịch học sắp tới
                        </button>
                    </li>
                    <li className="nav-item" role="presentation">
                        <button
                            className={`nav-link ${activeTab === 'all' ? 'active' : ''}`}
                            onClick={() => setActiveTab('all')}
                            type="button"
                            role="tab"
                        >
                            Tất cả lịch học
                        </button>
                    </li>
                </ul>
            </div>

            {/* Tabs Content */}
            <div className="card-body">
                <div className="tab-content">
                    {/* Upcoming Tab */}
                    {activeTab === 'upcoming' && (
                        <div className="tab-pane fade show active" role="tabpanel">
                            <ScheduleTable schedules={schedules} showTime={true} />
                        </div>
                    )}

                    {/* All Tab */}
                    {activeTab === 'all' && (
                        <div className="tab-pane fade show active" role="tabpanel">
                            <ScheduleTable schedules={schedules} showTime={false} />
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ScheduleTabs;
