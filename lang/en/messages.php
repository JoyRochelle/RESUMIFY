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

];
