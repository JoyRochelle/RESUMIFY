@php
    $personal   = $cv->sections->where('type', 'personal_info')->first();
    $experience = $cv->sections->where('type', 'work_experience')->first();
    $education  = $cv->sections->where('type', 'education')->first();
    $skills     = $cv->sections->where('type', 'skills')->first();
    $certifications = $cv->sections->where('type', 'certifications')->first();
    $projects       = $cv->sections->where('type', 'projects')->first();
    $languages      = $cv->sections->where('type', 'languages')->first();

    $info       = $personal?->content ?? [];
    $style      = $cv->template->style_config ?? [];
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=swap');
        
        @page { margin: 0; }
        body {
            margin: 0; padding: 0;
            font-family: '{{ $style['font_body'] ?? 'Inter' }}', sans-serif;
            background: {{ $style['background_color'] ?? '#ffffff' }};
            color: #1a1a1a;
        }
        .resume-page {
            @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif
            padding: 24mm;
            box-sizing: border-box;
        }
        .resume-header {
            text-align: center;
            border-bottom: 2px solid {{ $style['primary_color'] ?? '#2563EB' }};
            padding-bottom: 16px; margin-bottom: 24px;
        }
        h1 {
            font-family: '{{ $style['font_heading'] ?? 'Playfair Display' }}', serif;
            font-size: 32px; margin: 0 0 8px 0;
            color: {{ $style['primary_color'] ?? '#2563EB' }};
        }
        .contact-info { font-size: 14px; color: #4b5563; }
        h2 {
            font-family: '{{ $style['font_heading'] ?? 'Playfair Display' }}', serif;
            font-size: 20px; color: {{ $style['secondary_color'] ?? '#1E40AF' }};
            margin-top: 24px; margin-bottom: 12px; text-transform: uppercase;
            letter-spacing: 1px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px;
        }
        .item-entry { margin-bottom: 16px; }
        .item-title { font-weight: 600; font-size: 16px; display: inline-block; }
        .item-subtitle { font-style: italic; color: #4b5563; }
        .item-dates { float: right; font-size: 14px; color: #6b7280; }
        .item-description { margin-top: 6px; font-size: 14px; line-height: 1.5; }
        .skills-list { margin: 0; padding: 0 0 0 20px; font-size: 14px; }
        .skills-list li { margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="resume-page">
        <header class="resume-header">
            <h1>{{ $info['name'] ?? 'Your Name' }}</h1>
            <div class="contact-info">
                {{ $info['email'] ?? 'email@example.com' }} 
                @if(!empty($info['phone'])) &bull; {{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }} @endif
                @if(!empty($info['location'])) &bull; {{ $info['location'] }} @endif
            </div>
        </header>

        @if(!empty($info['summary']))
        <div class="item-description" style="margin-bottom: 20px;">
            {!! nl2br(e($info['summary'])) !!}
        </div>
        @endif

        @if($experience && !empty($experience->content))
        <section>
            <h2>Work Experience</h2>
            @foreach($experience->content as $job)
                <div class="item-entry">
                    <div>
                        <span class="item-title">{{ $job['title'] ?? 'Job Title' }}</span>, 
                        <span class="item-subtitle">{{ $job['company'] ?? 'Company' }}</span>
                        <span class="item-dates">{{ $job['start_date'] ?? '' }} – {{ $job['end_date'] ?? 'Present' }}</span>
                    </div>
                    <div class="item-description">{!! nl2br(e($job['description'] ?? '')) !!}</div>
                </div>
            @endforeach
        </section>
        @endif

        @if($education && !empty($education->content))
        <section>
            <h2>Education</h2>
            @foreach($education->content as $edu)
                <div class="item-entry">
                    <div>
                        <span class="item-title">{{ $edu['degree'] ?? 'Degree' }}</span>, 
                        <span class="item-subtitle">{{ $edu['school'] ?? 'School' }}</span>
                        <span class="item-dates">{{ $edu['start_date'] ?? '' }} – {{ $edu['end_date'] ?? 'Present' }}</span>
                    </div>
                    @if(!empty($edu['description']))
                    <div class="item-description">{!! nl2br(e($edu['description'])) !!}</div>
                    @endif
                </div>
            @endforeach
        </section>
        @endif

@if($certifications && !empty($certifications->content))
        <section>
            <h2>Certifications</h2>
            @foreach($certifications->content as $cert)
                <div class="item-entry">
                    <div>
                        <span class="item-title">{{ $cert['name'] ?? 'Certificate Name' }}</span>, 
                        <span class="item-subtitle">{{ $cert['issuer'] ?? 'Issuer' }}</span>
                        <span class="item-dates">{{ $cert['date'] ?? '' }}  </span>
                    </div>
                    @if(!empty($cert['url']))
                    <div class="item-description">{!! nl2br(e($cert['url'])) !!}</div>
                    @endif
                </div>
            @endforeach
        </section>
        @endif

@if($projects && !empty($projects->content))
        <section>
            <h2>Projects</h2>
            @foreach($projects->content as $proj)
                <div class="item-entry">
                    <div>
                        <span class="item-title">{{ $proj['name'] ?? 'Project Name' }}</span>, 
                        <span class="item-subtitle">{{ $proj['url'] ?? '' }}</span>
                        <span class="item-dates">{{ $proj['date'] ?? '' }}  </span>
                    </div>
                    @if(!empty($proj['description']))
                    <div class="item-description">{!! nl2br(e($proj['description'])) !!}</div>
                    @endif
                </div>
            @endforeach
        </section>
        @endif

        @if($skills && !empty($skills->content))
        <section>
            <h2>Skills</h2>
            <ul class="skills-list">
            @foreach($skills->content as $skill)
                <li><strong>{{ $skill['name'] ?? '' }}</strong>: {{ $skill['level'] ?? '' }}</li>
            @endforeach
            </ul>
        </section>
        @endif

@if($languages && !empty($languages->content))
        <section>
            <h2>Languages</h2>
            <ul class="skills-list">
            @foreach($languages->content as $lang)
                <li><strong>{{ $lang['name'] ?? '' }} @if(!empty($lang['level'])) ({{ $lang['level'] }}) @endif</strong>: {{ $lang['level'] ?? '' }}</li>
            @endforeach
            </ul>
        </section>
        @endif
    </div>
</body>
</html>
