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
    $hasWorkExp = $experience && !empty($experience->content) && !empty(array_filter($experience->content, fn($j) => !empty($j['company'])));
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Manrope', sans-serif; background: #ffffff; color: #1a2e1a; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; }
        .header { background: {{ $style['primary_color'] ?? '#16a34a' }}; padding: 24px 28px 20px 28px; }
        .name { font-size: 30px; font-weight: 800; color: #ffffff; margin: 0 0 3px 0; }
        .title-role { font-size: 14px; font-weight: 500; color: rgba(255,255,255,0.82); margin: 0 0 12px 0; }
        .contact { font-size: 12px; color: rgba(255,255,255,0.75); display: table; width: 100%; }
        .contact span { display: table-cell; padding-right: 20px; }
        .body { padding: 20px 28px 22px 28px; }
        h2 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: {{ $style['primary_color'] ?? '#16a34a' }}; margin: 20px 0 8px 0; border-bottom: 2px solid {{ $style['primary_color'] ?? '#16a34a' }}; padding-bottom: 3px; }
        .entry { margin-bottom: 13px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #9ca3af; white-space: nowrap; font-weight: 600; }
        .entry-title { font-size: 13.5px; font-weight: 700; color: #111827; }
        .entry-sub { font-size: 12.5px; color: #6b7280; margin-top: 1px; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #374151; margin-top: 5px; }
        .highlight-badge { display: inline-block; background: #dcfce7; color: {{ $style['primary_color'] ?? '#16a34a' }}; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px; margin-left: 6px; }
        .skills-wrap { font-size: 12px; line-height: 1.9; color: #374151; }
        .skill-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $style['primary_color'] ?? '#16a34a' }}; margin-right: 6px; vertical-align: middle; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
        @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
        <div class="contact">
            <span>{{ $info['email'] ?? 'email@example.com' }}</span>
            @if(!empty($info['phone'])) <span>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</span> @endif
            @if(!empty($info['location'])) <span>{{ $info['location'] }}</span> @endif
        </div>
    </div>
    <div class="body">
        @if(!empty($info['summary']))
        <div class="summary" style="margin-top:4px;">{!! nl2br(e($info['summary'])) !!}</div>
        @endif

        {{-- Education first for fresh grad --}}
        @if($education && !empty($education->content))
        <h2>Education</h2>
        @foreach($education->content as $edu)
        <div class="entry">
            <div class="entry-row">
                <div class="entry-main">
                    <div class="entry-title">{{ $edu['degree'] ?? '' }}@if(!empty($edu['gpa'])) <span class="highlight-badge">IPK {{ $edu['gpa'] }}</span>@endif</div>
                    <div class="entry-sub">{{ $edu['school'] ?? '' }}</div>
                </div>
                <div class="entry-date">{{ $edu['start_date'] ?? '' }}{{ !empty($edu['start_date']) ? ' – ' : '' }}{{ $edu['end_date'] ?? '' }}</div>
            </div>
            @if(!empty($edu['description'])) <div class="entry-desc">{!! nl2br(e($edu['description'])) !!}</div> @endif
        </div>
        @endforeach
        @endif

        @if($projects && !empty($projects->content))
        <h2>Projects & Work</h2>
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

        @if($experience && !empty($experience->content))
        <h2>{{ $hasWorkExp ? 'Work Experience' : 'Internships & Organization Experience' }}</h2>
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

        @if($skills && !empty($skills->content))
        <h2>Skills</h2>
        <div class="skills-wrap">
            @foreach($skills->content as $skill)
            <span><span class="skill-dot"></span>{{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) ({{ $skill['level'] }})@endif&nbsp;&nbsp;</span>
            @endforeach
        </div>
        @endif

        @if($certifications && !empty($certifications->content))
        <h2>Certifications</h2>
        @foreach($certifications->content as $cert)
        <div class="entry">
            <div class="entry-row">
                <div class="entry-main">
                    <div class="entry-title">{{ $cert['name'] ?? '' }}</div>
                    <div class="entry-sub">{{ $cert['issuer'] ?? '' }}</div>
                </div>
                <div class="entry-date">{{ $cert['date'] ?? '' }}</div>
            </div>
        </div>
        @endforeach
        @endif

        @if($languages && !empty($languages->content))
        <h2>Languages</h2>
        <div class="skills-wrap">
            @foreach($languages->content as $lang)
            <span><span class="skill-dot"></span>{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif&nbsp;&nbsp;</span>
            @endforeach
        </div>
        @endif
    </div>
</div>
</body>
</html>
