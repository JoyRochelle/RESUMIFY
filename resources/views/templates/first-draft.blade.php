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
    $hasWorkExp = $experience && !empty(array_filter($experience->content ?? [], fn($j) => !empty($j['company'])));
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #ffffff; color: #374151; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 22mm 24mm; box-sizing: border-box; }
        .name { font-size: 27px; font-weight: 700; color: #111827; margin: 0 0 2px 0; }
        .title-role { font-size: 14px; color: #6b7280; margin: 0 0 10px 0; }
        .contact { font-size: 11.5px; color: #9ca3af; margin-bottom: 18px; }
        .contact span { margin-right: 14px; }
        .section-title { display: table; width: 100%; margin: 20px 0 10px 0; }
        .section-title-text { display: table-cell; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: {{ $style['primary_color'] ?? '#374151' }}; vertical-align: middle; white-space: nowrap; padding-right: 10px; }
        .section-title-line { display: table-cell; width: 100%; vertical-align: middle; }
        .section-title-line hr { border: none; border-top: 1px solid #e5e7eb; margin: 0; }
        .entry { margin-bottom: 14px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #9ca3af; white-space: nowrap; font-weight: 500; }
        .entry-title { font-size: 13.5px; font-weight: 600; color: #111827; }
        .entry-sub { font-size: 12.5px; color: #6b7280; margin-top: 1px; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #4b5563; margin-top: 5px; }
        .skills-inline { font-size: 12px; color: #4b5563; line-height: 1.9; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #4b5563; margin-bottom: 4px; }
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

    {{-- Education first for fresh grad --}}
    @if($education && !empty($education->content))
    <div class="section-title"><div class="section-title-text">Education</div><div class="section-title-line"><hr></div></div>
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
    <div class="section-title"><div class="section-title-text">{{ $hasWorkExp ? 'Work Experience' : 'Magang & Organisasi' }}</div><div class="section-title-line"><hr></div></div>
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
    <div class="section-title"><div class="section-title-text">Projects</div><div class="section-title-line"><hr></div></div>
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
    <div class="section-title"><div class="section-title-text">Skills</div><div class="section-title-line"><hr></div></div>
    <div class="skills-inline">
        @foreach($skills->content as $skill)
        {{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) ({{ $skill['level'] }})@endif@if(!$loop->last)&nbsp;·&nbsp;@endif
        @endforeach
    </div>
    @endif

    @if($certifications && !empty($certifications->content))
    <div class="section-title"><div class="section-title-text">Certifications</div><div class="section-title-line"><hr></div></div>
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
    <div class="section-title"><div class="section-title-text">Languages</div><div class="section-title-line"><hr></div></div>
    <div class="skills-inline">
        @foreach($languages->content as $lang)
        {{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif@if(!$loop->last)&nbsp;·&nbsp;@endif
        @endforeach
    </div>
    @endif
</div>
</body>
</html>
