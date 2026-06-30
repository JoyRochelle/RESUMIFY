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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background: #ffffff; color: #1a1a1a; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif box-sizing: border-box; padding: 0; }
        .layout { display: table; width: 100%; min-height: 297mm; border-collapse: collapse; }
        .sidebar { display: table-cell; width: 35%; background: {{ $style['primary_color'] ?? '#ec4899' }}; vertical-align: top; padding: 32px 20px; }
        .main { display: table-cell; width: 65%; vertical-align: top; padding: 30px 26px; }
        .photo { width: 110px; height: 110px; object-fit: cover; border-radius: 50%; border: 4px solid rgba(255,255,255,0.5); display: block; margin: 0 auto 18px auto; }
        .photo-placeholder { width: 110px; height: 110px; border-radius: 50%; border: 2px dashed rgba(255,255,255,0.5); display: block; margin: 0 auto 18px auto; }
        .sidebar-name { font-size: 18px; font-weight: 700; color: #ffffff; text-align: center; margin: 0 0 4px 0; line-height: 1.3; }
        .sidebar-role { font-size: 11.5px; color: rgba(255,255,255,0.7); text-align: center; margin: 0 0 20px 0; }
        .sidebar-section { margin-top: 18px; }
        .sidebar-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,0.5); margin-bottom: 6px; }
        .sidebar-value { font-size: 11px; color: rgba(255,255,255,0.85); line-height: 1.7; word-break: break-word; }
        .sidebar-tag { display: block; background: rgba(255,255,255,0.15); color: #ffffff; font-size: 11px; padding: 4px 10px; border-radius: 20px; margin-bottom: 5px; text-align: center; }
        h2 { font-size: 17px; font-weight: 800; color: {{ $style['primary_color'] ?? '#ec4899' }}; margin: 22px 0 10px 0; border-bottom: 2px solid {{ $style['primary_color'] ?? '#ec4899' }}; padding-bottom: 4px; }
        .entry { margin-bottom: 14px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #e879f9; white-space: nowrap; font-weight: 500; }
        .entry-title { font-size: 14px; font-weight: 700; color: #111827; }
        .entry-sub { font-size: 12.5px; color: {{ $style['primary_color'] ?? '#ec4899' }}; margin-top: 1px; font-weight: 500; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #374151; margin-top: 5px; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="layout">
        <div class="sidebar">
            @if(!empty($style['show_photo']) && !empty($info['photo']))
            <img src="{{ $info['photo'] }}" alt="Profile" class="photo">
            @else
            <div class="photo-placeholder"></div>
            @endif
            <div class="sidebar-name">{{ $info['name'] ?? 'Your Name' }}</div>
            @if(!empty($info['title'])) <div class="sidebar-role">{{ $info['title'] }}</div> @endif

            <div class="sidebar-section">
                <div class="sidebar-label">Contact</div>
                <div class="sidebar-value">{{ $info['email'] ?? 'email@example.com' }}</div>
                @if(!empty($info['phone'])) <div class="sidebar-value">{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div> @endif
                @if(!empty($info['location'])) <div class="sidebar-value">{{ $info['location'] }}</div> @endif
            </div>

            @if($skills && !empty($skills->content))
            <div class="sidebar-section">
                <div class="sidebar-label">Skills</div>
                @foreach($skills->content as $skill)
                <span class="sidebar-tag">{{ $skill['name'] ?? '' }}</span>
                @endforeach
            </div>
            @endif

            @if($languages && !empty($languages->content))
            <div class="sidebar-section">
                <div class="sidebar-label">Languages</div>
                @foreach($languages->content as $lang)
                <span class="sidebar-tag">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) · {{ $lang['level'] }}@endif</span>
                @endforeach
            </div>
            @endif

            @if($certifications && !empty($certifications->content))
            <div class="sidebar-section">
                <div class="sidebar-label">Certifications</div>
                @foreach($certifications->content as $cert)
                <span class="sidebar-tag">{{ $cert['name'] ?? '' }}</span>
                @endforeach
            </div>
            @endif
        </div>
        <div class="main">
            @if(!empty($info['summary']))
            <div class="summary" style="margin-top:4px;">{!! nl2br(e($info['summary'])) !!}</div>
            @endif

            @if($experience && !empty($experience->content))
            <h2>Experience</h2>
            @foreach($experience->content as $job)
            <div class="entry">
                <div class="entry-row">
                    <div class="entry-main">
                        <div class="entry-title">{{ $job['title'] ?? '' }}</div>
                        <div class="entry-sub">{{ $job['company'] ?? '' }}</div>
                    </div>
                    <div class="entry-date">{{ $job['start_date'] ?? '' }}{{ !empty($job['start_date']) ? ' – ' : '' }}{{ $job['end_date'] ?? 'Present' }}</div>
                </div>
                @if(!empty($job['description'])) <div class="entry-desc">{!! nl2br(e($job['description'])) !!}</div> @endif
            </div>
            @endforeach
            @endif

            @if($projects && !empty($projects->content))
            <h2>Portfolio</h2>
            @foreach($projects->content as $proj)
            <div class="entry">
                <div class="entry-row">
                    <div class="entry-main">
                        <div class="entry-title">{{ $proj['name'] ?? '' }}</div>
                        @if(!empty($proj['url'])) <div class="entry-sub">{{ $proj['url'] }}</div> @endif
                    </div>
                    <div class="entry-date">{{ $proj['date'] ?? '' }}</div>
                </div>
                @if(!empty($proj['description'])) <div class="entry-desc">{!! nl2br(e($proj['description'])) !!}</div> @endif
            </div>
            @endforeach
            @endif

            @if($education && !empty($education->content))
            <h2>Education</h2>
            @foreach($education->content as $edu)
            <div class="entry">
                <div class="entry-row">
                    <div class="entry-main">
                        <div class="entry-title">{{ $edu['degree'] ?? '' }}</div>
                        <div class="entry-sub">{{ $edu['school'] ?? '' }}</div>
                    </div>
                    <div class="entry-date">{{ $edu['start_date'] ?? '' }}{{ !empty($edu['start_date']) ? ' – ' : '' }}{{ $edu['end_date'] ?? '' }}</div>
                </div>
                @if(!empty($edu['description'])) <div class="entry-desc">{!! nl2br(e($edu['description'])) !!}</div> @endif
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>
</body>
</html>
