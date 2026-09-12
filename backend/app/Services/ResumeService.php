<?php

namespace App\Services;

use App\Models\Resume;
use App\Models\ResumeProfile;
use App\Models\ResumeEducation;
use App\Models\ResumeExperience;
use App\Models\ResumeProject;
use App\Models\ResumeCertification;
use App\Models\ResumeLanguage;
use App\Models\Skill;
use App\Services\AIService;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

class ResumeService
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function processUpload(array $fileData, int $userId): array
    {
        DB::beginTransaction();
        $resume = null;

        try {
            $file = $fileData['file'];
            
            // 1. Save the file
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('resumes', $filename, 'local');

            // 2. Get the correct full path
            $fullPath = Storage::disk('local')->path($filePath);

            // 3. Try to extract text, but don't fail if it doesn't work
            $rawText = '';
            try {
                if (file_exists($fullPath)) {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($fullPath);
                    $rawText = $pdf->getText();
                }
            } catch (Exception $e) {
                // Log the error but continue
                Log::warning('PDF text extraction failed: ' . $e->getMessage());
                $rawText = '[PDF text extraction not available - using mock data]';
            }

            // 4. Create Resume record
            $resume = Resume::create([
                'user_id' => $userId,
                'file_path' => $filePath,
                'original_filename' => $file->getClientOriginalName(),
                'raw_text' => $rawText,
                'status' => 'extracting',
            ]);

            // 5. Call AI Service to parse structured data
            // (MockAIProvider will return hardcoded data regardless of rawText)
            $parsedData = $this->aiService->extractResumeData($rawText);

            // 6. Save structured data
            $this->saveStructuredData($resume, $parsedData);

            // 7. Update status
            $resume->update(['status' => 'extracted']);

            DB::commit();

            return [
                'success' => true,
                'data' => $resume->load([
                    'profile', 'educations', 'experiences', 'projects', 
                    'certifications', 'languages', 'skills'
                ]),
                'message' => 'Resume parsed successfully',
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            if ($resume) {
                $resume->update(['status' => 'failed']);
            }

            Log::error('Resume processing failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to process resume: ' . $e->getMessage(),
            ];
        }
    }

    protected function saveStructuredData(Resume $resume, array $data): void
    {
        // Save Profile
        if (isset($data['profile'])) {
            ResumeProfile::create(array_merge($data['profile'], [
                'resume_id' => $resume->id,
                'user_id' => $resume->user_id,
            ]));
        }

        // Save Education
        if (isset($data['education'])) {
            foreach ($data['education'] as $edu) {
                ResumeEducation::create(array_merge($edu, ['resume_id' => $resume->id]));
            }
        }

        // Save Experience
        if (isset($data['experience'])) {
            foreach ($data['experience'] as $exp) {
                ResumeExperience::create(array_merge($exp, ['resume_id' => $resume->id]));
            }
        }

        // Save Projects
        if (isset($data['projects'])) {
            foreach ($data['projects'] as $proj) {
                ResumeProject::create(array_merge($proj, ['resume_id' => $resume->id]));
            }
        }

        // Save Certifications
        if (isset($data['certifications'])) {
            foreach ($data['certifications'] as $cert) {
                ResumeCertification::create(array_merge($cert, ['resume_id' => $resume->id]));
            }
        }

        // Save Languages
        if (isset($data['languages'])) {
            foreach ($data['languages'] as $lang) {
                ResumeLanguage::create(array_merge($lang, ['resume_id' => $resume->id]));
            }
        }

        // Save Skills
        if (isset($data['skills'])) {
            foreach ($data['skills'] as $skillData) {
                $skill = Skill::firstOrCreate(
                    ['normalized_name' => strtolower(trim($skillData['name']))],
                    [
                        'name' => $skillData['name'],
                        'category' => $skillData['category'] ?? null,
                    ]
                );

                $resume->skills()->attach($skill->id, [
                    'proficiency_level' => $skillData['proficiency_level'] ?? null,
                ]);
            }
        }
    }
}