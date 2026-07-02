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
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;600;700&family=Inter:wght@400;500;600&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: {{ $style['background_color'] ?? '#ffffff' }}; color: #1f2937; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 20mm 22mm; box-sizing: border-box; }
        .name { font-family: 'Source Serif 4', serif; font-size: 30px; font-weight: 700; color: {{ $style['primary_color'] ?? '#1f2937' }}; margin: 0 0 4px 0; }
        .title { font-size: 14px; color: #6b7280; margin: 0 0 10px 0; }
        .contact { font-size: 12px; color: #6b7280; margin-bottom: 20px; }
        .contact span { margin-right: 14px; }
        h2 { font-family: 'Source Serif 4', serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: {{ $style['primary_color'] ?? '#1f2937' }}; margin: 22px 0 8px 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .entry { margin-bottom: 14px; }
        .entry-head { display: table; width: 100%; }
        .entry-left { display: table-cell; vertical-align: top; }
        .entry-right { display: table-cell; vertical-align: top; text-align: right; white-space: nowrap; font-size: 12px; color: #9ca3af; }
        .entry-title { font-size: 14px; font-weight: 600; color: #111827; }
        .entry-sub { font-size: 13px; color: #6b7280; margin-top: 1px; }
        .entry-desc { font-size: 12.5px; line-height: 1.6; color: #374151; margin-top: 5px; }
        .skills-wrap { display: block; font-size: 12.5px; color: #374151; line-height: 1.7; }
        .skill-item { display: inline; }
        .skill-item::after { content: ' · '; color: #9ca3af; }
        .skill-item:last-child::after { content: ''; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="page">
    <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
    @if(!empty($info['title'])) <div class="title">{{ $info['title'] }}</div> @endif
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
        <div class="entry-head">
            <div class="entry-left">
                <div class="entry-title">{{ $job['title'] ?? '' }}</div>
                <div class="entry-sub">{{ $job['company'] ?? '' }}</div>
            </div>
            <div class="entry-right">{{ $job['start_date'] ?? '' }}{{ !empty($job['start_date']) ? ' – ' : '' }}{{ $job['end_date'] ?? 'Present' }}</div>
        </div>
        @if(!empty($job['description'])) <div class="entry-desc">{!! nl2br(e($job['description'])) !!}</div> @endif
    </div>
    @endforeach
    @endif

    @if($education && !empty($education->content))
    <h2>Education</h2>
    @foreach($education->content as $edu)
    <div class="entry">
        <div class="entry-head">
            <div class="entry-left">
                <div class="entry-title">{{ $edu['degree'] ?? '' }}</div>
                <div class="entry-sub">{{ $edu['school'] ?? '' }}</div>
            </div>
            <div class="entry-right">{{ $edu['start_date'] ?? '' }}{{ !empty($edu['start_date']) ? ' – ' : '' }}{{ $edu['end_date'] ?? '' }}</div>
        </div>
        @if(!empty($edu['description'])) <div class="entry-desc">{!! nl2br(e($edu['description'])) !!}</div> @endif
    </div>
    @endforeach
    @endif

    @if($skills && !empty($skills->content))
    <h2>Skills</h2>
    <div class="skills-wrap">
        @foreach($skills->content as $skill)
        <span class="skill-item">{{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) ({{ $skill['level'] }})@endif</span>
        @endforeach
    </div>
    @endif

    @if($certifications && !empty($certifications->content))
    <h2>Certifications</h2>
    @foreach($certifications->content as $cert)
    <div class="entry">
        <div class="entry-head">
            <div class="entry-left">
                <div class="entry-title">{{ $cert['name'] ?? '' }}</div>
                <div class="entry-sub">{{ $cert['issuer'] ?? '' }}</div>
            </div>
            <div class="entry-right">{{ $cert['date'] ?? '' }}</div>
        </div>
    </div>
    @endforeach
    @endif

    @if($projects && !empty($projects->content))
    <h2>Projects</h2>
    @foreach($projects->content as $proj)
    <div class="entry">
        <div class="entry-head">
            <div class="entry-left">
                <div class="entry-title">{{ $proj['name'] ?? '' }}</div>
                @if(!empty($proj['url'])) <div class="entry-sub">{{ $proj['url'] }}</div> @endif
            </div>
            <div class="entry-right">{{ $proj['date'] ?? '' }}</div>
        </div>
        @if(!empty($proj['description'])) <div class="entry-desc">{!! nl2br(e($proj['description'])) !!}</div> @endif
    </div>
    @endforeach
    @endif

    @if($languages && !empty($languages->content))
    <h2>Languages</h2>
    <div class="skills-wrap">
        @foreach($languages->content as $lang)
        <span class="skill-item">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
        @endforeach
    </div>
    @endif
</div>
</body>
</html>
