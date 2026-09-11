export default function DashboardPage() {
  const stats = [
    { label: 'Total Jobs', value: 0, color: 'text-gray-900' },
    { label: 'Matched', value: 0, color: 'text-blue-600' },
    { label: 'Qualified', value: 0, color: 'text-green-600' },
    { label: 'Applied', value: 0, color: 'text-purple-600' },
    { label: 'Interviews', value: 0, color: 'text-orange-600' },
    { label: 'Offers', value: 0, color: 'text-emerald-600' },
  ];

  return (
    <div>
      <h1 className="text-3xl font-bold text-gray-900 mb-6">Dashboard</h1>

      <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        {stats.map(stat => (
          <div key={stat.label} className="bg-white rounded-lg shadow p-4">
            <p className="text-sm text-gray-600">{stat.label}</p>
            <p className={`text-3xl font-bold mt-2 ${stat.color}`}>
              {stat.value}
            </p>
          </div>
        ))}
      </div>

      <div className="bg-white rounded-lg shadow p-6">
        <h2 className="text-lg font-semibold text-gray-800 mb-4">
          Welcome to AI Job Hunter
        </h2>
        <p className="text-gray-600 mb-4">
          Your AI-powered career assistant is ready. Here's how to get started:
        </p>
        <ol className="list-decimal list-inside space-y-2 text-gray-700">
          <li>Upload your resume in the <strong>Resume</strong> tab</li>
          <li>Add jobs you're interested in in the <strong>Jobs</strong> tab</li>
          <li>View match scores and eligibility in each job detail</li>
          <li>Track your applications in the <strong>Applications</strong> tab</li>
          <li>Configure auto-apply settings in the <strong>Settings</strong> tab</li>
        </ol>
      </div>
    </div>
  );
}