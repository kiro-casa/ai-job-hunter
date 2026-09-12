import { useState, useEffect } from 'react';
import api from '../services/api';

export default function InterviewPreparation({ jobId }) {
  const [loading, setLoading] = useState(false);
  const [data, setData] = useState(null);
  const [error, setError] = useState('');
  const [filter, setFilter] = useState('all');
  const [expandedQuestion, setExpandedQuestion] = useState(null);

  useEffect(() => {
    fetchPreparation();
  }, [jobId]);

  const fetchPreparation = async () => {
    setLoading(true);
    setError('');
    try {
      const response = await api.post(`/jobs/${jobId}/interview/prepare`);
      if (response.data.success) {
        setData(response.data.data);
      } else {
        setError(response.data.message || 'Failed to generate');
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to generate');
    } finally {
      setLoading(false);
    }
  };

  const filteredQuestions = data?.questions.filter(q => 
    filter === 'all' || q.category === filter
  ) || [];

  const getDifficultyColor = (difficulty) => {
    return {
      easy: 'bg-green-100 text-green-800',
      medium: 'bg-yellow-100 text-yellow-800',
      hard: 'bg-red-100 text-red-800',
    }[difficulty] || 'bg-gray-100 text-gray-800';
  };

  const getLikelihoodColor = (likelihood) => {
    return {
      very_high: 'bg-red-100 text-red-800 border-red-300',
      high: 'bg-orange-100 text-orange-800 border-orange-300',
      medium: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    }[likelihood] || 'bg-gray-100 text-gray-800';
  };

  const getCategoryIcon = (category) => {
    return {
      behavioral: '💬',
      technical: '💻',
      situational: '🎯',
      company: '🏢',
    }[category] || '❓';
  };

  const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
    alert('Copied to clipboard!');
  };

  if (loading) {
    return (
      <div className="bg-white rounded-lg shadow p-6">
        <h3 className="text-xl font-bold text-gray-900 mb-4">🎤 Interview Preparation</h3>
        <p className="text-gray-600">Generating personalized interview questions...</p>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-white rounded-lg shadow p-6">
        <h3 className="text-xl font-bold text-gray-900 mb-4">🎤 Interview Preparation</h3>
        <div className="bg-red-50 border border-red-200 rounded p-3">
          <p className="text-red-700 text-sm">{error}</p>
        </div>
      </div>
    );
  }

  if (!data) return null;

  return (
    <div className="bg-white rounded-lg shadow p-6">
      <div className="flex justify-between items-start mb-4">
        <div>
          <h3 className="text-xl font-bold text-gray-900">🎤 Interview Preparation</h3>
          <p className="text-sm text-gray-600 mt-1">
            {data.total_questions} questions for {data.job_title} at {data.company}
          </p>
        </div>
      </div>

      {/* Preparation Summary */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
        <p className="text-sm text-blue-900">{data.preparation_summary}</p>
      </div>

      {/* Category Filters */}
      <div className="flex gap-2 mb-4 flex-wrap">
        {['all', 'behavioral', 'technical', 'situational', 'company'].map(cat => (
          <button
            key={cat}
            onClick={() => setFilter(cat)}
            className={`px-3 py-1 rounded text-sm font-medium transition-colors ${
              filter === cat
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`}
          >
            {cat === 'all' ? 'All' : cat.charAt(0).toUpperCase() + cat.slice(1)}
          </button>
        ))}
      </div>

      {/* Questions List */}
      <div className="space-y-3">
        {filteredQuestions.map((q, idx) => (
          <div key={idx} className="border rounded-lg overflow-hidden">
            <div
              onClick={() => setExpandedQuestion(expandedQuestion === idx ? null : idx)}
              className="p-4 cursor-pointer hover:bg-gray-50"
            >
              <div className="flex items-start justify-between gap-2">
                <div className="flex-1">
                  <div className="flex items-center gap-2 mb-2 flex-wrap">
                    <span className="text-lg">{getCategoryIcon(q.category)}</span>
                    <span className={`text-xs font-semibold px-2 py-1 rounded ${getDifficultyColor(q.difficulty)}`}>
                      {q.difficulty.toUpperCase()}
                    </span>
                    <span className={`text-xs font-semibold px-2 py-1 rounded border ${getLikelihoodColor(q.likelihood)}`}>
                      {q.likelihood.replace('_', ' ').toUpperCase()} likelihood
                    </span>
                    <span className="text-xs text-gray-500 capitalize">
                      {q.category}
                    </span>
                  </div>
                  <p className="font-medium text-gray-900">{q.question}</p>
                </div>
                <span className="text-gray-400 text-xl">
                  {expandedQuestion === idx ? '−' : '+'}
                </span>
              </div>
            </div>

            {expandedQuestion === idx && (
              <div className="border-t bg-gray-50 p-4 space-y-4">
                {/* STAR Template */}
                {q.star_template && (
                  <div>
                    <h4 className="font-semibold text-sm text-gray-800 mb-2">📋 STAR Method Template:</h4>
                    <div className="grid grid-cols-2 gap-2">
                      {Object.entries(q.star_template).map(([key, value]) => (
                        <div key={key} className="bg-white rounded p-2 border">
                          <p className="text-xs font-bold text-blue-700 uppercase">{key}</p>
                          <p className="text-xs text-gray-600 mt-1">{value}</p>
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {/* Coaching Tips */}
                <div>
                  <h4 className="font-semibold text-sm text-gray-800 mb-2">💡 Coaching Tips:</h4>
                  <ul className="space-y-1">
                    {q.coaching_tips.map((tip, i) => (
                      <li key={i} className="text-sm text-gray-700 flex items-start">
                        <span className="text-green-600 mr-2">✓</span>
                        {tip}
                      </li>
                    ))}
                  </ul>
                </div>

                {/* Sample Answer */}
                {q.sample_answer && (
                  <div>
                    <div className="flex justify-between items-center mb-2">
                      <h4 className="font-semibold text-sm text-gray-800">📝 Sample Answer:</h4>
                      <button
                        onClick={() => copyToClipboard(q.sample_answer)}
                        className="text-xs text-blue-600 hover:underline"
                      >
                        Copy
                      </button>
                    </div>
                    <div className="bg-white rounded p-3 border">
                      <p className="text-sm text-gray-700 whitespace-pre-wrap">{q.sample_answer}</p>
                    </div>
                    <p className="text-xs text-gray-500 italic mt-1">
                      ⚠️ Use this as inspiration, not a script. Personalize it with your own experiences.
                    </p>
                  </div>
                )}
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}