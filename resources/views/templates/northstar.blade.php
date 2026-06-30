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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #ffffff; color: #1e3a5f; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; }
        .header { background: {{ $style['primary_color'] ?? '#2563eb' }}; padding: 26px 30px 20px 30px; }
        .name { font-size: 30px; font-weight: 700; color: #ffffff; margin: 0 0 3px 0; }
        .title-role { font-size: 14px; color: rgba(255,255,255,0.8); margin: 0 0 14px 0; }
        .contact-row { display: table; width: 100%; }
        .contact-cell { display: table-cell; font-size: 11.5px; color: rgba(255,255,255,0.65); padding-right: 20px; }
        .body { padding: 20px 30px; }
        h2 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: {{ $style['primary_color'] ?? '#2563eb' }}; margin: 20px 0 8px 0; padding-bottom: 3px; border-bottom: 2px solid {{ $style['primary_color'] ?? '#2563eb' }}; }
        .entry { margin-bottom: 15px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #93c5fd; white-space: nowrap; font-weight: 500; }
        .entry-title { font-size: 13.5px; font-weight: 600; color: #0f172a; }
        .entry-sub { font-size: 12.5px; color: {{ $style['primary_color'] ?? '#2563eb' }}; margin-top: 1px; font-weight: 500; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #374151; margin-top: 5px; }
        {{-- metric highlight --}}
        .metric { display: inline-block; background: #dbeafe; color: {{ $style['primary_color'] ?? '#2563eb' }}; font-weight: 700; font-size: 11.5px; padding: 1px 7px; border-radius: 3px; margin-left: 4px; }
        .skills-row { font-size: 12px; color: #374151; line-height: 1.9; }
        .skill-badge { display: inline-block; border: 1px solid #bfdbfe; padding: 3px 10px; border-radius: 4px; font-size: 11.5px; color: #1d4ed8; margin: 2px 4px 2px 0; font-weight: 500; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
        @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
        <div class="contact-row">
            <div class="contact-cell">{{ $info['email'] ?? 'email@example.com' }}</div>
            @if(!empty($info['phone'])) <div class="contact-cell">{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div> @endif
            @if(!empty($info['location'])) <div class="contact-cell">{{ $info['location'] }}</div> @endif
        </div>
    </div>
    <div class="body">
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
        <div>
            @foreach($skills->content as $skill)
            <span class="skill-badge">{{ $skill['name'] ?? '' }}</span>
            @endforeach
        </div>
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
        <div class="skills-row">
            @foreach($languages->content as $lang)
            {{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif@if(!$loop->last) &nbsp;·&nbsp; @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
</body>
</html>
