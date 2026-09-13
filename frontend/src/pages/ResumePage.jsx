import { useState, useEffect } from 'react';
import api from '../services/api';
import { useAuth } from '../context/AuthContext';

export default function ResumePage() {
  const { user } = useAuth();
  const [file, setFile] = useState(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [resumeData, setResumeData] = useState(null);
  const [successMessage, setSuccessMessage] = useState('');

  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
    setError('');
    setSuccessMessage('');
  };

  const handleUpload = async (e) => {
    e.preventDefault();
    if (!file) {
      setError('Please select a PDF file');
      return;
    }

    setLoading(true);
    setError('');
    setSuccessMessage('');

    const formData = new FormData();
    formData.append('file', file);

    try {
      const response = await api.post('/resumes', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      if (response.data.success) {
        setResumeData(response.data.data);
        setSuccessMessage('Resume parsed successfully! Review the data below.');
        setFile(null);
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to upload resume');
    } finally {
      setLoading(false);
    }
  };

  const handleVerify = async () => {
  if (!resumeData) return;
  
  try {
    const response = await api.post(`/resumes/${resumeData.id}/verify`);
    if (response.data.success) {
      setSuccessMessage('Resume verified! It is now ready for job matching.');
      setResumeData(response.data.data);
    }
  } catch (err) {
    // Show the actual error message
    const errorMessage = err.response?.data?.message || 
                         err.response?.data?.errors || 
                         err.message || 
                         'Failed to verify resume';
    console.error('Verify error:', err.response?.data || err);
    setError(typeof errorMessage === 'object' ? JSON.stringify(errorMessage) : errorMessage);
  }
    };

  return (
    <div className="max-w-4xl mx-auto">
      <h1 className="text-3xl font-bold text-gray-900 mb-6">Resume Management</h1>

      {/* Upload Section */}
      <div className="bg-white rounded-lg shadow p-6 mb-6">
        <h2 className="text-xl font-semibold text-gray-800 mb-4">Upload Resume</h2>
        <form onSubmit={handleUpload} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              PDF Resume (Max 5MB)
            </label>
            <input
              type="file"
              accept=".pdf"
              onChange={handleFileChange}
              className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
            />
          </div>

          {error && (
            <div className="bg-red-50 border border-red-200 rounded p-3">
              <p className="text-red-700 text-sm">{error}</p>
            </div>
          )}

          {successMessage && (
            <div className="bg-green-50 border border-green-200 rounded p-3">
              <p className="text-green-700 text-sm">{successMessage}</p>
            </div>
          )}

          <button
            type="submit"
            disabled={loading || !file}
            className="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
          >
            {loading ? 'Processing with AI...' : 'Upload & Parse'}
          </button>
        </form>
      </div>

      {/* Extracted Data Display */}
      {resumeData && (
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-semibold text-gray-800">Extracted Data</h2>
            {resumeData.status !== 'verified' && (
              <button
                onClick={handleVerify}
                className="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm"
              >
                Verify & Save Profile
              </button>
            )}
            {resumeData.status === 'verified' && (
              <span className="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                Verified
              </span>
            )}
          </div>

          <div className="space-y-6">
            {/* Profile */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 border-b pb-2 mb-2">Profile</h3>
              <p><strong>Name:</strong> {resumeData.profile?.full_name}</p>
              <p><strong>Email:</strong> {resumeData.profile?.email}</p>
              <p><strong>Phone:</strong> {resumeData.profile?.phone}</p>
              <p><strong>Location:</strong> {resumeData.profile?.location}</p>
              <p className="mt-2"><strong>Summary:</strong> {resumeData.profile?.professional_summary}</p>
            </div>

            {/* Experience */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 border-b pb-2 mb-2">Experience</h3>
              {resumeData.experiences?.map((exp, idx) => (
                <div key={idx} className="mb-3">
                  <p className="font-semibold">{exp.job_title} at {exp.company}</p>
                  <p className="text-sm text-gray-700 mt-1">{exp.responsibilities}</p>
                </div>
              ))}
            </div>

            {/* Education */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 border-b pb-2 mb-2">Education</h3>
              {resumeData.educations?.map((edu, idx) => (
                <div key={idx} className="mb-2">
                  <p className="font-semibold">{edu.degree} in {edu.field_of_study}</p>
                  <p className="text-sm text-gray-600">{edu.institution}</p>
                </div>
              ))}
            </div>

            {/* Skills */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 border-b pb-2 mb-2">Skills</h3>
              <div className="flex flex-wrap gap-2">
                {resumeData.skills?.map((skill, idx) => (
                  <span 
                    key={idx} 
                    className="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded"
                  >
                    {skill.name} ({skill.pivot.proficiency_level})
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}