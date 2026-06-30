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
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,300&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Merriweather', serif; background: #ffffff; color: #111827; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 20mm 24mm; box-sizing: border-box; }
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 1px solid {{ $style['secondary_color'] ?? '#92722a' }}; padding-bottom: 12px; margin-bottom: 16px; display: table; }
        .header-name-cell { display: table-cell; vertical-align: bottom; }
        .name { font-size: 26px; font-weight: 700; color: #111827; margin: 0 0 3px 0; }
        .title-role { font-size: 13px; font-weight: 300; color: #6b7280; margin: 0; }
        .header-contact-cell { display: table-cell; vertical-align: bottom; text-align: right; font-size: 11px; color: #9ca3af; line-height: 1.8; }
        h2 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: {{ $style['secondary_color'] ?? '#92722a' }}; margin: 20px 0 4px 0; }
        .gold-rule { border: none; border-top: 1px solid {{ $style['secondary_color'] ?? '#92722a' }}; margin-bottom: 10px; }
        .entry { margin-bottom: 14px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #9ca3af; white-space: nowrap; font-weight: 300; }
        .entry-title { font-size: 13.5px; font-weight: 700; color: #111827; }
        .entry-sub { font-size: 12.5px; color: #6b7280; margin-top: 1px; font-weight: 300; }
        .entry-desc { font-size: 12px; line-height: 1.7; color: #374151; margin-top: 6px; font-weight: 300; }
        .skills-line { font-size: 12px; color: #374151; line-height: 1.9; font-weight: 300; }
        .summary { font-size: 12px; line-height: 1.75; color: #4b5563; margin-bottom: 8px; font-weight: 300; font-style: italic; }
    </style>
</head>
<body>
<div class="page">
    <div class="header-table">
        <div class="header-name-cell">
            <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
            @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
        </div>
        <div class="header-contact-cell">
            <div>{{ $info['email'] ?? 'email@example.com' }}</div>
            @if(!empty($info['phone'])) <div>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div> @endif
            @if(!empty($info['location'])) <div>{{ $info['location'] }}</div> @endif
        </div>
    </div>

    @if(!empty($info['summary']))
    <div class="summary">{!! nl2br(e($info['summary'])) !!}</div>
    @endif

    @if($experience && !empty($experience->content))
    <h2>Experience</h2>
    <hr class="gold-rule">
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
    <hr class="gold-rule">
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
    <hr class="gold-rule">
    <div class="skills-line">
        @foreach($skills->content as $skill)
        {{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) ({{ $skill['level'] }})@endif@if(!$loop->last) &nbsp;·&nbsp; @endif
        @endforeach
    </div>
    @endif

    @if($certifications && !empty($certifications->content))
    <h2>Certifications</h2>
    <hr class="gold-rule">
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
    <hr class="gold-rule">
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
    <hr class="gold-rule">
    <div class="skills-line">
        @foreach($languages->content as $lang)
        {{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif@if(!$loop->last) &nbsp;·&nbsp; @endif
        @endforeach
    </div>
    @endif
</div>
</body>
</html>
