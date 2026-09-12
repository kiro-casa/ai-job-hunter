import { useState, useEffect } from 'react';
import api from '../services/api';

export default function SettingsPage() {
  const [settings, setSettings] = useState(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [successMessage, setSuccessMessage] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    fetchSettings();
  }, []);

  const fetchSettings = async () => {
    try {
      const response = await api.get('/automation/settings');
      if (response.data.success) {
        setSettings(response.data.data);
      } else {
        setError('Failed to load settings');
      }
    } catch (err) {
      console.error('Settings fetch error:', err);
      setError(err.response?.data?.message || 'Failed to load settings');
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    if (!settings) return;
    
    setSaving(true);
    setError('');
    setSuccessMessage('');

    try {
      const response = await api.patch('/automation/settings', {
        auto_apply_enabled: settings.auto_apply_enabled,
        require_confirmation: settings.require_confirmation,
        min_match_score: parseFloat(settings.min_match_score),
        mandatory_threshold: parseFloat(settings.mandatory_threshold),
        min_confidence: parseFloat(settings.min_confidence),
        max_applications_per_day: parseInt(settings.max_applications_per_day),
      });

      if (response.data.success) {
        setSettings(response.data.data);
        setSuccessMessage('Settings saved successfully!');
        setTimeout(() => setSuccessMessage(''), 3000);
      }
    } catch (err) {
      console.error('Settings save error:', err);
      setError(err.response?.data?.message || 'Failed to save settings');
    } finally {
      setSaving(false);
    }
  };

  const handleChange = (field, value) => {
    if (!settings) return;
    setSettings({ ...settings, [field]: value });
  };

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold text-gray-900 mb-6">Automation Settings</h1>
        <div className="bg-white rounded-lg shadow p-6">
          <p className="text-gray-600">Loading settings...</p>
        </div>
      </div>
    );
  }

  if (!settings) {
    return (
      <div className="max-w-4xl mx-auto">
        <h1 className="text-3xl font-bold text-gray-900 mb-6">Automation Settings</h1>
        <div className="bg-red-50 border border-red-200 rounded-lg p-6">
          <p className="text-red-700">Failed to load settings. Please try refreshing the page.</p>
          <button
            onClick={fetchSettings}
            className="mt-4 bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700"
          >
            Retry
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold text-gray-900 mb-6">Automation Settings</h1>

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

      <div className="bg-white rounded-lg shadow p-6 space-y-6">
        {/* Auto-Apply Toggle */}
        <div className="border-b pb-4">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-lg font-semibold text-gray-900">Auto-Apply</h2>
              <p className="text-sm text-gray-600">Enable automatic application submission</p>
            </div>
            <label className="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                checked={settings.auto_apply_enabled}
                onChange={(e) => handleChange('auto_apply_enabled', e.target.checked)}
                className="sr-only peer"
              />
              <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
          </div>
          {!settings.auto_apply_enabled && (
            <p className="text-xs text-yellow-600 mt-2">
              ⚠️ Auto-apply is currently OFF. Applications will require manual submission.
            </p>
          )}
        </div>

        {/* Confirmation Required */}
        <div>
          <label className="flex items-center">
            <input
              type="checkbox"
              checked={settings.require_confirmation}
              onChange={(e) => handleChange('require_confirmation', e.target.checked)}
              className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <span className="ml-2 text-sm text-gray-700">
              Require final confirmation before submission
            </span>
          </label>
        </div>

        {/* Thresholds */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Minimum Match Score (%)
            </label>
            <input
              type="number"
              value={settings.min_match_score}
              onChange={(e) => handleChange('min_match_score', e.target.value)}
              min="0"
              max="100"
              className="w-full px-3 py-2 border border-gray-300 rounded-md"
            />
            <p className="text-xs text-gray-500 mt-1">Only apply to jobs with match score ≥ this value</p>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Mandatory Requirements Threshold (%)
            </label>
            <input
              type="number"
              value={settings.mandatory_threshold}
              onChange={(e) => handleChange('mandatory_threshold', e.target.value)}
              min="0"
              max="100"
              className="w-full px-3 py-2 border border-gray-300 rounded-md"
            />
            <p className="text-xs text-gray-500 mt-1">Percentage of mandatory requirements that must be met</p>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Minimum Confidence Score
            </label>
            <input
              type="number"
              value={settings.min_confidence}
              onChange={(e) => handleChange('min_confidence', e.target.value)}
              min="0"
              max="1"
              step="0.01"
              className="w-full px-3 py-2 border border-gray-300 rounded-md"
            />
            <p className="text-xs text-gray-500 mt-1">AI confidence threshold (0.0 - 1.0)</p>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Max Applications Per Day
            </label>
            <input
              type="number"
              value={settings.max_applications_per_day}
              onChange={(e) => handleChange('max_applications_per_day', e.target.value)}
              min="1"
              max="100"
              className="w-full px-3 py-2 border border-gray-300 rounded-md"
            />
            <p className="text-xs text-gray-500 mt-1">Rate limit to prevent excessive applications</p>
          </div>
        </div>

        {/* Save Button */}
        <div className="border-t pt-4">
          <button
            onClick={handleSave}
            disabled={saving}
            className="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:bg-gray-400"
          >
            {saving ? 'Saving...' : 'Save Settings'}
          </button>
        </div>
      </div>

      {/* Safety Information */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
        <h3 className="text-lg font-semibold text-blue-900 mb-2">🛡️ Safety Features</h3>
        <ul className="list-disc list-inside space-y-1 text-sm text-blue-800">
          <li>Applications are only submitted when ALL mandatory requirements are met</li>
          <li>Duplicate applications are automatically prevented</li>
          <li>Rate limiting protects against excessive submissions</li>
          <li>Every auto-apply attempt is logged for audit purposes</li>
          <li>The Safety Gate evaluates 9 different checks before allowing submission</li>
        </ul>
      </div>
    </div>
  );
}