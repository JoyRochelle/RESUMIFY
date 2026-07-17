<?php

return [

    'auth' => [

        'fields' => [
            'email' => 'EMAIL',
            'email_address' => 'EMAIL ADDRESS',
            'password' => 'PASSWORD',
            'confirm' => 'CONFIRM',
            'full_name' => 'FULL NAME',
        ],

        'processing' => 'Processing...',
        'error_summary' => 'Please review the highlighted fields.',
        'or_continue_with' => 'OR CONTINUE WITH',

        'social' => [
            'in_development' => 'In development',
            'linkedin_unavailable' => 'LinkedIn sign-in is still in development. Please continue with Google or email.',
        ],

        'tabs' => [
            'login' => 'Login',
            'sign_up' => 'Sign Up',
        ],

        'legal' => [
            'terms' => 'TERMS & CONDITIONS',
            'privacy' => 'PRIVACY POLICY',
            'help' => 'HELP CENTER',
            'close' => 'Close',
        ],

        'marketing' => [
            'headline' => 'One Account, One Adapted Career ✨',
            'subtitle' => 'Build professional resumes and boost your ATS score in minutes.',
            'ats_match' => 'ATS MATCH',
            'ai_optimization' => 'AI Optimization Active',
        ],

        'login' => [
            'title' => 'Login | Resumify',
            'heading' => 'Login to Your Account',
            'subtitle' => 'Welcome back to your career journey.',
            'forgot_password' => 'Forgot Password?',
            'remember_me' => 'Remember me for 30 days',
            'submit' => 'Login',
        ],

        'register' => [
            'title' => 'Register | Resumify',
            'heading' => 'Create Your Account',
            'subtitle' => 'Start your professional journey in minutes.',
            'terms_prefix' => 'I agree to the',
            'terms_of_service' => 'Terms of Service',
            'and' => 'and',
            'privacy_policy' => 'Privacy Policy',
            'submit' => 'Sign Up',
            'have_account' => 'Already have an account?',
            'sign_in' => 'Sign In',
        ],

        'forgot_password' => [
            'title' => 'Forgot Password | Resumify',
            'heading' => 'Forgot Password',
            'subtitle' => "Enter your email and we'll send you a link to reset your password.",
            'submit' => 'Send Password Reset Link',
            'back_to_login' => 'Back to Login',
            'modal_heading' => 'Forgot Password?',
            'modal_subtitle' => "Enter your email address and we'll send you a link to reset your password.",
            'modal_placeholder' => 'Enter your email',
            'modal_submit' => 'Send Link',
        ],

        'reset_password' => [
            'title' => 'Reset Password | Resumify',
            'new_password' => 'New Password',
            'confirm_password' => 'Confirm Password',
            'submit' => 'Update Password',
        ],

        'verify_email' => [
            'title' => 'Verify Email | Resumify',
            'heading' => 'Email Sent<br>Successfully!',
            'body' => "We've sent a verification link to your email address. Please check your inbox (and spam folder) to proceed.",
            'back_to_login' => 'Back to Login',
            'no_email_question' => "Didn't receive an email?",
            'resend' => 'Resend Link',
            'sending' => 'Sending...',
        ],

        'legal_modals' => [
            'privacy' => [
                'title' => 'Privacy Policy',
                'html' => '<p class="mb-4">At Resumify, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">1. Information We Collect</h4>
                    <p class="mb-4">We may collect personal identification information from Users in a variety of ways, including, but not limited to, when Users visit our site, register on the site, place an order, and in connection with other activities, services, features or resources we make available on our Site.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">2. How We Use Collected Information</h4>
                    <p class="mb-4">Resumify may collect and use Users\' personal information for the following purposes:</p>
                    <ul class="list-disc pl-5 mb-4 space-y-1">
                        <li>To improve customer service</li>
                        <li>To personalize user experience</li>
                        <li>To process payments securely</li>
                        <li>To send periodic emails regarding your manuscript updates</li>
                    </ul>
                    <h4 class="font-bold text-primary mb-2 mt-6">3. Data Security</h4>
                    <p>We adopt appropriate data collection, storage and processing practices and security measures to protect against unauthorized access, alteration, disclosure or destruction of your personal information, username, password, transaction information and data stored on our Site.</p>',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'html' => '<p class="mb-4">Welcome to Resumify. By accessing this website, we assume you accept these terms and conditions. Do not continue to use Resumify if you do not agree to take all of the terms and conditions stated on this page.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">1. License</h4>
                    <p class="mb-4">Unless otherwise stated, Resumify and/or its licensors own the intellectual property rights for all material on Resumify. All intellectual property rights are reserved. You may access this from Resumify for your own personal use subjected to restrictions set in these terms and conditions.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">2. User Accounts</h4>
                    <p class="mb-4">When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">3. Limitation of Liability</h4>
                    <p>In no event shall Resumify, nor any of its officers, directors and employees, shall be held liable for anything arising out of or in any way connected with your use of this Website whether such liability is under contract.</p>',
            ],
            'help' => [
                'title' => 'Help Center',
                'html' => '<p class="mb-6 text-[15px]">We would love to hear from you. If you have any questions, concerns, or feedback regarding Resumify, please reach out to our support team.</p>

                    <div class="bg-primary/5 p-4 rounded-xl mb-4 border border-primary/10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-secondary">mail</span>
                            <span class="font-bold text-primary">Email Support</span>
                        </div>
                        <p class="text-primary/70 ml-9">hello@resumify.com<br>support@resumify.com</p>
                    </div>

                    <div class="bg-primary/5 p-4 rounded-xl mb-6 border border-primary/10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-secondary">location_on</span>
                            <span class="font-bold text-primary">Office Headquarters</span>
                        </div>
                        <p class="text-primary/70 ml-9">123 Innovation Drive<br>Tech District, San Francisco<br>CA 94105, United States</p>
                    </div>

                    <p class="text-sm italic text-primary/60">Our support team usually responds within 24-48 business hours.</p>',
            ],
        ],

    ],

    'nav' => [
        'dashboard' => 'Dashboard',
        'manuscripts' => 'Manuscripts',
        'ats_analyzer' => 'ATS Analyzer',
        'ats_short' => 'ATS',
        'interview' => 'Interview',
        'settings' => 'Settings',
        'help' => 'Help',
        'upgrade_quota' => 'Upgrade Quota',
        'language' => 'Language',
        'logout' => 'Log Out',
        'more' => 'More',
    ],

    'dashboard' => [
        'welcome' => 'Welcome,',
        'resume_quota' => ':used/:limit Resumes Created',
        'unlimited' => 'Unlimited',
        'your_resumes' => 'Your Resumes',
        'untitled_resume' => 'Untitled Resume',
        'start_new_manuscript' => 'Start New Manuscript',
        'start_new_manuscript_desc' => 'Create your professional career narrative in minutes.',
        'resume_limit_reached' => 'Resume Limit Reached',
        'resume_limit_reached_desc' => 'Basic includes 1 resume. Upgrade from the plan card above for unlimited resumes.',
        'daily_tip_label' => 'DAILY TIP',
        'daily_tip' => 'Use strong action verbs to give weight to your professional narrative.',
        'ats_insight_label' => 'ATS Analyzer',
        'ats_insight_prefix' => 'Check how well your resume matches a job description with our',
        'ats_insight_link' => 'ATS Analyzer',
        'ats_insight_suffix' => '— get a keyword score and actionable suggestions in seconds.',
        'delete_modal' => [
            'title' => 'Delete Resume',
            'body' => 'Are you sure you want to delete this resume? This action cannot be undone.',
            'cancel' => 'Cancel',
            'confirm' => 'Yes, Delete',
            'close_aria' => 'Close delete confirmation',
        ],
        'rename_modal' => [
            'title' => 'Rename Resume',
            'label' => 'Resume Title',
            'placeholder' => 'Enter resume title...',
            'cancel' => 'Cancel',
            'save' => 'Save',
            'close_aria' => 'Close rename dialog',
        ],
    ],

    'common' => [
        'fix_following_fields' => 'Please fix the following fields:',
        'footer_copyright' => '© 2026 Resumify - Curated with Integrity',
    ],

    'manuscripts_page' => [
        'title' => 'Your Manuscripts',
        'subtitle' => 'Manage your existing resumes or create a new one tailored to your target job.',
    ],

    'plan_badge' => [
        'premium' => 'Premium Member',
        'basic' => 'Basic Member',
    ],

    'quota_status' => [
        'upgrade' => 'Upgrade',
        'ai_credits' => 'AI Credits',
        'used_suffix' => 'used',
        'resumes' => 'Resumes',
        'created_suffix' => 'created',
    ],

    'btn_create' => [
        'label' => 'Create New Resume',
        'unlimited_title' => 'Unlimited resumes',
        'unlimited_desc' => 'Basic includes 1 resume. Premium unlocks unlimited resumes for every role you are targeting.',
    ],

    'resume_card' => [
        'edit_manuscript' => 'Edit Manuscript',
        'last_edited' => 'Last edited: :date',
        'edit' => 'Edit',
        'edit_aria' => 'Edit :title',
        'rename' => 'Rename Resume',
        'rename_aria' => 'Rename :title',
        'duplicate' => 'Duplicate Resume',
        'duplicate_aria' => 'Duplicate :title',
        'delete' => 'Delete Resume',
        'delete_aria' => 'Delete :title',
    ],

    'resume' => [
        'index' => [
            'title' => 'My Resumes',
            'new_resume' => 'New Resume',
            'updated' => 'Updated',
            'no_template' => 'No template',
            'empty' => [
                'title' => 'Create your first resume',
                'description' => 'Start with a template, then tailor your content for the role you want.',
                'action' => 'Create Resume',
            ],
            'view' => 'View',
            'edit' => 'Edit',
            'duplicate' => 'Duplicate',
            'duplicating' => 'Duplicating...',
            'delete' => 'Delete',
            'cancel' => 'Cancel',
            'confirm' => 'Confirm',
            'deleting' => 'Deleting...',
        ],
        'create' => [
            'title' => 'Create Resume',
            'resume_title_label' => 'Resume Title',
            'title_placeholder' => 'e.g. Senior Product Designer',
            'title_hint' => 'Use a title that tells you which role this resume targets.',
            'choose_template' => 'Choose Template',
            'select_template' => 'Select a template',
            'template_hint' => 'You can change the template later from the manuscript editor.',
            'premium_suffix' => '(Premium)',
            'cancel' => 'Cancel',
            'creating' => 'Creating...',
            'submit' => 'Create Resume',
        ],
        'edit' => [
            'title' => 'Edit Resume',
            'details_heading' => 'Resume Details',
            'title_label' => 'Title',
            'make_public' => 'Make Public',
            'make_public_desc' => 'Allow this resume to be available from public preview links.',
            'saving' => 'Saving...',
            'save_changes' => 'Save Changes',
            'sections_heading' => 'Sections',
            'section_title_label' => 'Section Title',
            'content_json_label' => 'Content JSON',
            'updating' => 'Updating...',
            'update_section' => 'Update Section',
        ],
        'show' => [
            'edit' => 'Edit',
            'template' => 'Template',
            'visibility' => 'Visibility',
            'public' => 'Public',
            'private' => 'Private',
            'updated' => 'Updated',
            'duplicate' => 'Duplicate',
            'duplicating' => 'Duplicating...',
            'delete' => 'Delete',
            'cancel' => 'Cancel',
            'confirm' => 'Confirm',
            'deleting' => 'Deleting...',
            'no_content' => 'No content yet.',
            'empty_title' => 'No sections yet',
            'empty_description' => 'Open the editor to add the core sections for this resume.',
            'open_editor' => 'Open Editor',
        ],
    ],

    'editor' => [
        'title_prefix' => 'Editor: ',
        'tailor_cv' => 'Tailor CV',
        'preview' => 'Preview',
        'download_pdf' => 'Download PDF',
        'premium_pdf_title' => 'Premium PDF export',
        'premium_pdf_desc' => 'Export polished, high-quality PDFs for applications and recruiter sharing.',
        'tab_edit' => 'Edit',
        'tab_preview' => 'Preview',
        'add_optional_section' => 'Add Optional Section',
        'add_certifications' => '+ Certifications',
        'add_projects' => '+ Projects',
        'add_languages' => '+ Languages',
        'ats_score' => 'ATS Score',
        'minimize' => 'Minimize',
        'maximize' => 'Maximize',
        'layout' => 'Layout',
        'history' => 'History',
        'select_template_title' => 'Select Template',
        'close_template_selection' => 'Close template selection',
        'premium_badge' => 'Premium',
        'locked_premium_template' => 'Locked Premium Template',
        'upgrade_to_apply_layout' => 'Upgrade to apply this layout to your resume.',
        'upgrade_to_unlock' => 'Upgrade to Unlock',
        'use_template' => 'Use Template',
        'use_x_template_aria' => 'Use :name template',
        'refine_with_ai' => 'Refine with AI',
        'close_ai_refinement' => 'Close AI refinement',
        'generating_bullet_points' => 'Generating optimized bullet points...',
        'tailored_cv_versions' => 'Tailored CV Versions',
        'close_tailored_versions' => 'Close tailored CV versions',
        'generate_tailored_versions' => 'Generate Tailored Versions',
        'generate_versions_desc' => 'We will generate 3 distinct CV versions tailored to your target job: Leadership, Technical, and Ownership angles. This uses 3 AI credits.',
        'generate_versions_button' => 'Generate Versions',
        'crafting_versions' => 'Crafting tailored CV versions...',
        'crafting_versions_desc' => 'This usually takes about 10-20 seconds.',
        'cv_history' => 'CV History',
        'close_cv_history' => 'Close CV history',
        'loading_ellipsis' => 'Loading…',
        'apply_version_title' => 'Apply this version?',
        'apply_version_desc' => 'This will overwrite your current CV content. A backup of your current content is saved to History, so you can restore it later.',
        'cancel' => 'Cancel',
        'apply_and_overwrite' => 'Apply & Overwrite',

        'js' => [
            'photo_too_large' => 'Maximum photo size is 2MB. Please choose a smaller file.',
            'generating' => 'Generating...',
            'premium_required_pdf' => 'Premium is required for PDF export.',
            'download_pdf_failed' => 'Failed to download PDF. Please try again.',
            'premium_required_template' => 'Premium is required for this template.',
            'change_template_failed' => 'Failed to change template.',
            'no_resume_for_template' => 'No resume available to update template.',
            'saving' => 'Saving…',
            'saved_at_prefix' => 'Saved',
            'changes_saved' => 'Changes saved!',
            'save_failed' => 'Failed to save — please retry.',
            'network_error_not_saved' => 'Network error — changes not saved.',
            'confirm_remove_section' => 'Are you sure you want to completely remove this section?',
            'delete_section_failed' => 'Failed to delete section.',
            'network_error' => 'Network error.',
            'no_target_job' => 'No Target Job',
            'keyword_match' => 'Keyword Match',
            'refine_min_length' => 'Please write at least a few words before refining.',
            'refine_failed' => 'Failed to refine bullet.',
            'connection_error' => 'An error occurred while connecting to the server.',
            'job_description_min_length' => 'Please provide a detailed Target Job Description (at least 50 characters) in the Target Job section first.',
            'version_warning_prefix' => "Please check this version — some details weren't found in your original CV: ",
            'angle_labels' => [
                'leadership' => 'Leadership',
                'technical' => 'Technical',
                'ownership' => 'Ownership',
            ],
            'angle_suffix' => 'Angle',
            'version_emphasizes' => 'This version emphasizes :angle aspects of your experience, perfectly tailored for the provided job description.',
            'apply_this_version' => 'Apply this version',
            'preview_button' => 'Preview',
            'download_pdf_button' => 'Download PDF',
            'generate_versions_failed' => 'Failed to generate versions.',
            'apply_version_warning_prefix' => "Heads up — some details weren't found in your original CV: ",
            'apply_version_warning_suffix' => 'Review before applying.',
            'version_applied' => 'Version applied! Reloading...',
            'apply_version_failed' => 'Failed to apply version.',
            'version_not_applied' => 'Network error — version not applied.',
            'history_reason_chameleon_apply' => 'Before applying a tailored version',
            'history_reason_pre_restore' => 'Before restoring an earlier version',
            'history_reason_snapshot' => 'Snapshot',
            'loading' => 'Loading…',
            'no_history' => 'No history yet.',
            'restore' => 'Restore',
            'history_load_failed' => 'Failed to load history.',
            'confirm_restore' => 'Restore this version? Your current content will be snapshotted first so you can undo this too.',
            'restored' => 'Restored! Reloading...',
            'restore_failed' => 'Failed to restore.',
            'restore_network_error' => 'Network error — restore failed.',
        ],

        'placeholder' => [
            'role_location' => 'Senior Product Designer • San Francisco, CA',
            'professional_summary_heading' => 'Professional Summary',
            'summary_body' => 'Accomplished Product Designer with 8+ years of experience crafting intuitive digital experiences for high-growth tech companies. Expertise in systems thinking, accessibility-first design, and bridge-building between engineering and design teams.',
            'experience_heading' => 'Experience',
            'job_title_1' => 'Senior Product Designer',
            'job_desc_1' => "Leading design systems for the world's most productive software teams. Crafting high-fidelity components and maintaining visual consistency across mobile and desktop platforms.",
            'job_title_2' => 'Product Designer',
            'job_desc_2' => 'Focused on the guest booking experience and internationalization of the design system. Reduced checkout friction by 12% through iterative testing and accessible UI patterns.',
            'education_heading' => 'Education',
            'degree' => 'BFA in Interaction Design',
            'expertise_heading' => 'Expertise',
            'tag_design_systems' => 'Design Systems',
            'tag_accessibility' => 'Accessibility',
            'tag_prototyping' => 'Prototyping',
            'page_of' => ':current of :total',
        ],

        'sections' => [
            'target_job' => [
                'title' => 'Target Job',
                'job_title' => 'Target Job Title',
                'job_title_placeholder' => 'e.g. Senior Software Engineer',
                'job_company' => 'Target Company',
                'job_company_placeholder' => 'e.g. Acme Corp',
                'job_description' => 'Job Description',
                'job_description_placeholder' => 'Paste the job description here to see how well your resume matches...',
            ],
            'personal_info' => [
                'title' => 'Personal Info',
                'full_name' => 'Full Name',
                'full_name_placeholder' => 'e.g. John Doe',
                'professional_title' => 'Professional Title',
                'professional_title_placeholder' => 'e.g. Senior Product Designer',
                'email' => 'Email',
                'phone_number' => 'Phone Number',
                'phone_placeholder' => '812 xxxx xxxx',
                'location' => 'Location',
                'professional_summary' => 'Professional Summary',
                'summary_placeholder' => 'Write 2–4 sentences about your background, key skills, and career goals...',
                'refine' => 'Refine',
                'profile_photo' => 'Profile Photo',
                'change_photo' => 'Change Photo',
                'upload_photo' => 'Upload Photo',
                'remove_photo' => 'Remove photo',
                'photo_hint' => 'JPG, PNG, WebP · Max 2MB',
            ],
            'work_experience' => [
                'title' => 'Work Experience',
                'job_title' => 'Job Title',
                'job_title_placeholder' => 'e.g. Software Engineer',
                'company' => 'Company',
                'company_placeholder' => 'e.g. Acme Corp',
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
                'end_date_hint' => 'Leave blank if current',
                'description' => 'Description',
                'description_placeholder' => 'Describe your key responsibilities and achievements...',
                'refine' => 'Refine',
                'add' => 'Add Experience',
            ],
            'education' => [
                'title' => 'Education',
                'degree' => 'Degree/Course',
                'degree_placeholder' => 'e.g. Bachelor of Science',
                'school' => 'School/University',
                'school_placeholder' => 'e.g. University of Indonesia',
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
                'end_date_hint' => 'Leave blank if current',
                'additional_info' => 'Additional Info',
                'add' => 'Add Education',
            ],
            'skills' => [
                'title' => 'Skills',
                'skill_name' => 'Skill Name',
                'proficiency_level' => 'Proficiency Level',
                'select_level' => 'Select level',
                'add' => 'Add Skill',
                'levels' => [
                    'Beginner' => 'Beginner',
                    'Elementary' => 'Elementary',
                    'Intermediate' => 'Intermediate',
                    'Advanced' => 'Advanced',
                    'Expert' => 'Expert',
                ],
            ],
            'certifications' => [
                'title' => 'Certifications',
                'name' => 'Certification Name',
                'issuer' => 'Issuer',
                'date' => 'Date',
                'add' => 'Add Certification',
            ],
            'projects' => [
                'title' => 'Projects',
                'name' => 'Project Name',
                'url' => 'Project URL (Optional)',
                'description' => 'Description',
                'refine' => 'Refine',
                'add' => 'Add Project',
            ],
            'languages' => [
                'title' => 'Languages',
                'language' => 'Language',
                'proficiency' => 'Proficiency',
                'select_level' => 'Select level',
                'add' => 'Add Language',
                'levels' => [
                    'Beginner' => 'Beginner',
                    'Conversational' => 'Conversational',
                    'Fluent' => 'Fluent',
                    'Native' => 'Native',
                ],
            ],
        ],
    ],

    'ats' => [
        'analyze_match' => 'Analyze Match',
        'premium_badge' => 'Premium',
        'page_title' => 'ATS Analyzer',
        'how_it_works' => 'How It Works',
        'sections_aria_label' => 'ATS Analyzer sections',
        'tabs' => [
            'setup' => 'Setup',
            'results' => 'Results',
        ],
        'instructions_modal' => [
            'title' => 'How ATS Scoring Works',
            'close_aria' => 'Close ATS scoring instructions',
            'intro' => 'Our ATS analyzer mimics how Applicant Tracking Systems evaluate your resume against a job description.',
            'keyword_match_label' => 'Keyword Match (65%)',
            'keyword_match_desc' => '— We extract critical single-word and multi-word terms from the JD and check how many appear in your resume.',
            'action_verbs_label' => 'Action Verbs (15%)',
            'action_verbs_desc' => '— Strong, impactful verbs signal an achievement-oriented candidate to ATS parsers.',
            'quantification_label' => 'Quantification (12%)',
            'quantification_desc' => '— Numbers and percentages dramatically improve relevancy scores in most ATS systems.',
            'length_format_label' => 'Length &amp; Format (8%)',
            'length_format_desc' => '— Resumes between 200–800 words are parsed most reliably by automated systems.',
            'tip' => "Tip: The closer your resume's language mirrors the job description, the higher your match score will be.",
        ],
        'setup' => [
            'select_from_resumes' => 'Select from your Resumes',
            'choose_resume_placeholder' => '-- Choose a Resume --',
            'autofill_hint' => "Auto-filled from your resume's target job. You can edit before analyzing.",
            'analyzer_title' => 'ATS Analyzer',
            'analyzer_desc' => 'Premium unlocks full resume-to-job matching, missing keywords, and prioritized improvement guidance.',
            'scan_history' => 'Scan History',
            'recent_scans_count' => ':count recent scan|:count recent scans',
            'untitled_scan' => 'Untitled Scan',
            'no_resume_linked' => 'No resume linked',
            'delete' => 'Delete',
            'delete_scan_aria' => 'Delete scan :title',
            'load_scan_aria' => 'Load scan :title',
            'no_history_yet' => 'No scan history yet — run your first analysis above.',
        ],
        'results' => [
            'select_analyze_heading' => 'Select & Analyze',
            'select_analyze_before' => 'Select a resume on the left, then click',
            'select_analyze_after' => 'to see your ATS score and actionable recommendations.',
            'resume_preview' => 'Resume Preview',
            'click_to_score' => 'Click :action to score',
            'missing_keywords' => 'Missing Keywords',
            'matched_keywords' => 'Matched Keywords',
            'action_verbs_detected' => 'Action Verbs Detected',
            'consider_adding' => 'Consider Adding',
            'section_breakdown' => 'Section Breakdown',
            'strategic_insights' => 'Strategic Insights',
            'reanalyze_before' => 'Update your texts and click',
            'reanalyze_after' => 'again to see your new score.',
        ],
    ],

    'interview' => [
        'index' => [
            'page_title' => 'Mock Interview',
            'heading' => 'Mock Interview HRD',
            'greeting' => "Hello! I'm Ms. Sarah",
            'intro' => "I'll interview you based on your actual CV content — not generic questions. Choose a CV and the position you'd like to practice.",
            'trial_used_title' => 'Your Free Trial Has Been Used',
            'trial_used_body' => "You've used your 1 free interview session.<br>\nUpgrade to Premium for unlimited sessions.",
            'upgrade_to_premium' => 'Upgrade to Premium',
            'view_last_session' => 'View Last Session',
            'ai_credits_remaining' => 'AI Credits Remaining',
            'select_cv' => 'Select CV',
            'no_cv_yet' => "You don't have a CV yet.",
            'create_cv_first' => 'Create a CV first',
            'position_applied_for' => 'Position Applied For',
            'position_placeholder' => 'e.g. Backend Engineer, Product Manager…',
            'trial_banner_before' => 'You have',
            'trial_banner_bold' => '1 free trial session',
            'trial_banner_after' => 'as a Basic user.',
            'start_interview' => 'Start Interview',
            'starting' => 'Starting…',
            'select_cv_first_error' => 'Please select a CV first.',
            'enter_position_error' => "Please enter the position you're applying for.",
            'start_failed_error' => 'Failed to start session. Please try again.',
            'timeout_error' => 'Request timed out. Please try again.',
            'network_error' => 'A network error occurred. Please try again.',
            'recent_interviews' => 'Recent Interviews',
            'view_full_history' => 'View Full History',
            'cv_prefix' => 'CV:',
            'deleted_cv' => 'Deleted CV',
            'score_label' => 'Score: :score/100',
            'in_progress' => 'In Progress',
            'ended' => 'Ended',
        ],
        'session' => [
            'page_title' => 'Interview with Ms. Sarah',
            'header_name' => 'Ms. Sarah · HRD Interviewer',
            'end_session' => 'End Session',
            'session_ended_badge' => 'Session Ended',
            'ended_on' => 'This session ended on :date.',
            'view_report' => 'View Report',
            'generate_report' => 'Generate Report (1 credit)',
            'input_placeholder' => 'Type your answer… (Enter to send, Shift+Enter for new line)',
            'end_modal_title' => 'End Session?',
            'end_modal_subtitle' => 'Ended sessions cannot be resumed.',
            'end_modal_body' => 'Are you sure you want to end this interview session now?',
            'cancel' => 'Cancel',
            'error_occurred' => 'An error occurred. Please try again.',
            'timeout_error' => 'Request timed out. Please try again.',
            'connection_lost' => 'Connection lost. Check your network and try again.',
        ],
        'feedback' => [
            'page_title' => 'Interview Report',
            'heading' => 'Interview Report',
            'badge_ready' => 'Ready to Work',
            'badge_almost_ready' => 'Almost Ready',
            'badge_needs_practice' => 'Needs More Practice',
            'position_label' => 'Position: :job',
            'overall_score' => 'Overall Score',
            'score_excellent' => 'Your interview performance was excellent. Keep it up!',
            'score_good' => 'Your performance was good with a few areas to improve.',
            'score_needs_practice' => 'More practice needed. Review the feedback below carefully.',
            'per_question_breakdown' => 'Per-Question Breakdown',
            'star_situation' => 'Situation',
            'star_task' => 'Task',
            'star_action' => 'Action',
            'star_result' => 'Result',
            'start_new_interview' => 'Start New Interview',
            'view_conversation' => 'View Conversation',
            'want_more_practice' => 'Want more practice?',
            'upgrade_unlimited_desc' => 'Upgrade to Premium for unlimited interview sessions + 50 AI credits/month.',
        ],
        'history' => [
            'page_title' => 'Interview History',
            'heading' => 'Interview History',
            'all_resumes' => 'All Resumes',
            'sort_label' => 'Sort:',
            'sort_date' => 'Date',
            'sort_score' => 'Score',
            'reset' => 'Reset',
            'no_sessions_yet' => 'No interview sessions yet',
            'complete_first_interview' => 'Complete your first mock interview to see your history here.',
            'start_first_interview' => 'Start Your First Interview',
        ],
    ],

    'help' => [
        'hero_title' => 'Help Center',
        'hero_subtitle' => 'Find answers to common questions or reach out to our support team.',
        'my_tickets' => 'My Tickets',
        'faq_heading' => 'Frequently Asked Questions',
        'faq' => [
            'getting_started' => [
                'label' => 'Getting Started',
                'items' => [
                    ['q' => 'How do I create my first resume?', 'a' => 'Go to your dashboard and click "New Resume". Choose a template, then fill in your personal info, work experience, education, and skills. You can preview and download your resume as a PDF at any time.'],
                    ['q' => 'What templates are available?', 'a' => 'We offer a variety of professionally designed templates suited for different industries and experience levels. Visit the Templates page to preview all available designs.'],
                    ['q' => 'Can I create multiple resumes?', 'a' => 'Yes! You can create as many resumes as you need. Each resume can be customized independently for different job applications.'],
                ],
            ],
            'resume_builder' => [
                'label' => 'Resume Builder',
                'items' => [
                    ['q' => 'How do I download my resume as a PDF?', 'a' => 'Open your resume in the editor and click the "Export PDF" button in the top right corner. Your resume will be generated and downloaded automatically.'],
                    ['q' => 'Can I change the template after I\'ve started editing?', 'a' => 'Yes, you can switch templates at any time from the editor. Your content will be preserved, only the visual design will change.'],
                    ['q' => 'What sections can I add to my resume?', 'a' => 'You can add Personal Info, Work Experience, Education, Skills, and Target Job sections. Each section can be customized to fit your background.'],
                ],
            ],
            'ai_features' => [
                'label' => 'AI Features',
                'items' => [
                    ['q' => 'How do I improve my ATS score?', 'a' => 'Use the ATS Analyzer feature to check how well your resume matches a job description. Paste the job posting, and our AI will identify missing keywords and suggest improvements.'],
                    ['q' => 'How many AI credits do I get?', 'a' => 'Basic users receive 10 AI credits per month. Premium users get 100 credits. Each AI action (ATS analysis, bullet optimization, etc.) uses a set number of credits.'],
                    ['q' => 'What does "AI Polish" do?', 'a' => 'AI Polish rewrites your resume bullet points to be more impactful, using strong action verbs and quantifiable achievements. It uses 1 credit per bullet point.'],
                ],
            ],
            'billing' => [
                'label' => 'Billing',
                'items' => [
                    ['q' => 'How do I upgrade to Premium?', 'a' => 'Go to the Upgrade page from your dashboard or settings. We support payment via Midtrans (bank transfer, e-wallet, and cards).'],
                    ['q' => 'What happens to my data if I cancel?', 'a' => 'Your resumes and data are preserved. You\'ll be downgraded to the Basic plan and your AI quota will be adjusted accordingly at the next billing cycle.'],
                ],
            ],
            'technical' => [
                'label' => 'Technical',
                'items' => [
                    ['q' => 'Is my data secure?', 'a' => 'Yes. All data is encrypted in transit (HTTPS) and at rest. We never share your personal information with third parties. You can delete your account and all associated data at any time from Settings.'],
                    ['q' => 'Why won\'t my PDF export?', 'a' => 'Make sure all required sections (Personal Info, Work Experience, Education, Skills) are filled in. If the issue persists, try a different browser or contact support.'],
                ],
            ],
        ],
        'contact' => [
            'heading' => 'Still need help?',
            'subtitle' => "Send us a message and we'll get back to you as soon as possible.",
            'subject_label' => 'Subject',
            'subject_placeholder' => 'Briefly describe your issue...',
            'message_label' => 'Message',
            'message_placeholder' => 'Describe your issue in detail...',
            'send' => 'Send Message',
            'sending' => 'Sending...',
            'success' => "Your message has been sent! We'll get back to you soon.",
        ],
    ],

    'tickets' => [
        'status' => [
            'open' => 'Open',
            'pending' => 'Pending',
            'awaiting_closure' => 'Awaiting Closure',
            'closed' => 'Closed',
        ],
        'list' => [
            'breadcrumb_help' => 'Help Center',
            'breadcrumb_current' => 'My Tickets',
            'heading' => 'My Support Tickets',
            'new_ticket' => 'New Ticket',
            'replies_count' => ':count reply|:count replies',
            'empty_title' => 'No support tickets yet',
            'empty_cta' => 'Submit your first ticket',
        ],
        'show' => [
            'breadcrumb_help' => 'Help Center',
            'breadcrumb_my_tickets' => 'My Tickets',
            'ticket_number' => 'Ticket #:id',
            'opened_on' => 'Opened :date',
        ],
        'chat' => [
            'conversation' => 'Conversation',
            'unknown_sender' => 'Unknown',
            'support_badge' => 'Support',
            'no_replies' => 'No replies yet.',
            'ready_to_close' => 'Ready to close this ticket?',
            'request_close' => 'Request Close',
            'waiting_other_party' => 'Waiting for the other party to confirm or reject your close request.',
            'other_requested_close' => 'The other party requested to close this ticket.',
            'reject' => 'Reject',
            'confirm_close' => 'Confirm Close',
            'reply_label' => 'Reply message',
            'reply_placeholder' => 'Type your reply...',
            'send_reply' => 'Send Reply',
            'sending' => 'Sending...',
            'closed_notice' => 'This ticket is closed.',
            'reply_sent' => 'Your reply has been sent.',
            'close_already_pending_error' => 'A close request is already pending or the ticket is already closed.',
            'no_pending_confirm_error' => 'There is no pending close request to confirm.',
            'no_pending_reject_error' => 'There is no pending close request to reject.',
            'closed_reply_error' => 'This ticket is closed and cannot receive new replies.',
        ],
    ],

    'settings' => [
        'page_title' => 'Account Settings',
        'heading' => 'Settings',
        'subtitle' => 'Manage your editorial presence and workspace security.',
        'notifications' => [
            'heading' => 'Notifications',
            'mark_all_read' => 'Mark all as read',
            'default_message' => 'You have a new notification.',
            'empty' => 'No notifications yet.',
        ],
        'flash' => [
            'avatar_updated' => 'Avatar updated successfully.',
            'avatar_deleted' => 'Avatar removed.',
            'profile_updated' => 'Profile updated successfully.',
            'password_updated' => 'Password updated successfully.',
        ],
        'profile_section' => [
            'title' => 'User Profile',
            'verified_badge' => 'Verified Author',
            'avatar_alt' => 'User Avatar',
            'avatar_format_hint' => 'Format: JPG, PNG (Max 2MB)',
            'full_name' => 'Full Name',
            'email_address' => 'Email Address',
            'locked' => 'locked',
            'save_changes' => 'Save Changes',
        ],
        'billing_section' => [
            'title' => 'Subscription & Billing',
            'upgrade_quota' => 'Upgrade Quota',
            'optimized_by' => 'Optimized by Resumify Editorial Engine.',
            'view_transaction_history' => 'View Transaction History',
            'transaction_history_wip' => 'Transaction history feature is currently in development. Please check back later.',
        ],
        'security_section' => [
            'title' => 'Security & Password',
            'subtitle' => 'Ensure your account is using a long, random password to stay secure.',
            'current_password' => 'Current Password',
            'new_password' => 'New Password',
            'confirm_new_password' => 'Confirm New Password',
            'password_hint' => 'Use at least 8 characters with a combination of numbers and symbols.',
            'update_password' => 'Update Password',
        ],
        'danger_zone' => [
            'title' => 'Danger Zone',
            'warning' => 'This action cannot be undone. All your data will be permanently deleted from our servers.',
            'confirm_password_placeholder' => 'Confirm your password',
            'delete_account' => 'Delete Account',
            'confirm_dialog' => 'Are you sure you want to delete your account? This cannot be undone.',
        ],
    ],

    'landing' => [

        'navbar' => [
            'features' => 'Features',
            'templates' => 'Templates',
            'pricing' => 'Pricing',
            'login' => 'Login',
            'cta_create_resume' => 'Create Free Resume ✨',
            'language' => 'Language',
        ],

        'footer' => [
            'copyright' => 'Resumify. The Curated Manuscript.',
            'privacy_policy' => 'Privacy Policy',
            'terms_of_service' => 'Terms of Service',
            'cookie_policy' => 'Cookie Policy',
            'contact' => 'Contact',
            'close' => 'Close',
            'legal' => [
                'privacy' => [
                    'title' => 'Privacy Policy',
                    'html' => '
                        <p class="mb-4">At Resumify, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">1. Information We Collect</h4>
                        <p class="mb-4">We may collect personal identification information from Users in a variety of ways, including, but not limited to, when Users visit our site, register on the site, place an order, and in connection with other activities, services, features or resources we make available on our Site.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">2. How We Use Collected Information</h4>
                        <p class="mb-4">Resumify may collect and use Users\' personal information for the following purposes:</p>
                        <ul class="list-disc pl-5 mb-4 space-y-1">
                            <li>To improve customer service</li>
                            <li>To personalize user experience</li>
                            <li>To process payments securely</li>
                            <li>To send periodic emails regarding your manuscript updates</li>
                        </ul>
                        <h4 class="font-bold text-primary mb-2 mt-6">3. Data Security</h4>
                        <p>We adopt appropriate data collection, storage and processing practices and security measures to protect against unauthorized access, alteration, disclosure or destruction of your personal information, username, password, transaction information and data stored on our Site.</p>
                    ',
                ],
                'terms' => [
                    'title' => 'Terms of Service',
                    'html' => '
                        <p class="mb-4">Welcome to Resumify. By accessing this website, we assume you accept these terms and conditions. Do not continue to use Resumify if you do not agree to take all of the terms and conditions stated on this page.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">1. License</h4>
                        <p class="mb-4">Unless otherwise stated, Resumify and/or its licensors own the intellectual property rights for all material on Resumify. All intellectual property rights are reserved. You may access this from Resumify for your own personal use subjected to restrictions set in these terms and conditions.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">2. User Accounts</h4>
                        <p class="mb-4">When you create an account with us, you must provide us information that is accurate, complete, and current at all times. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account on our Service.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">3. Limitation of Liability</h4>
                        <p>In no event shall Resumify, nor any of its officers, directors and employees, shall be held liable for anything arising out of or in any way connected with your use of this Website whether such liability is under contract.</p>
                    ',
                ],
                'cookie' => [
                    'title' => 'Cookie Policy',
                    'html' => '
                        <p class="mb-4">Our website uses cookies to distinguish you from other users of our website. This helps us to provide you with a good experience when you browse our website and also allows us to improve our site.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">1. What are cookies?</h4>
                        <p class="mb-4">A cookie is a small file of letters and numbers that we store on your browser or the hard drive of your computer if you agree. Cookies contain information that is transferred to your computer\'s hard drive.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">2. How we use cookies</h4>
                        <p class="mb-4">We use the following cookies:</p>
                        <ul class="list-disc pl-5 mb-4 space-y-1">
                            <li><strong>Strictly necessary cookies:</strong> Required for the operation of our website, such as secure login areas.</li>
                            <li><strong>Analytical or performance cookies:</strong> Allow us to recognise and count the number of visitors.</li>
                            <li><strong>Functionality cookies:</strong> Used to recognise you when you return to our website and remember your preferences.</li>
                        </ul>
                        <p>You can block cookies by activating the setting on your browser that allows you to refuse the setting of all or some cookies.</p>
                    ',
                ],
                'contact' => [
                    'title' => 'Contact Us',
                    'html' => '
                        <p class="mb-6 text-[15px]">We would love to hear from you. If you have any questions, concerns, or feedback regarding Resumify, please reach out to our support team.</p>

                        <div class="bg-primary/5 p-4 rounded-xl mb-4 border border-primary/10">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-secondary">mail</span>
                                <span class="font-bold text-primary">Email Support</span>
                            </div>
                            <p class="text-primary/70 ml-9">hello@resumify.com<br>support@resumify.com</p>
                        </div>

                        <div class="bg-primary/5 p-4 rounded-xl mb-6 border border-primary/10">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-secondary">location_on</span>
                                <span class="font-bold text-primary">Office Headquarters</span>
                            </div>
                            <p class="text-primary/70 ml-9">123 Innovation Drive<br>Tech District, San Francisco<br>CA 94105, United States</p>
                        </div>

                        <p class="text-sm italic text-primary/60">Our support team usually responds within 24-48 business hours.</p>
                    ',
                ],
            ],
        ],

        'welcome' => [
            'hero' => [
                'title' => 'Write Your Success Story',
                'subtitle' => 'Adapt your resume to job openings with artificial intelligence.',
                'cta' => 'Upgrade Your Resume',
            ],
            'preview' => [
                'work_experience' => 'WORK EXPERIENCE',
                'ai_optimized' => 'AI Optimized',
                'ai_optimized_desc' => 'Leading a design team of 12 people and increased user conversion rates by 34% through systematic A/B testing.',
                'ats_match_score' => 'ATS MATCH SCORE',
                'low' => 'Low',
                'high' => 'High',
                'input_editor' => 'INPUT EDITOR',
                'name_label' => 'NAME',
                'company_label' => 'COMPANY',
                'description_label' => 'DESCRIPTION',
                'description_value' => 'Leading design team and increasing conversions by 34%...',
                'resume_quality' => 'Your resume quality',
                'high_match' => 'High Match',
            ],
            'features' => [
                'ai_bullet' => [
                    'title' => 'AI Bullet Point Generator',
                    'desc' => 'Write your achievements instantly with data-driven suggestions that stand out to recruiters.',
                ],
                'ats_scanner' => [
                    'title' => 'ATS Match Score Scanner',
                    'desc' => 'Evaluate your resume against job descriptions in real-time to ensure it passes filtration systems.',
                ],
                'premium_templates' => [
                    'title' => 'Premium Templates',
                    'desc' => 'A collection of professionally curated templates for various industries and career levels.',
                ],
            ],
            'cta' => [
                'title' => 'Ready to build your story?',
                'subtitle' => 'Join thousands of professionals who have accelerated their career with Resumify.',
                'button' => 'Start for Free Now',
            ],
        ],

        'templates' => [
            'hero_title' => 'Choose a Template<br>that Fits Your Career',
            'hero_subtitle' => 'From minimalist to creative, all our templates are optimized to pass ATS filters with a high-end editorial touch.',
            'tab_all' => 'All',
            'use_template_full' => 'Use This Template',
            'use_template_short' => 'Use Template',
            'preview_aria' => 'Preview :title template',
            'close_preview' => 'Close preview',
            'cta' => [
                'title' => 'Haven\'t found the right fit?',
                'subtitle' => 'Don\'t worry, all templates can be fully customized to meet your personal brand needs. Start your career journey today.',
                'button' => 'Register for Free Now',
            ],
        ],

        'pricing' => [
            'hero' => [
                'title' => 'Invest in Your Career',
                'subtitle' => 'Start for free, or unlock your full potential with AI Premium features.',
            ],
            'plans' => [
                'starter' => 'Starter',
                'forever' => 'forever',
                'month' => 'month',
                'standard_templates' => 'Standard Templates',
                'no_ai_enhancement' => 'No AI Enhancement',
                'get_started' => 'Get Started',
                'premium_pro' => 'Premium PRO',
                'ai_bullet_optimizer' => 'AI Bullet Point Optimizer',
                'ai_bullet_optimizer_subtitle' => 'Optimize with high-impact keywords',
                'realtime_ats_matcher' => 'Real-time ATS Matcher',
                'premium_pdf_export' => 'Premium PDF Export',
                'priority_support' => 'Priority Support',
                'activate_premium' => 'Activate Premium Now',
            ],
            'compare' => [
                'title' => 'Compare Our Features',
                'key_features' => 'Key Features',
                'number_of_resumes' => 'Number of Resumes',
                'standard' => 'Standard',
                'premium' => 'Premium',
            ],
            'faq' => [
                'title' => 'Frequently Asked Questions',
                'q1' => 'Can I cancel my subscription?',
                'a1' => 'Yes, you can cancel your subscription at any time through your account settings. Your premium access will remain active until the end of the current billing period.',
                'q2' => 'What payment methods are available?',
                'a2' => 'We accept credit cards (Visa, Mastercard), PayPal, and various local digital wallets to facilitate your transactions securely.',
                'q3' => 'How does the AI help my resume?',
                'a3' => 'Our AI analyzes job descriptions and provides relevant keyword suggestions, optimizes bullet point grammar, and ensures your resume format is well-read by ATS systems.',
            ],
        ],

    ],

];
