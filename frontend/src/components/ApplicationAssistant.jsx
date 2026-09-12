import { useState } from 'react';
import api from '../services/api';

export default function ApplicationAssistant({ jobId }) {
  const [activeTab, setActiveTab] = useState('cover-letter');
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState({});
  const [error, setError] = useState('');

  const tabs = [
    { key: 'cover-letter', label: 'Cover Letter', endpoint: 'cover-letter' },
    { key: 'answers', label: 'Q&A Assistant', endpoint: 'answers' },
    { key: 'suggestions', label: 'Resume Tips', endpoint: 'resume-suggestions' },
  ];

  const fetchTab = async (endpoint) => {
    setLoading(true);
    setError('');
    try {
      const response = await api.post(`/jobs/${jobId}/assistant/${endpoint}`);
      if (response.data.success) {
        setData(prev => ({ ...prev, [endpoint]: response.data.data }));
      } else {
        setError(response.data.message || 'Failed to generate');
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to generate');
    } finally {
      setLoading(false);
    }
  };

  const handleTabChange = (tab) => {
    setActiveTab(tab.key);
    if (!data[tab.endpoint]) {
      fetchTab(tab.endpoint);
    }
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    alert('Copied to clipboard!');
  };

  return (
    <div className="bg-white rounded-lg shadow p-6">
      <h3 className="text-xl font-bold text-gray-900 mb-4">🤖 AI Application Assistant</h3>

      {/* Tabs */}
      <div className="flex border-b mb-4">
        {tabs.map(tab => (
          <button
            key={tab.key}
            onClick={() => handleTabChange(tab)}
            className={`px-4 py-2 font-medium text-sm transition-colors ${
              activeTab === tab.key
                ? 'text-blue-600 border-b-2 border-blue-600'
                : 'text-gray-600 hover:text-gray-900'
            }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 rounded p-3 mb-4">
          <p className="text-red-700 text-sm">{error}</p>
        </div>
      )}

      {loading && (
        <div className="text-center py-8">
          <p className="text-gray-600">Generating with AI...</p>
        </div>
      )}

      {/* Cover Letter Tab */}
      {activeTab === 'cover-letter' && data['cover-letter'] && (
        <div>
          <div className="flex justify-between items-center mb-2">
            <p className="text-sm text-gray-600">
              {data['cover-letter'].word_count} words • {data['cover-letter'].tone} tone
            </p>
            <button
              onClick={() => copyToClipboard(data['cover-letter'].cover_letter)}
              className="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
            >
              Copy to Clipboard
            </button>
          </div>
          <div className="bg-gray-50 rounded p-4">
            <pre className="text-sm text-gray-800 whitespace-pre-wrap font-sans">
              {data['cover-letter'].cover_letter}
            </pre>
          </div>
        </div>
      )}

      {/* Q&A Tab */}
      {activeTab === 'answers' && data['answers'] && (
        <div className="space-y-4">
          {data['answers'].questions.map((q, idx) => (
            <div key={idx} className="border rounded-lg p-4">
              <p className="font-semibold text-gray-900 mb-2">
                Q: {q.question}
              </p>
              <div className="flex justify-between items-start mb-2">
                <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                  {q.category}
                </span>
                <button
                  onClick={() => copyToClipboard(q.answer)}
                  className="text-xs text-blue-600 hover:underline"
                >
                  Copy
                </button>
              </div>
              <p className="text-sm text-gray-700 whitespace-pre-wrap">{q.answer}</p>
            </div>
          ))}
        </div>
      )}

      {/* Resume Suggestions Tab */}
      {activeTab === 'suggestions' && data['resume-suggestions'] && (
        <div className="space-y-3">
          <p className="text-sm text-gray-600 mb-2">
            {data['resume-suggestions'].total_suggestions} suggestions for the {data['resume-suggestions'].job_title} role:
          </p>
          {data['resume-suggestions'].suggestions.map((s, idx) => (
            <div key={idx} className="border-l-4 border-blue-500 bg-gray-50 rounded p-3">
              <div className="flex items-center gap-2 mb-1">
                <span className={`text-xs font-semibold px-2 py-1 rounded ${
                  s.priority === 'high' ? 'bg-red-100 text-red-800' :
                  s.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                  'bg-gray-100 text-gray-800'
                }`}>
                  {s.priority.toUpperCase()}
                </span>
                <span className="text-xs text-gray-500 capitalize">{s.type.replace('_', ' ')}</span>
              </div>
              <p className="text-sm font-medium text-gray-900">{s.suggestion}</p>
              <p className="text-xs text-gray-600 mt-1 italic">{s.reason}</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}