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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Fira+Code:wght@400;500;600&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #ffffff; color: #1e1b4b; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; }
        .header { background: {{ $style['primary_color'] ?? '#7c3aed' }}; padding: 28px 30px 22px 30px; }
        .name { font-size: 28px; font-weight: 700; color: #ffffff; margin: 0 0 3px 0; }
        .title-role { font-size: 13px; color: rgba(255,255,255,0.75); margin: 0 0 12px 0; font-weight: 400; }
        .contact-row { display: table; width: 100%; }
        .contact-item { display: table-cell; font-size: 11.5px; color: rgba(255,255,255,0.65); padding-right: 20px; }
        .body { padding: 20px 30px; }
        {{-- SKILL-FIRST section --}}
        .skills-section { background: #faf5ff; border-radius: 6px; padding: 14px 16px; margin-bottom: 18px; }
        .skills-section-label { font-family: 'Fira Code', monospace; font-size: 10px; font-weight: 600; color: {{ $style['primary_color'] ?? '#7c3aed' }}; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
        .skill-tag { display: inline-block; background: #ede9fe; color: {{ $style['primary_color'] ?? '#7c3aed' }}; border-radius: 4px; padding: 3px 10px; font-size: 11.5px; font-family: 'Fira Code', monospace; margin: 2px 4px 2px 0; font-weight: 500; }
        h2 { font-family: 'Fira Code', monospace; font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: {{ $style['primary_color'] ?? '#7c3aed' }}; margin: 18px 0 8px 0; }
        .entry { margin-bottom: 13px; padding-bottom: 13px; border-bottom: 1px solid #f5f3ff; }
        .entry:last-child { border-bottom: none; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-family: 'Fira Code', monospace; font-size: 10.5px; color: #a78bfa; white-space: nowrap; }
        .entry-title { font-size: 13.5px; font-weight: 600; color: #1e1b4b; }
        .entry-sub { font-size: 12.5px; color: #7c3aed; margin-top: 1px; font-weight: 500; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #4c1d95; margin-top: 5px; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
        @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
        <div class="contact-row">
            <div class="contact-item">{{ $info['email'] ?? 'email@example.com' }}</div>
            @if(!empty($info['phone'])) <div class="contact-item">{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div> @endif
            @if(!empty($info['location'])) <div class="contact-item">{{ $info['location'] }}</div> @endif
        </div>
    </div>
    <div class="body">
        {{-- Skills FIRST --}}
        @if($skills && !empty($skills->content))
        <div class="skills-section">
            <div class="skills-section-label">// Tech Stack &amp; Skills</div>
            @foreach($skills->content as $skill)
            <span class="skill-tag">{{ $skill['name'] ?? '' }}</span>
            @endforeach
        </div>
        @endif

        @if(!empty($info['summary']))
        <div class="summary">{!! nl2br(e($info['summary'])) !!}</div>
        @endif

        @if($experience && !empty($experience->content))
        <h2>// Experience</h2>
        @foreach($experience->content as $job)
        <div class="entry">
            <div class="entry-row">
                <div class="entry-main">
                    <div class="entry-title">{{ $job['title'] ?? '' }}</div>
                    <div class="entry-sub">{{ $job['company'] ?? '' }}</div>
                </div>
                <div class="entry-date">{{ $job['start_date'] ?? '' }}{{ !empty($job['start_date']) ? '–' : '' }}{{ $job['end_date'] ?? 'now' }}</div>
            </div>
            @if(!empty($job['description'])) <div class="entry-desc">{!! nl2br(e($job['description'])) !!}</div> @endif
        </div>
        @endforeach
        @endif

        @if($projects && !empty($projects->content))
        <h2>// Projects</h2>
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
        <h2>// Education</h2>
        @foreach($education->content as $edu)
        <div class="entry">
            <div class="entry-row">
                <div class="entry-main">
                    <div class="entry-title">{{ $edu['degree'] ?? '' }}</div>
                    <div class="entry-sub">{{ $edu['school'] ?? '' }}</div>
                </div>
                <div class="entry-date">{{ $edu['start_date'] ?? '' }}{{ !empty($edu['start_date']) ? '–' : '' }}{{ $edu['end_date'] ?? '' }}</div>
            </div>
        </div>
        @endforeach
        @endif

        @if($certifications && !empty($certifications->content))
        <h2>// Certifications</h2>
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
        <h2>// Languages</h2>
        <div style="font-size:12px; color:#4c1d95;">
            @foreach($languages->content as $lang)
            {{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif@if(!$loop->last) &nbsp;·&nbsp; @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
</body>
</html>
