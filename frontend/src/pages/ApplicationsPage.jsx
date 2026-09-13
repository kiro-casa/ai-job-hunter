import { useState, useEffect } from 'react';
import api from '../services/api';
import ApplicationAssistant from '../components/ApplicationAssistant';
import InterviewPreparation from '../components/InterviewPreparation';

export default function ApplicationsPage() {
  const [applications, setApplications] = useState({});
  const [selectedApp, setSelectedApp] = useState(null);
  const [newNote, setNewNote] = useState('');
  const [loading, setLoading] = useState(false);
  const [autoApplyLoading, setAutoApplyLoading] = useState(false);
  const [autoApplyResult, setAutoApplyResult] = useState(null);

  const statusColumns = [
    { key: 'saved', label: 'Saved', color: 'bg-gray-100' },
    { key: 'qualified', label: 'Qualified', color: 'bg-blue-100' },
    { key: 'ready_to_apply', label: 'Ready to Apply', color: 'bg-yellow-100' },
    { key: 'applied', label: 'Applied', color: 'bg-purple-100' },
    { key: 'screening', label: 'Screening', color: 'bg-indigo-100' },
    { key: 'interview', label: 'Interview', color: 'bg-orange-100' },
    { key: 'offer', label: 'Offer', color: 'bg-green-100' },
    { key: 'accepted', label: 'Accepted', color: 'bg-emerald-100' },
    { key: 'rejected', label: 'Rejected', color: 'bg-red-100' },
  ];

  useEffect(() => {
    fetchApplications();
  }, []);

  const fetchApplications = async () => {
    try {
      const response = await api.get('/applications');
      if (response.data.success) {
        setApplications(response.data.data);
      }
    } catch (err) {
      console.error('Failed to fetch applications:', err);
    }
  };

  const updateStatus = async (appId, newStatus) => {
    try {
      await api.patch(`/applications/${appId}/status`, { status: newStatus });
      fetchApplications();
      if (selectedApp?.id === appId) {
        setSelectedApp({ ...selectedApp, status: newStatus });
      }
    } catch (err) {
      console.error('Failed to update status:', err);
    }
  };

  const addNote = async (appId) => {
    if (!newNote.trim()) return;
    try {
      await api.post(`/applications/${appId}/notes`, { note: newNote });
      setNewNote('');
      fetchApplications();
    } catch (err) {
      console.error('Failed to add note:', err);
    }
  };

  const attemptAutoApply = async (appId) => {
    setAutoApplyLoading(true);
    setAutoApplyResult(null);
    try {
      const response = await api.post(`/applications/${appId}/auto-apply`);
      setAutoApplyResult(response.data);
      // Refresh applications to get updated status
      fetchApplications();
    } catch (err) {
      setAutoApplyResult(err.response?.data || { success: false, message: 'Unknown error' });
    } finally {
      setAutoApplyLoading(false);
    }
  };

  return (
    <div className="max-w-full mx-auto">
      <h1 className="text-3xl font-bold text-gray-900 mb-6">Application Tracker</h1>

      {/* Kanban Board */}
      <div className="flex gap-4 overflow-x-auto pb-4">
        {statusColumns.map(column => (
          <div key={column.key} className={`flex-shrink-0 w-72 ${column.color} rounded-lg p-4`}>
            <h3 className="font-semibold text-gray-800 mb-3">
              {column.label} ({applications[column.key]?.length || 0})
            </h3>
            <div className="space-y-3">
              {applications[column.key]?.map(app => (
                <div
                  key={app.id}
                  onClick={() => {
                    setSelectedApp(app);
                    setAutoApplyResult(null);
                  }}
                  className="bg-white rounded-lg shadow p-4 cursor-pointer hover:shadow-md transition-shadow"
                >
                  <h4 className="font-semibold text-gray-900">{app.job?.title}</h4>
                  <p className="text-sm text-gray-600">{app.job?.company}</p>
                  {app.job?.location && (
                    <p className="text-xs text-gray-500 mt-1">{app.job.location}</p>
                  )}
                  {app.notes?.length > 0 && (
                    <p className="text-xs text-gray-500 mt-2">
                      {app.notes.length} note{app.notes.length > 1 ? 's' : ''}
                    </p>
                  )}
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>

      {/* Application Detail Modal */}
      {selectedApp && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
          <div className="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6">
              <div className="flex justify-between items-start mb-4">
                <div>
                  <h2 className="text-2xl font-bold text-gray-900">{selectedApp.job?.title}</h2>
                  <p className="text-gray-600">{selectedApp.job?.company}</p>
                  {selectedApp.job?.location && (
                    <p className="text-sm text-gray-500">{selectedApp.job.location}</p>
                  )}
                </div>
                <button
                  onClick={() => {
                    setSelectedApp(null);
                    setAutoApplyResult(null);
                  }}
                  className="text-gray-400 hover:text-gray-600 text-2xl"
                >
                  ×
                </button>
              </div>

              {/* Status Selector */}
              <div className="mb-6">
                <label className="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                <select
                  value={selectedApp.status}
                  onChange={(e) => updateStatus(selectedApp.id, e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md"
                >
                  {statusColumns.map(col => (
                    <option key={col.key} value={col.key}>{col.label}</option>
                  ))}
                </select>
              </div>

              {/* Auto-Apply Safety Gate Section */}
              <div className="mb-6 border-t pt-4">
                <h3 className="text-lg font-semibold text-gray-800 mb-3">🛡️ Automation Safety Gate</h3>
                <button
                  onClick={() => attemptAutoApply(selectedApp.id)}
                  disabled={autoApplyLoading}
                  className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:bg-gray-400 mb-4"
                >
                  {autoApplyLoading ? 'Running Safety Checks...' : 'Run Auto-Apply Check'}
                </button>

                {autoApplyResult && (
                  <div className={`rounded-lg p-4 border ${
                    autoApplyResult.success 
                      ? 'bg-green-50 border-green-200' 
                      : 'bg-red-50 border-red-200'
                  }`}>
                    <h4 className={`font-bold mb-2 ${
                      autoApplyResult.success ? 'text-green-800' : 'text-red-800'
                    }`}>
                      Result: {autoApplyResult.success ? 'APPROVED' : 'BLOCKED'}
                    </h4>
                    <p className="text-sm mb-3 font-medium">
                      {autoApplyResult.message || autoApplyResult.data?.safety_result?.blocked_reason}
                    </p>

                    {/* Safety Check Details */}
                    <div className="space-y-2 text-sm">
                      {autoApplyResult.data?.safety_result?.checks && 
                       Object.entries(autoApplyResult.data.safety_result.checks).map(([key, check]) => (
                        <div key={key} className="flex items-start">
                          <span className={`mt-1 mr-2 text-lg ${check.passed ? 'text-green-600' : 'text-red-600'}`}>
                            {check.passed ? '✓' : '✗'}
                          </span>
                          <div>
                            <span className="font-semibold capitalize">
                              {key.replace(/_/g, ' ')}:
                            </span>
                            <span className="text-gray-700 ml-1">{check.reason}</span>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
              </div>

              {/* AI Application Assistant */}
<div className="mb-6 border-t pt-4">
  <ApplicationAssistant jobId={selectedApp.job_id} />
</div>

{/* Interview Preparation */}
<div className="mb-6 border-t pt-4">
  <InterviewPreparation jobId={selectedApp.job_id} />
</div>

              {/* Notes Section */}
              <div className="mb-6">
                <h3 className="text-lg font-semibold text-gray-800 mb-3">Notes</h3>
                <div className="space-y-2 mb-3 max-h-40 overflow-y-auto">
                  {selectedApp.notes?.length > 0 ? (
                    selectedApp.notes.map(note => (
                      <div key={note.id} className="bg-gray-50 rounded p-3">
                        <p className="text-sm text-gray-700">{note.note}</p>
                      </div>
                    ))
                  ) : (
                    <p className="text-sm text-gray-500">No notes yet</p>
                  )}
                </div>
                <div className="flex gap-2">
                  <input
                    type="text"
                    value={newNote}
                    onChange={(e) => setNewNote(e.target.value)}
                    placeholder="Add a note..."
                    className="flex-1 px-3 py-2 border border-gray-300 rounded-md"
                    onKeyPress={(e) => e.key === 'Enter' && addNote(selectedApp.id)}
                  />
                  <button
                    onClick={() => addNote(selectedApp.id)}
                    className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}