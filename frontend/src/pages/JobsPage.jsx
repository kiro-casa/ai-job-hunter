import { useState, useEffect } from 'react';
import api from '../services/api';

export default function JobsPage() {
  const [jobs, setJobs] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [successMessage, setSuccessMessage] = useState('');
  const [selectedJob, setSelectedJob] = useState(null);
  const [viewJobModal, setViewJobModal] = useState(null);
  const [matchData, setMatchData] = useState(null);
  const [matchLoading, setMatchLoading] = useState(false);
  const [eligibilityData, setEligibilityData] = useState(null);
  const [eligibilityLoading, setEligibilityLoading] = useState(false);
  const [skillGapData, setSkillGapData] = useState(null);
  const [skillGapLoading, setSkillGapLoading] = useState(false);

  const saveToApplications = async (jobId) => {
    try {
      const response = await api.post('/applications', { job_id: jobId });
      if (response.data.success) {
        if (response.data.already_exists) {
          setError('This job is already in your applications');
        } else {
          setSuccessMessage('Job saved to applications!');
        }
        setTimeout(() => {
          setSuccessMessage('');
          setError('');
        }, 3000);
      } else {
        setError(response.data.message || 'Failed to save to applications');
      }
    } catch (err) {
      console.error('Save application error:', err);
      setError(err.response?.data?.message || 'Failed to save to applications');
    }
  };

  const analyzeSkillGaps = async (jobId) => {
    setSkillGapLoading(true);
    setError('');
    try {
      const response = await api.post(`/jobs/${jobId}/skill-gaps`);
      if (response.data.success) {
        setSkillGapData(response.data.data);
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to analyze skill gaps');
    } finally {
      setSkillGapLoading(false);
    }
  };

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
          title: '', company: '', description: '',
          location: '', work_arrangement: 'unspecified', employment_type: 'unspecified',
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
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const calculateMatch = async (jobId) => {
    setMatchLoading(true);
    setError('');
    try {
      const response = await api.post(`/jobs/${jobId}/match`);
      if (response.data.success) {
        setMatchData(response.data.data);
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to calculate match');
    } finally {
      setMatchLoading(false);
    }
  };

  const getScoreColor = (score) => {
    if (score >= 85) return 'text-green-600 bg-green-50 border-green-200';
    if (score >= 70) return 'text-blue-600 bg-blue-50 border-blue-200';
    if (score >= 55) return 'text-yellow-600 bg-yellow-50 border-yellow-200';
    return 'text-red-600 bg-red-50 border-red-200';
  };

  const getRecommendationBadge = (rec) => {
    const badges = {
      high_priority: 'bg-green-100 text-green-800',
      good_match: 'bg-blue-100 text-blue-800',
      possible_match: 'bg-yellow-100 text-yellow-800',
      weak_match: 'bg-orange-100 text-orange-800',
      not_recommended: 'bg-red-100 text-red-800',
    };
    return badges[rec] || 'bg-gray-100 text-gray-800';
  };

  const checkEligibility = async (jobId) => {
    setEligibilityLoading(true);
    setError('');
    try {
      const response = await api.post(`/jobs/${jobId}/eligibility/check`);
      if (response.data.success) {
        setEligibilityData(response.data.data);
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to check eligibility');
    } finally {
      setEligibilityLoading(false);
    }
  };

  return (
    <div className="max-w-6xl mx-auto p-4">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-3xl font-bold text-gray-900">Jobs</h1>
        <button
          onClick={() => setShowForm(!showForm)}
          className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition"
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

      {showForm && (
        <div className="bg-white rounded-lg shadow p-6 mb-6">
          <h2 className="text-xl font-semibold text-gray-800 mb-4">Add New Job</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Job Title *</label>
                <input type="text" name="title" value={formData.title} onChange={handleChange} required
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g., Database Administrator" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Company *</label>
                <input type="text" name="company" value={formData.company} onChange={handleChange} required
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g., ABC Corporation" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" name="location" value={formData.location} onChange={handleChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="e.g., Manila, Philippines" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Work Arrangement</label>
                <select name="work_arrangement" value={formData.work_arrangement} onChange={handleChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="unspecified">Not specified</option>
                  <option value="remote">Remote</option>
                  <option value="hybrid">Hybrid</option>
                  <option value="on_site">On-site</option>
                </select>
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Job Description *</label>
              <textarea name="description" value={formData.description} onChange={handleChange} required rows={10}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Paste the full job description here..." />
            </div>
            <button type="submit" disabled={loading}
              className="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:bg-gray-400 transition">
              {loading ? 'Analyzing...' : 'Add & Analyze Job'}
            </button>
          </form>
        </div>
      )}

      {/* Match Score Display */}
      {matchData && (
        <div className={`rounded-lg border p-6 mb-6 ${getScoreColor(matchData.overall_score)}`}>
          <div className="flex justify-between items-start mb-4">
            <div>
              <h2 className="text-2xl font-bold">Match Score: {matchData.overall_score}%</h2>
              <span className={`inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold ${getRecommendationBadge(matchData.recommendation)}`}>
                {matchData.recommendation.replace('_', ' ').toUpperCase()}
              </span>
            </div>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
            {matchData.components?.map(comp => (
              <div key={comp.component_name} className="bg-white bg-opacity-50 rounded p-3">
                <p className="text-sm font-medium">{comp.component_name}</p>
                <p className="text-lg font-bold">{comp.score}%</p>
                <p className="text-xs text-gray-500">Weight: {comp.weight * 100}%</p>
              </div>
            ))}
          </div>

          {matchData.explanation && (
            <div className="bg-white bg-opacity-50 rounded p-3">
              <h3 className="font-medium mb-2">Explanation:</h3>
              <pre className="text-sm whitespace-pre-wrap font-sans">{matchData.explanation}</pre>
            </div>
          )}
        </div>
      )}

      {eligibilityData && (
        <div className={`rounded-lg border p-6 mb-6 ${
          eligibilityData.eligible 
            ? 'bg-green-50 border-green-200 text-green-800' 
            : 'bg-red-50 border-red-200 text-red-800'
        }`}>
          <h2 className="text-xl font-bold mb-2">
            Eligibility Decision: {eligibilityData.decision.replace('_', ' ')}
          </h2>
          <p className="text-sm mb-4">{eligibilityData.reason}</p>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
              <p className="font-semibold">Mandatory Requirements:</p>
              <p>{eligibilityData.required_requirements_met} / {eligibilityData.required_requirements} met</p>
              {eligibilityData.missing_required.length > 0 && (
                <div className="mt-2">
                  <p className="font-semibold text-red-700">Missing:</p>
                  <ul className="list-disc list-inside">
                    {eligibilityData.missing_required.map((req, idx) => (
                      <li key={idx}>{req}</li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
            <div>
              <p className="font-semibold">Preferred Requirements:</p>
              <p>{eligibilityData.preferred_requirements_met} / {eligibilityData.preferred_requirements} met</p>
              {eligibilityData.missing_preferred.length > 0 && (
                <div className="mt-2">
                  <p className="font-semibold text-yellow-700">Missing (Won't block auto-apply):</p>
                  <ul className="list-disc list-inside">
                    {eligibilityData.missing_preferred.map((req, idx) => (
                      <li key={idx}>{req}</li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
          </div>
          
          <div className="mt-4 pt-4 border-t border-current border-opacity-20">
            <p className="text-sm font-semibold">Confidence Score: {(eligibilityData.confidence * 100).toFixed(0)}%</p>
          </div>
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
            <div key={job.id}
              onClick={() => {
                setSelectedJob(selectedJob?.id === job.id ? null : job);
                setMatchData(null);
              }}
              className="bg-white rounded-lg shadow p-6 cursor-pointer hover:shadow-md transition-shadow"
            >
              <div className="flex justify-between items-start gap-4">
                <div className="flex-1">
                  <h3 className="text-lg font-semibold text-gray-900">{job.title}</h3>
                  <p className="text-gray-600">{job.company}</p>
                  {job.location && <p className="text-sm text-gray-500 mt-1">📍 {job.location}</p>}
                </div>
                
                <div className="flex flex-col items-end gap-2">
                  <button
                    onClick={(e) => { e.stopPropagation(); setViewJobModal(job); }}
                    className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm transition"
                  >
                    View Job
                  </button>
                  <span className={`px-3 py-1 rounded-full text-xs font-semibold ${
                    job.status === 'new' ? 'bg-gray-100 text-gray-800' :
                    job.status === 'qualified' ? 'bg-green-100 text-green-800' :
                    'bg-blue-100 text-blue-800'
                  }`}>
                    {job.status.replace('_', ' ').toUpperCase()}
                  </span>
                </div>
              </div>

              {selectedJob?.id === job.id && (
                <div className="mt-4 pt-4 border-t">
                  <div className="flex justify-between items-center mb-4">
                    <h4 className="font-medium text-gray-800">Extracted Requirements:</h4>
                    <button
                      onClick={(e) => { e.stopPropagation(); calculateMatch(job.id); }}
                      disabled={matchLoading}
                      className="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 disabled:bg-gray-400 text-sm transition"
                    >
                      {matchLoading ? 'Calculating...' : 'Calculate Match Score'}
                    </button>
                  </div>
                  
                  <div className="flex gap-2 mb-4 flex-wrap">
                    <button
                      onClick={(e) => { e.stopPropagation(); calculateMatch(job.id); }}
                      disabled={matchLoading}
                      className="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 disabled:bg-gray-400 text-sm transition"
                    >
                      {matchLoading ? 'Calculating...' : 'Calculate Match'}
                    </button>
                    <button
                      onClick={(e) => { e.stopPropagation(); checkEligibility(job.id); }}
                      disabled={eligibilityLoading}
                      className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:bg-gray-400 text-sm transition"
                    >
                      {eligibilityLoading ? 'Checking...' : 'Check Eligibility'}
                    </button>
                    <button
                      onClick={(e) => { e.stopPropagation(); analyzeSkillGaps(job.id); }}
                      disabled={skillGapLoading}
                      className="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700 disabled:bg-gray-400 text-sm transition"
                    >
                      {skillGapLoading ? 'Analyzing...' : 'Analyze Skill Gaps'}
                    </button>
                    <button
                      onClick={(e) => { e.stopPropagation(); saveToApplications(job.id); }}
                      className="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm transition"
                    >
                      Save to Applications
                    </button>
                  </div>

                  {skillGapData && skillGapData.skill_gaps.length > 0 && (
                    <div className="bg-white rounded-lg border border-gray-200 p-6 mb-6">
                      <h2 className="text-xl font-bold text-gray-900 mb-4">Skill Gap Analysis</h2>
                      <div className="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                        <p className="text-sm text-blue-800 whitespace-pre-wrap">{skillGapData.summary}</p>
                      </div>
                      <h3 className="text-lg font-semibold text-gray-800 mb-3">Learning Priorities</h3>
                      <div className="space-y-3">
                        {skillGapData.learning_priorities.map((priority, idx) => {
                          const gap = skillGapData.skill_gaps.find(g => g.skill === priority.skill);
                          return (
                            <div key={idx} className="border border-gray-200 rounded-lg p-4">
                              <div className="flex justify-between items-start mb-2">
                                <div>
                                  <h4 className="font-semibold text-gray-900">{idx + 1}. {gap.skill}</h4>
                                  <p className="text-sm text-gray-600">{gap.category}</p>
                                </div>
                                <span className={`px-2 py-1 rounded text-xs font-semibold ${
                                  gap.importance === 'high' ? 'bg-red-100 text-red-800' :
                                  gap.importance === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                  'bg-gray-100 text-gray-800'
                                }`}>
                                  {gap.importance.toUpperCase()} PRIORITY
                                </span>
                              </div>
                              <p className="text-sm text-gray-700 mb-2">{gap.reason}</p>
                              <div className="flex gap-4 text-xs text-gray-600">
                                <span>Difficulty: {gap.difficulty}</span>
                                <span>Learning Time: {gap.estimated_learning_time}</span>
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  )}

                  {skillGapData && skillGapData.skill_gaps.length === 0 && (
                    <div className="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                      <h2 className="text-xl font-bold text-green-900 mb-2">No Skill Gaps!</h2>
                      <p className="text-sm text-green-800">{skillGapData.summary}</p>
                    </div>
                  )}

                  {job.requirements && job.requirements.length > 0 ? (
                    <div className="space-y-2">
                      {job.requirements.map(req => (
                        <div key={req.id} className="flex items-start">
                          <span className={`inline-block w-2 h-2 rounded-full mt-2 mr-2 flex-shrink-0 ${
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

      {/* ✅ NEW: View Job Modal */}
      {viewJobModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
          <div className="bg-white rounded-lg shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6">
              <div className="flex justify-between items-start mb-4">
                <div>
                  <h2 className="text-2xl font-bold text-gray-900">{viewJobModal.title}</h2>
                  <p className="text-lg text-gray-600">{viewJobModal.company}</p>
                  {viewJobModal.location && (
                    <p className="text-sm text-gray-500 mt-1 flex items-center">
                      📍 {viewJobModal.location}
                    </p>
                  )}
                  <div className="flex gap-2 mt-3">
                    <span className="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold uppercase">
                      {viewJobModal.work_arrangement?.replace('_', ' ')}
                    </span>
                    <span className="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold uppercase">
                      {viewJobModal.employment_type?.replace('_', ' ')}
                    </span>
                  </div>
                </div>
                <button
                  onClick={() => setViewJobModal(null)}
                  className="text-gray-400 hover:text-gray-600 text-3xl font-bold leading-none"
                >
                  &times;
                </button>
              </div>

              <div className="border-t pt-4">
                <h3 className="text-lg font-semibold text-gray-800 mb-2">Job Description</h3>
                <div className="text-gray-700 whitespace-pre-wrap text-sm leading-relaxed bg-gray-50 p-4 rounded-md border border-gray-200 max-h-64 overflow-y-auto">
                  {viewJobModal.description || 'No description provided.'}
                </div>
              </div>

              {viewJobModal.requirements && viewJobModal.requirements.length > 0 && (
                <div className="border-t pt-4 mt-4">
                  <h3 className="text-lg font-semibold text-gray-800 mb-2">Extracted Requirements</h3>
                  <div className="space-y-2 max-h-48 overflow-y-auto">
                    {viewJobModal.requirements.map(req => (
                      <div key={req.id} className="flex items-start">
                        <span className={`inline-block w-2 h-2 rounded-full mt-2 mr-2 flex-shrink-0 ${
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
                </div>
              )}

              <div className="mt-6 flex justify-end gap-3 border-t pt-4">
                <button
                  onClick={() => setViewJobModal(null)}
                  className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                >
                  Close
                </button>
                <button
                  onClick={() => {
                    setViewJobModal(null);
                    setSelectedJob(viewJobModal);
                  }}
                  className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                >
                  Analyze This Job
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}