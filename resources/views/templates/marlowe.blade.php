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
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@300;400;500;600&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #ffffff; color: #1a1a1a; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 22mm 24mm; box-sizing: border-box; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 3px solid {{ $style['primary_color'] ?? '#1a1a1a' }}; }
        .name { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 800; color: {{ $style['primary_color'] ?? '#1a1a1a' }}; margin: 0 0 4px 0; }
        .title-role { font-family: 'Playfair Display', serif; font-size: 15px; font-weight: 400; color: #6b7280; margin: 0 0 10px 0; }
        .contact { font-size: 12px; color: #6b7280; }
        .contact span { margin: 0 10px; }
        h2 { font-family: 'Playfair Display', serif; font-size: 14px; font-weight: 700; color: {{ $style['primary_color'] ?? '#1a1a1a' }}; text-transform: uppercase; letter-spacing: 1.5px; margin: 22px 0 10px 0; padding-bottom: 4px; border-bottom: 1px solid #d1d5db; }
        .entry { margin-bottom: 14px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11.5px; color: #9ca3af; white-space: nowrap; font-style: italic; }
        .entry-title { font-size: 14px; font-weight: 600; color: #0f172a; }
        .entry-sub { font-size: 13px; color: #6b7280; font-style: italic; margin-top: 1px; }
        .entry-desc { font-size: 12.5px; line-height: 1.65; color: #374151; margin-top: 5px; }
        .skills-line { font-size: 12.5px; color: #374151; line-height: 1.9; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #4b5563; margin-bottom: 6px; text-align: center; font-style: italic; }
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

    @if(!empty($info['summary']))
    <div class="summary">{!! nl2br(e($info['summary'])) !!}</div>
    @endif

    @if($experience && !empty($experience->content))
    <h2>Professional Experience</h2>
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
    <div class="skills-line">
        @foreach($languages->content as $lang)
        {{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif@if(!$loop->last) &nbsp;·&nbsp; @endif
        @endforeach
    </div>
    @endif
</div>
</body>
</html>
