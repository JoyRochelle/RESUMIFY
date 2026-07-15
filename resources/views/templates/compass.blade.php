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
        @import url('https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Lato', sans-serif; background: #ffffff; color: #1e293b; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; }
        .top-bar { height: 6px; background: {{ $style['primary_color'] ?? '#1e293b' }}; }
        .header { padding: 22px 28px 18px 28px; border-bottom: 1px solid #e2e8f0; }
        .name { font-size: 28px; font-weight: 700; color: {{ $style['primary_color'] ?? '#1e293b' }}; margin: 0 0 4px 0; }
        .career-goal-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: #94a3b8; margin: 12px 0 4px 0; }
        .career-goal { font-size: 12.5px; font-style: italic; color: #475569; line-height: 1.6; padding-left: 10px; border-left: 3px solid {{ $style['primary_color'] ?? '#1e293b' }}; }
        .contact { font-size: 11.5px; color: #94a3b8; margin-top: 10px; }
        .contact span { margin-right: 16px; }
        .body { padding: 18px 28px; }
        h2 { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: {{ $style['primary_color'] ?? '#1e293b' }}; margin: 18px 0 8px 0; border-bottom: 1.5px solid {{ $style['primary_color'] ?? '#1e293b' }}; padding-bottom: 3px; }
        .entry { margin-bottom: 13px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #94a3b8; white-space: nowrap; }
        .entry-title { font-size: 13.5px; font-weight: 700; color: #0f172a; }
        .entry-sub { font-size: 12.5px; color: #64748b; margin-top: 1px; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #374151; margin-top: 5px; }
        .skills-line { font-size: 12px; color: #374151; line-height: 1.9; }
        .title-role { font-size: 14px; color: #64748b; font-weight: 300; margin: 0 0 8px 0; }
    </style>
</head>
<body>
<div class="page">
    <div class="top-bar"></div>
    <div class="header">
        <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
        @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif

        @if(!empty($info['summary']))
        <div class="career-goal-label">Career Objective</div>
        <div class="career-goal">{!! nl2br(e($info['summary'])) !!}</div>
        @endif

        <div class="contact" style="margin-top:12px;">
            <span>{{ $info['email'] ?? 'email@example.com' }}</span>
            @if(!empty($info['phone'])) <span>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</span> @endif
            @if(!empty($info['location'])) <span>{{ $info['location'] }}</span> @endif
        </div>
    </div>
    <div class="body">
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

        @if($experience && !empty($experience->content))
        <h2>Experience & Organizations</h2>
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
        <h2>Projects</h2>
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

        @if($skills && !empty($skills->content))
        <h2>Skills</h2>
        <div class="skills-line">
            @foreach($skills->content as $skill)
            {{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) ({{ $skill['level'] }})@endif@if(!$loop->last) &nbsp;·&nbsp; @endif
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
        <div class="skills-line">
            @foreach($languages->content as $lang)
            {{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif@if(!$loop->last) &nbsp;·&nbsp; @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
</body>
</html>
