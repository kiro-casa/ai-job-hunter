import { useState, useEffect } from 'react';
import api from '../services/api';

export default function DashboardPage() {
  const [stats, setStats] = useState(null);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const response = await api.get('/dashboard');
      if (response.data.success) {
        setStats(response.data.data);
      }
    } catch (err) {
      console.error('Failed to fetch stats:', err);
    }
  };

  const statCards = stats ? [
    { label: 'Total Applications', value: stats.total, color: 'text-gray-900' },
    { label: 'Saved', value: stats.saved, color: 'text-gray-600' },
    { label: 'Qualified', value: stats.qualified, color: 'text-blue-600' },
    { label: 'Ready to Apply', value: stats.ready_to_apply, color: 'text-yellow-600' },
    { label: 'Applied', value: stats.applied, color: 'text-purple-600' },
    { label: 'Interviews', value: stats.interview, color: 'text-orange-600' },
    { label: 'Offers', value: stats.offer, color: 'text-green-600' },
    { label: 'Accepted', value: stats.accepted, color: 'text-emerald-600' },
    { label: 'Rejected', value: stats.rejected, color: 'text-red-600' },
  ] : [];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
      </div>

      {/* Stats Grid - Responsive */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-4">
        {statCards.map(stat => (
          <div key={stat.label} className="bg-white rounded-lg shadow p-3 sm:p-4">
            <p className="text-xs sm:text-sm text-gray-600 truncate">{stat.label}</p>
            <p className={`text-2xl sm:text-3xl font-bold mt-2 ${stat.color}`}>
              {stat.value}
            </p>
          </div>
        ))}
      </div>

      {/* Welcome Section */}
      <div className="bg-white rounded-lg shadow p-4 sm:p-6">
        <h2 className="text-lg sm:text-xl font-semibold text-gray-800 mb-4">
          Welcome to AI Job Hunter
        </h2>
        <p className="text-sm sm:text-base text-gray-600 mb-4">
          Your AI-powered career assistant is ready. Here's how to get started:
        </p>
        <ol className="list-decimal list-inside space-y-2 text-sm sm:text-base text-gray-700">
          <li>Upload your resume in the <strong>Resume</strong> tab</li>
          <li>Add jobs you're interested in in the <strong>Jobs</strong> tab</li>
          <li>View match scores and eligibility in each job detail</li>
          <li>Save jobs to track them in the <strong>Applications</strong> tab</li>
          <li>Use the Kanban board to track your application status</li>
        </ol>
      </div>
    </div>
  );
}