@php
    $personal   = $cv->sections->where('type', 'personal_info')->first();
    $experience = $cv->sections->where('type', 'work_experience')->first();
    $education  = $cv->sections->where('type', 'education')->first();
    $skills     = $cv->sections->where('type', 'skills')->first();
    $certifications = $cv->sections->where('type', 'certifications')->first();
    $projects       = $cv->sections->where('type', 'projects')->first();
    $languages      = $cv->sections->where('type', 'languages')->first();

    $info  = $personal?->content ?? [];
    $style = $cv->template->style_config ?? [];
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Roboto', sans-serif; background: #ffffff; color: #1a3a3a; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; }
        .layout { display: table; width: 100%; min-height: 297mm; border-collapse: collapse; }
        .sidebar { display: table-cell; width: 28%; background: {{ $style['primary_color'] ?? '#0d9488' }}; vertical-align: top; padding: 30px 20px; }
        .main { display: table-cell; width: 72%; vertical-align: top; padding: 30px 28px; background: #ffffff; }
        .photo { width: 90px; height: 90px; object-fit: cover; border-radius: 50%; border: 3px solid rgba(255,255,255,0.4); display: block; margin: 0 auto 16px auto; }
        .sidebar-name { font-size: 17px; font-weight: 700; color: #ffffff; text-align: center; margin: 0 0 3px 0; }
        .sidebar-role { font-size: 11px; color: rgba(255,255,255,0.65); text-align: center; margin: 0 0 18px 0; }
        .sidebar-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,0.5); margin: 16px 0 6px 0; }
        .sidebar-value { font-size: 11px; color: rgba(255,255,255,0.85); line-height: 1.7; word-break: break-all; }
        .sidebar-skill { display: block; font-size: 11px; color: rgba(255,255,255,0.85); padding: 3px 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
        h2 { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: {{ $style['primary_color'] ?? '#0d9488' }}; margin: 0 0 14px 0; }
        .section-wrap { margin-bottom: 20px; }
        {{-- Timeline entries --}}
        .timeline-entry { display: table; width: 100%; margin-bottom: 16px; }
        .timeline-dot-cell { display: table-cell; width: 20px; vertical-align: top; padding-top: 4px; }
        .timeline-dot { width: 10px; height: 10px; border-radius: 50%; background: {{ $style['primary_color'] ?? '#0d9488' }}; display: inline-block; }
        .timeline-line-cell { display: table-cell; width: 1px; background: #ccfbf1; }
        .timeline-content { display: table-cell; vertical-align: top; padding-left: 12px; }
        .entry-title { font-size: 13.5px; font-weight: 700; color: #111827; }
        .entry-sub { font-size: 12.5px; color: #0d9488; font-weight: 500; margin-top: 1px; }
        .entry-date { font-size: 11px; color: #9ca3af; margin: 2px 0 4px 0; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #374151; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 18px; }
        .skill-badge { display: inline-block; background: #ccfbf1; color: #0f766e; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin: 2px 3px 2px 0; }
    </style>
</head>
<body>
<div class="page">
    <div class="layout">
        <div class="sidebar">
            @if(!empty($style['show_photo']) && !empty($info['photo']))
            <img src="{{ $info['photo'] }}" alt="Profile" class="photo">
            @endif
            <div class="sidebar-name">{{ $info['name'] ?? 'Your Name' }}</div>
            @if(!empty($info['title'])) <div class="sidebar-role">{{ $info['title'] }}</div> @endif

            <div class="sidebar-label">Email</div>
            <div class="sidebar-value">{{ $info['email'] ?? 'email@example.com' }}</div>

            @if(!empty($info['phone']))
            <div class="sidebar-label">Phone</div>
            <div class="sidebar-value">{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div>
            @endif

            @if(!empty($info['location']))
            <div class="sidebar-label">Location</div>
            <div class="sidebar-value">{{ $info['location'] }}</div>
            @endif

            @if($skills && !empty($skills->content))
            <div class="sidebar-label">Skills</div>
            @foreach($skills->content as $skill)
            <span class="sidebar-skill">{{ $skill['name'] ?? '' }}</span>
            @endforeach
            @endif

            @if($languages && !empty($languages->content))
            <div class="sidebar-label">Languages</div>
            @foreach($languages->content as $lang)
            <span class="sidebar-skill">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
            @endforeach
            @endif
        </div>
        <div class="main">
            @if(!empty($info['summary']))
            <div class="summary">{!! nl2br(e($info['summary'])) !!}</div>
            @endif

            @if($experience && !empty($experience->content))
            <div class="section-wrap">
                <h2>Experience Timeline</h2>
                @foreach($experience->content as $job)
                <div class="timeline-entry">
                    <div class="timeline-dot-cell"><div class="timeline-dot"></div></div>
                    <div class="timeline-content">
                        <div class="entry-title">{{ $job['title'] ?? '' }}</div>
                        <div class="entry-sub">{{ $job['company'] ?? '' }}</div>
                        <div class="entry-date">{{ $job['start_date'] ?? '' }}{{ !empty($job['start_date']) ? ' – ' : '' }}{{ $job['end_date'] ?? 'Present' }}</div>
                        @if(!empty($job['description'])) <div class="entry-desc">{!! nl2br(e($job['description'])) !!}</div> @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($education && !empty($education->content))
            <div class="section-wrap">
                <h2>Education</h2>
                @foreach($education->content as $edu)
                <div class="timeline-entry">
                    <div class="timeline-dot-cell"><div class="timeline-dot"></div></div>
                    <div class="timeline-content">
                        <div class="entry-title">{{ $edu['degree'] ?? '' }}</div>
                        <div class="entry-sub">{{ $edu['school'] ?? '' }}</div>
                        <div class="entry-date">{{ $edu['start_date'] ?? '' }}{{ !empty($edu['start_date']) ? ' – ' : '' }}{{ $edu['end_date'] ?? '' }}</div>
                        @if(!empty($edu['description'])) <div class="entry-desc">{!! nl2br(e($edu['description'])) !!}</div> @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($projects && !empty($projects->content))
            <div class="section-wrap">
                <h2>Projects</h2>
                @foreach($projects->content as $proj)
                <div class="timeline-entry">
                    <div class="timeline-dot-cell"><div class="timeline-dot"></div></div>
                    <div class="timeline-content">
                        <div class="entry-title">{{ $proj['name'] ?? '' }}</div>
                        @if(!empty($proj['url'])) <div class="entry-sub">{{ $proj['url'] }}</div> @endif
                        @if(!empty($proj['description'])) <div class="entry-desc">{!! nl2br(e($proj['description'])) !!}</div> @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if($certifications && !empty($certifications->content))
            <div class="section-wrap">
                <h2>Certifications</h2>
                @foreach($certifications->content as $cert)
                <span class="skill-badge">{{ $cert['name'] ?? '' }}</span>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
