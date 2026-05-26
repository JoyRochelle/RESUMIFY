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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Poppins' }}', sans-serif; background: {{ $style['background_color'] ?? '#f8fafc' }}; color: #334155; }
        .resume-page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; background: #ffffff; }
        .accent-bar { height: 12px; background: {{ $style['primary_color'] ?? '#f43f5e' }}; width: 100%; }
        .header-container { padding: 30px 40px; display: table; width: 100%; box-sizing: border-box; background: {{ $style['secondary_color'] ?? '#ffe4e6' }}; }
        .photo-cell { display: table-cell; width: 120px; vertical-align: middle; }
        .photo { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid {{ $style['primary_color'] ?? '#f43f5e' }}; }
        .title-cell { display: table-cell; vertical-align: middle; padding-left: 20px; }
        h1 { font-family: '{{ $style['font_heading'] ?? 'Poppins' }}', sans-serif; font-size: 36px; font-weight: 700; margin: 0; color: #0f172a; text-transform: uppercase; }
        .role { font-size: 18px; color: {{ $style['primary_color'] ?? '#f43f5e' }}; font-weight: 600; margin-top: 5px; margin-bottom: 10px; }
        .contact-info { font-size: 13px; }
        .content-container { padding: 40px; }
        h2 { font-family: '{{ $style['font_heading'] ?? 'Poppins' }}', sans-serif; font-size: 22px; color: #0f172a; margin-top: 0; margin-bottom: 20px; position: relative; padding-left: 15px; }
        h2::before { content: ''; position: absolute; left: 0; top: 4px; height: 20px; width: 4px; background: {{ $style['primary_color'] ?? '#f43f5e' }}; }
        .section-mb { margin-bottom: 30px; }
        .entry-grid { display: table; width: 100%; margin-bottom: 20px; }
        .dates-cell { display: table-cell; width: 25%; font-size: 13px; color: #64748b; font-weight: 600; vertical-align: top; }
        .details-cell { display: table-cell; width: 75%; vertical-align: top; }
        .entry-title { font-weight: 700; font-size: 16px; color: #0f172a; margin: 0 0 4px 0; }
        .entry-subtitle { font-size: 14px; color: {{ $style['primary_color'] ?? '#f43f5e' }}; font-weight: 600; margin-bottom: 8px; }
        .entry-desc { font-size: 13px; line-height: 1.6; }
        .skill-item { display: inline-block; background: {{ $style['secondary_color'] ?? '#ffe4e6' }}; color: #0f172a; padding: 6px 12px; border-radius: 15px; font-size: 13px; font-weight: 600; margin-right: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="resume-page">
        <div class="accent-bar"></div>
        <div class="header-container">
            @if(!empty($style['show_photo']) && !empty($info['photo']))
            <div class="photo-cell">
                <img src="{{ $info['photo'] }}" alt="Profile Photo" class="photo">
            </div>
            @endif
            <div class="title-cell">
                <h1>{{ $info['name'] ?? 'Your Name' }}</h1>
                @if(isset($info['title'])) <div class="role">{{ $info['title'] }}</div> @endif
                <div class="contact-info">
                    {{ $info['email'] ?? 'email@example.com' }} 
                    @if(!empty($info['phone'])) | {{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }} @endif
                    @if(!empty($info['location'])) | {{ $info['location'] }} @endif
                </div>
            </div>
        </div>

        <div class="content-container">
            @if(!empty($info['summary']))
            <div class="section-mb">
                <div class="entry-desc">{!! nl2br(e($info['summary'])) !!}</div>
            </div>
            @endif

            @if($experience && !empty($experience->content))
            <div class="section-mb">
                <h2>Experience</h2>
                @foreach($experience->content as $job)
                <div class="entry-grid">
                    <div class="dates-cell">
                        {{ $job['start_date'] ?? '' }} –<br>{{ $job['end_date'] ?? 'Present' }}
                    </div>
                    <div class="details-cell">
                        <div class="entry-title">{{ $job['title'] ?? 'Job Title' }}</div>
                        <div class="entry-subtitle">{{ $job['company'] ?? 'Company' }}</div>
                        <div class="entry-desc">{!! nl2br(e($job['description'] ?? '')) !!}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($education && !empty($education->content))
            <div class="section-mb">
                <h2>Education</h2>
                @foreach($education->content as $edu)
                <div class="entry-grid">
                    <div class="dates-cell">
                        {{ $edu['start_date'] ?? '' }} –<br>{{ $edu['end_date'] ?? 'Present' }}
                    </div>
                    <div class="details-cell">
                        <div class="entry-title">{{ $edu['degree'] ?? 'Degree' }}</div>
                        <div class="entry-subtitle">{{ $edu['school'] ?? 'School' }}</div>
                        @if(!empty($edu['description']))
                        <div class="entry-desc">{!! nl2br(e($edu['description'])) !!}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

@if($certifications && !empty($certifications->content))
            <div class="section-mb">
                <h2>Certifications</h2>
                @foreach($certifications->content as $cert)
                <div class="entry-grid">
                    <div class="dates-cell">
                        {{ $cert['date'] ?? '' }} 
                    </div>
                    <div class="details-cell">
                        <div class="entry-title">{{ $cert['name'] ?? 'Certificate Name' }}</div>
                        <div class="entry-subtitle">{{ $cert['issuer'] ?? 'Issuer' }}</div>
                        @if(!empty($cert['url']))
                        <div class="entry-desc">{!! nl2br(e($cert['url'])) !!}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

@if($projects && !empty($projects->content))
            <div class="section-mb">
                <h2>Projects</h2>
                @foreach($projects->content as $proj)
                <div class="entry-grid">
                    <div class="dates-cell">
                        {{ $proj['date'] ?? '' }} 
                    </div>
                    <div class="details-cell">
                        <div class="entry-title">{{ $proj['name'] ?? 'Project Name' }}</div>
                        <div class="entry-subtitle">{{ $proj['url'] ?? '' }}</div>
                        @if(!empty($proj['description']))
                        <div class="entry-desc">{!! nl2br(e($proj['description'])) !!}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($skills && !empty($skills->content))
            <div class="section-mb">
                <h2>Skills & Expertise</h2>
                <div>
                @foreach($skills->content as $skill)
                    <span class="skill-item">{{ $skill['name'] ?? '' }}</span>
                @endforeach
                </div>
            </div>
            @endif

@if($languages && !empty($languages->content))
            <div class="section-mb">
                <h2>Languages</h2>
                <div>
                @foreach($languages->content as $lang)
                    <span class="skill-item">{{ $lang['name'] ?? '' }} @if(!empty($lang['level'])) ({{ $lang['level'] }}) @endif</span>
                @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
