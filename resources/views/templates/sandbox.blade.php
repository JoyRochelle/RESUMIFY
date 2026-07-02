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
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background: #ffffff; color: #1c0a00; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; }
        .accent { height: 8px; background: {{ $style['primary_color'] ?? '#ea580c' }}; }
        .header { padding: 22px 28px 16px 28px; border-bottom: 1px solid #fed7aa; }
        .name { font-size: 28px; font-weight: 700; color: {{ $style['primary_color'] ?? '#ea580c' }}; margin: 0 0 3px 0; }
        .title-role { font-size: 13.5px; color: #6b7280; margin: 0 0 10px 0; font-weight: 400; }
        .contact { font-size: 11.5px; color: #9ca3af; }
        .contact span { margin-right: 16px; }
        .body { padding: 18px 28px; }
        h2 { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 2.5px; color: {{ $style['primary_color'] ?? '#ea580c' }}; margin: 20px 0 10px 0; padding-bottom: 4px; border-bottom: 2px solid {{ $style['primary_color'] ?? '#ea580c' }}; }
        .project-card { border: 1px solid #fed7aa; border-radius: 6px; padding: 12px 14px; margin-bottom: 12px; }
        .project-title { font-size: 14px; font-weight: 600; color: #c2410c; }
        .project-url { font-size: 11.5px; color: #9ca3af; margin-top: 1px; }
        .project-desc { font-size: 12px; line-height: 1.65; color: #374151; margin-top: 6px; }
        .entry { margin-bottom: 14px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #d1d5db; white-space: nowrap; }
        .entry-title { font-size: 13.5px; font-weight: 600; color: #111827; }
        .entry-sub { font-size: 12.5px; color: #6b7280; margin-top: 1px; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #374151; margin-top: 5px; }
        .skill-chip { display: inline-block; background: #fff7ed; color: {{ $style['primary_color'] ?? '#ea580c' }}; border: 1px solid #fed7aa; padding: 3px 10px; border-radius: 4px; font-size: 11.5px; margin: 2px 4px 2px 0; font-weight: 500; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="page">
    <div class="accent"></div>
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

        {{-- Projects highlighted first --}}
        @if($projects && !empty($projects->content))
        <h2>Side Projects &amp; Portfolio</h2>
        @foreach($projects->content as $proj)
        <div class="project-card">
            <div class="project-title">{{ $proj['name'] ?? '' }}</div>
            @if(!empty($proj['url'])) <div class="project-url">{{ $proj['url'] }}</div> @endif
            @if(!empty($proj['description'])) <div class="project-desc">{!! nl2br(e($proj['description'])) !!}</div> @endif
        </div>
        @endforeach
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

        @if($skills && !empty($skills->content))
        <h2>Skills</h2>
        <div>
            @foreach($skills->content as $skill)
            <span class="skill-chip">{{ $skill['name'] ?? '' }}</span>
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
        <div>
            @foreach($languages->content as $lang)
            <span class="skill-chip">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
            @endforeach
        </div>
        @endif
    </div>
</div>
</body>
</html>
