# 🚀 AI Job Hunter

A full-stack, AI-powered career assistant that automates and optimizes the job application process. Built with **Laravel 12**, **React 19**, **Tailwind CSS v4**, and **MySQL**.

## 🌟 Key Features

- 📄 **Smart Resume Parsing**: Upload a PDF resume to extract and structure profile, education, experience, and skills.
- 🎯 **Intelligent Matching Engine**: Calculates a transparent, weighted match score (Skills, Experience, Education, Keywords) between your resume and job descriptions.
- 🛡️ **Eligibility & Safety Gate**: A robust decision engine that prevents auto-applying to jobs where mandatory requirements are missing, confidence is low, or rate limits are exceeded.
- 🤖 **AI Application Assistant**: Generates personalized cover letters, Q&A answers, and resume tailoring suggestions based on the specific job.
- 🎤 **Interview Preparation**: Predicts role-specific interview questions with STAR method templates and coaching tips.
- 📊 **Kanban Application Tracker**: Track your pipeline from "Saved" to "Offer" with detailed notes and audit logs.

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.x, MySQL, PHPUnit
- **Frontend**: React 19, Vite, Tailwind CSS v4, Axios, React Router
- **Architecture**: Service-Repository pattern, Dependency Injection, Interface-based AI Provider abstraction (easily swappable between Mock, OpenAI, Anthropic, etc.)
- **Testing**: 20+ Unit and Feature tests covering core matching, eligibility, and safety logic.

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+ & Composer
- Node.js 18+ & npm
- MySQL 8.0+

### Backend Setup
```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

### Testing
```bash
cd backend
php artisan test
```

## 👤 Author
**King Xyro B. Casa**
