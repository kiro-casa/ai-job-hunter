import { useState, useEffect } from 'react';
import api from '../services/api';

export default function JobsPage() {
  const [jobs, setJobs] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const [selectedJob, setSelectedJob] = useState(null);

  const [formData, setFormData] = useState({
    title: '',
    company: '',
    description: '',
    location: '',
    work_arrangement: 'unspecified',
    employment_type: 'unspecified',
  });

  useEffect(() => {
    fetchJobs();
  }, []);

  const fetchJobs = async () => {
    try {
      const response = await api.get('/jobs');
      if (response.data.success) {
        setJobs(response.data.data);
      }
    } catch (err) {
      console.error('Failed to fetch jobs:', err);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setSuccessMessage('');

    try {
      const response = await api.post('/jobs', formData);
      if (response.data.success) {
        setSuccessMessage('Job added and analyzed successfully!');
        setFormData({
          title: '',
          company: '',
          description: '',
          location: '',
          work_arrangement: 'unspecified',
          employment_type: 'unspecified',
        });
        setShowForm(false);
        fetchJobs();
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to add job');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  return (
    <div className="max-w-6xl mx-auto">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-gray-900">Jobs</h1>
        <button
          onClick={() => setShowForm(!showForm)}
          className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
        >
          {showForm ? 'Cancel' : '+ Add Job'}
        </button>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 rounded p-3 mb-4">
          <p className="text-red-700 text-sm">{error}</p>
        </div>
      )}

      {successMessage && (
        <div className="bg-green-50 border border-green-200 rounded p-3 mb-4">
          <p className="text-green-700 text-sm">{successMessage}</p>
        </div>
      )}

      {/* Add Job Form */}
      {showForm && (
        <div className="bg-white rounded-lg shadow p-6 mb-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Add New Job</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Job Title *
                </label>
                <input
                  type="text"
                  name="title"
                  value={formData.title}
                  onChange={handleChange}
                  required
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g., Database Administrator"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Company *
                </label>
                <input
                  type="text"
                  name="company"
                  value={formData.company}
                  onChange={handleChange}
                  required
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g., ABC Corporation"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Location
                </label>
                <input
                  type="text"
                  name="location"
                  value={formData.location}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g., Manila, Philippines"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Work Arrangement
                </label>
                <select
                  name="work_arrangement"
                  value={formData.work_arrangement}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="unspecified">Not specified</option>
                  <option value="remote">Remote</option>
                  <option value="hybrid">Hybrid</option>
                  <option value="on_site">On-site</option>
                </select>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Job Description *
              </label>
              <textarea
                name="description"
                value={formData.description}
                onChange={handleChange}
                required
                rows={10}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Paste the full job description here..."
              />
            </div>

            <button
              type="submit"
              disabled={loading}
              className="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:bg-gray-400"
            >
              {loading ? 'Analyzing...' : 'Add & Analyze Job'}
            </button>
          </form>
        </div>
      )}

      {/* Job List */}
      <div className="grid grid-cols-1 gap-4">
        {jobs.length === 0 ? (
          <div className="bg-white rounded-lg shadow p-6 text-center">
            <p className="text-gray-600">No jobs added yet. Click "+ Add Job" to get started.</p>
          </div>
        ) : (
          jobs.map(job => (
            <div
              key={job.id}
              onClick={() => setSelectedJob(selectedJob?.id === job.id ? null : job)}
              className="bg-white rounded-lg shadow p-6 cursor-pointer hover:shadow-md transition-shadow"
            >
              <div className="flex justify-between items-start">
                <div>
                  <h3 className="text-lg font-semibold text-gray-900">{job.title}</h3>
                  <p className="text-gray-600">{job.company}</p>
                  {job.location && (
                    <p className="text-sm text-gray-500 mt-1">{job.location}</p>
                  )}
                </div>
                <span className={`px-3 py-1 rounded-full text-xs font-semibold ${
                  job.status === 'new' ? 'bg-gray-100 text-gray-800' :
                  job.status === 'qualified' ? 'bg-green-100 text-green-800' :
                  job.status === 'not_qualified' ? 'bg-red-100 text-red-800' :
                  'bg-blue-100 text-blue-800'
                }`}>
                  {job.status.replace('_', ' ').toUpperCase()}
                </span>
              </div>

              {selectedJob?.id === job.id && (
                <div className="mt-4 pt-4 border-t">
                  <h4 className="font-medium text-gray-800 mb-2">Extracted Requirements:</h4>
                  {job.requirements && job.requirements.length > 0 ? (
                    <div className="space-y-2">
                      {job.requirements.map(req => (
                        <div key={req.id} className="flex items-start">
                          <span className={`inline-block w-2 h-2 rounded-full mt-2 mr-2 ${
                            req.classification === 'mandatory' ? 'bg-red-500' :
                            req.classification === 'preferred' ? 'bg-yellow-500' :
                            'bg-gray-400'
                          }`}></span>
                          <div>
                            <p className="text-sm text-gray-700">{req.requirement_text}</p>
                            <p className="text-xs text-gray-500">
                              Type: {req.requirement_type} | Classification: {req.classification}
                            </p>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <p className="text-sm text-gray-500">No requirements extracted yet.</p>
                  )}
                </div>
              )}
            </div>
          ))
        )}
      </div>
    </div>
  );
}