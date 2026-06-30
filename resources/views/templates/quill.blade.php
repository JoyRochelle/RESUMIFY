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
        @import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Source+Sans+3:wght@300;400;600&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Source Sans 3', sans-serif; background: {{ $style['background_color'] ?? '#faf7f2' }}; color: #2d2d2d; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 22mm 24mm; box-sizing: border-box; background: {{ $style['background_color'] ?? '#faf7f2' }}; }
        .name { font-family: 'Lora', serif; font-size: 32px; font-weight: 700; color: {{ $style['primary_color'] ?? '#1a1a1a' }}; margin: 0; border-bottom: 2px solid {{ $style['primary_color'] ?? '#1a1a1a' }}; padding-bottom: 6px; }
        .title-role { font-family: 'Lora', serif; font-style: italic; font-size: 15px; color: #6b6b6b; margin: 6px 0 12px 0; }
        .contact { font-size: 12px; color: #7a7a7a; margin-bottom: 20px; }
        .contact span { margin-right: 18px; }
        h2 { font-family: 'Lora', serif; font-size: 14px; font-weight: 700; color: {{ $style['primary_color'] ?? '#1a1a1a' }}; text-transform: uppercase; letter-spacing: 1px; margin: 22px 0 10px 0; }
        .entry { margin-bottom: 15px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 12px; color: #9a9a9a; white-space: nowrap; font-style: italic; }
        .entry-title { font-weight: 600; font-size: 14px; color: #1a1a1a; }
        .entry-sub { font-size: 13px; color: #666; margin-top: 1px; }
        .entry-desc { font-size: 12.5px; line-height: 1.65; color: #4a4a4a; margin-top: 5px; }
        .skills-list { font-size: 12.5px; line-height: 1.8; color: #4a4a4a; margin: 0; padding: 0; list-style: none; }
        .skills-list li { padding-left: 12px; position: relative; }
        .skills-list li::before { content: '—'; position: absolute; left: 0; color: #9a9a9a; }
        .summary { font-size: 12.5px; line-height: 1.75; color: #4a4a4a; font-style: italic; margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="page">
    <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
    @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
    <div class="contact">
        <span>{{ $info['email'] ?? 'email@example.com' }}</span>
        @if(!empty($info['phone'])) <span>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</span> @endif
        @if(!empty($info['location'])) <span>{{ $info['location'] }}</span> @endif
    </div>

    @if(!empty($info['summary']))
    <div class="summary">{!! nl2br(e($info['summary'])) !!}</div>
    @endif

    @if($experience && !empty($experience->content))
    <h2>Work Experience</h2>
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

    @if($skills && !empty($skills->content))
    <h2>Skills</h2>
    <ul class="skills-list">
        @foreach($skills->content as $skill)
        <li>{{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) — {{ $skill['level'] }}@endif</li>
        @endforeach
    </ul>
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

    @if($languages && !empty($languages->content))
    <h2>Languages</h2>
    <ul class="skills-list">
        @foreach($languages->content as $lang)
        <li>{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</li>
        @endforeach
    </ul>
    @endif
</div>
</body>
</html>
