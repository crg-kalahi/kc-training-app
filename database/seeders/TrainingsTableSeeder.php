<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conf\KeyResourcePerson;
use App\Models\Conf\KeyTraining;
use App\Models\Conf\Learning;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TrainingsTableSeeder extends Seeder
{
    public function run()
    {
        // Get UUIDs from the related tables
        $keyTrainings = KeyTraining::all()->pluck('id')->toArray();
        $keyLearnings = Learning::all()->pluck('id')->toArray();
        $keyResourcePeople = KeyResourcePerson::all()->pluck('id')->toArray();

        // Define a list of elegant training titles (ensure this has at least 100 unique titles)
        $trainingTitles = [
            'Advanced Leadership Development',
            'Innovative Problem Solving Techniques',
            'Effective Communication in the Workplace',
            'Building Resilience for Personal Growth',
            'Strategic Thinking for Managers',
            'Mastering Time Management Skills',
            'Teamwork and Collaboration Excellence',
            'Financial Planning and Investment Strategies',
            'Sustainable Practices in Modern Business',
            'Public Speaking and Presentation Mastery',
            // Add more unique titles to ensure uniqueness across 100 entries
            'Digital Transformation in Business',
            'Conflict Resolution Strategies for Leaders',
            'Agile Project Management Fundamentals',
            'Emotional Intelligence in the Workplace',
            'Introduction to Data Science for Business',
            'Negotiation Skills for Effective Management',
            'Cross-Cultural Communication for Global Teams',
            'Building a High-Performance Team',
            'Creative Problem Solving in Business',
            'Entrepreneurship and Innovation',
            'Organizational Behavior and Development',
            'Change Management for Leaders',
            'Customer Relationship Management (CRM)',
            'Effective Delegation for Managers',
            'Leadership in Crisis Management',
            'Design Thinking for Product Development',
            'The Art of Feedback and Coaching',
            'Building a Personal Brand in the Workplace',
            'Time Management for Executives',
            'Marketing Strategies for the Digital Age',
            'Data-Driven Decision Making for Managers',
            'Employee Engagement and Retention Strategies',
            'Workplace Diversity and Inclusion',
            'Building Emotional Resilience for Leaders',
            'Social Media Strategies for Business',
            'Risk Management in Business Operations',
            'Financial Forecasting and Budgeting',
            'Team Leadership and Motivation',
            'The Science of Negotiation',
            'Developing a Growth Mindset',
            'Crisis Communication Strategies',
            'Organizational Leadership in the 21st Century',
            'Building a Culture of Innovation',
            'Leadership Communication Skills',
            'Project Risk Assessment and Management',
            'Sustainable Business Practices',
            'Human Resource Management Fundamentals',
            'Workplace Wellness and Mental Health',
            'Effective Decision Making for Executives',
            'Harnessing the Power of Data Analytics',
            'Collaborative Leadership Styles',
            'Innovation in Business Strategy',
            'Maximizing Team Potential',
            'The Psychology of Leadership',
            'Customer-Centric Business Models',
            'Strategic Marketing and Branding',
            'Navigating Change in Organizations',
            'Corporate Social Responsibility (CSR)',
            'Building a Learning Organization',
            'Advanced Negotiation Tactics',
            'Managing Remote Teams Effectively',
            'Corporate Governance and Ethics',
            'Financial Risk and Compliance Management',
            'Sales Strategies for Business Growth',
            'Personal Effectiveness for Leaders',
            'Leading Organizational Change',
            'Executive Leadership Strategies',
            'Business Process Improvement Strategies',
            'Leading through Influence',
            'Innovation Management in Business',
            'Building Trust in Leadership',
            'Leading High-Performance Teams',
            'Cultural Intelligence for Global Leadership',
            'Strategic Leadership in Complex Environments',
            'Mentoring and Coaching for Leaders',
            'Digital Marketing Strategies',
            'Conflict Management and Mediation',
            'Talent Acquisition and Retention',
            'Leadership in Digital Transformation',
            'Managing Organizational Behavior',
            'Understanding Business Analytics',
            'Leadership Development for Future Leaders',
            'Crisis Management and Recovery',
            'Corporate Strategy and Planning',
            'Leadership Agility in Uncertain Times',
            'Global Leadership Strategies',
            'The Role of Innovation in Business Success',
            'Driving Business Excellence through Leadership',
            'The Future of Leadership in Business',
            'Coaching for Organizational Success',
            'Managing Stakeholder Relationships',
            'Business Ethics and Integrity',
            'Leadership for Sustainable Development',
            'Managing Work-Life Balance as a Leader',
            'Human Capital Management',
            'Developing High-Impact Leadership Skills',
            'The Power of Influence in Leadership',
            'Strategic HR Management',
            'Business Intelligence and Analytics for Managers',
            'Operational Excellence and Continuous Improvement',
            'Leading Innovation and Change',
            'Financial Management for Leaders',
            'Leadership Skills for New Managers',
            'Building Strong Workplace Relationships',
            'Sustainability and Green Business Practices',
            'Leading with Empathy and Emotional Intelligence',
            'Mastering Executive Presence',
            'Developing Future Leaders',
            'Innovation and Entrepreneurship for Leaders',
            'Effective Conflict Resolution in Teams',
            'Leadership in the Digital Era'
        ];

        // Shuffle the titles to ensure uniqueness and randomness
        shuffle($trainingTitles);

        // Ensure that we only use the first 100 titles (in case there are more than 100 titles)
        $trainingTitles = array_slice($trainingTitles, 0, 100);

        // Generate 100 trainings entries
        for ($i = 0; $i < 100; $i++) {
            // Generate random month and day for the 'date_from' between January and December
            $monthFrom = rand(1, 12);
            $dayFrom = rand(1, 28); // To avoid issues with days in different months, limit to 28
        
            // Generate the 'date_from' using a random month and day
            $dateFrom = Carbon::create(Carbon::now()->year, $monthFrom, $dayFrom);
        
            // Generate 'date_to' to be after 'date_from' with a range of 1 to 30 days
            $dateTo = $dateFrom->copy()->addDays(rand(1, 30));
        
            DB::table('trainings')->insert([
                'id' => Str::uuid(), // Generate UUID for the training ID
                'old_id' => $i + 1,
                'title' => $trainingTitles[$i], // Unique title
                'venue' => 'Venue ' . ($i + 1),
                'key_trainings' => $keyTrainings[array_rand($keyTrainings)], // Random UUID from KeyTraining
                'key_learnings' => $keyLearnings[array_rand($keyLearnings)], // Random UUID from Learning
                'key_rp' => $keyResourcePeople[array_rand($keyResourcePeople)], // Random UUID from KeyResourcePerson
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'rso_number' => 'RSO-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'encoded_by' => 'user_' . rand(1, 10), // Assuming you have users with ids 1-10
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null // or a random date for soft delete
            ]);
        }
    }
}
