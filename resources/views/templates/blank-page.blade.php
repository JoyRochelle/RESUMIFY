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
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Open Sans', sans-serif; background: #ffffff; color: #374151; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 24mm 26mm; box-sizing: border-box; }
        .name { font-size: 28px; font-weight: 700; color: #111827; letter-spacing: -0.3px; margin: 0 0 3px 0; }
        .title-role { font-size: 13.5px; font-weight: 300; color: #9ca3af; margin: 0 0 10px 0; letter-spacing: 1px; text-transform: uppercase; }
        .contact-row { display: table; width: 100%; margin-bottom: 24px; }
        .contact-cell { display: table-cell; font-size: 11.5px; color: #9ca3af; border-top: 1px solid #f3f4f6; border-bottom: 1px solid #f3f4f6; padding: 6px 0; }
        .contact-cell + .contact-cell { border-left: 1px solid #f3f4f6; padding-left: 14px; }
        h2 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: #9ca3af; margin: 22px 0 10px 0; }
        .entry { margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid #f9fafb; }
        .entry:last-child { border-bottom: none; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #d1d5db; white-space: nowrap; }
        .entry-title { font-size: 13.5px; font-weight: 600; color: #111827; }
        .entry-sub { font-size: 12.5px; color: #9ca3af; margin-top: 2px; }
        .entry-desc { font-size: 12px; line-height: 1.7; color: #6b7280; margin-top: 6px; }
        .skills-wrap { font-size: 12px; color: #6b7280; line-height: 2; }
        .skill-chip { display: inline-block; border: 1px solid #e5e7eb; padding: 2px 9px; border-radius: 20px; margin: 2px 4px 2px 0; font-size: 11.5px; color: #374151; }
        .summary { font-size: 12.5px; line-height: 1.75; color: #6b7280; margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="page">
    <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
    @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
    <div class="contact-row">
        <div class="contact-cell">{{ $info['email'] ?? 'email@example.com' }}</div>
        @if(!empty($info['phone'])) <div class="contact-cell">{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div> @endif
        @if(!empty($info['location'])) <div class="contact-cell">{{ $info['location'] }}</div> @endif
    </div>

    @if(!empty($info['summary']))
    <div class="summary">{!! nl2br(e($info['summary'])) !!}</div>
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
    <div class="skills-wrap">
        @foreach($skills->content as $skill)
        <span class="skill-chip">{{ $skill['name'] ?? '' }}</span>
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

    @if($experience && !empty($experience->content))
    <h2>Experience & Volunteering</h2>
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

    @if($languages && !empty($languages->content))
    <h2>Languages</h2>
    <div class="skills-wrap">
        @foreach($languages->content as $lang)
        <span class="skill-chip">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
        @endforeach
    </div>
    @endif
</div>
</body>
</html>
