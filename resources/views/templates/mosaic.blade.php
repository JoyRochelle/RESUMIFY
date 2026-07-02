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
        @import url('https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&family=Inter:wght@300;400;500&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; background: #ffffff; color: #1a1a1a; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; }
        /* Mosaic header: 2-column with photo */
        .header-table { display: table; width: 100%; background: {{ $style['primary_color'] ?? '#7c3aed' }}; }
        .header-left { display: table-cell; vertical-align: middle; padding: 28px 28px 22px 30px; }
        .header-right { display: table-cell; vertical-align: middle; width: 110px; text-align: center; padding-right: 28px; }
        .name { font-family: 'Oswald', sans-serif; font-size: 32px; font-weight: 700; color: #ffffff; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        .title-role { font-size: 13px; color: rgba(255,255,255,0.7); margin: 0 0 12px 0; font-weight: 300; letter-spacing: 1px; }
        .contact { font-size: 11.5px; color: rgba(255,255,255,0.65); line-height: 1.8; }
        .photo { width: 90px; height: 90px; object-fit: cover; border-radius: 8px; border: 3px solid rgba(255,255,255,0.4); display: block; margin: 0 auto; }
        /* Content area with accent column on left */
        .content-table { display: table; width: 100%; }
        .accent-col { display: table-cell; width: 8px; background: {{ $style['secondary_color'] ?? '#ea580c' }}; }
        .content-col { display: table-cell; vertical-align: top; padding: 22px 28px; }
        h2 { font-family: 'Oswald', sans-serif; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: {{ $style['primary_color'] ?? '#7c3aed' }}; margin: 20px 0 8px 0; padding-bottom: 3px; border-bottom: 2px solid {{ $style['secondary_color'] ?? '#ea580c' }}; }
        .entry { margin-bottom: 14px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #9ca3af; white-space: nowrap; font-weight: 500; }
        .entry-title { font-size: 13.5px; font-weight: 600; color: #111827; }
        .entry-sub { font-size: 12.5px; color: #7c3aed; margin-top: 1px; font-weight: 500; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #374151; margin-top: 5px; }
        .skill-mosaic { display: inline-block; padding: 3px 10px; border-radius: 3px; font-size: 11.5px; margin: 2px 4px 2px 0; }
        .skill-1 { background: #ede9fe; color: #7c3aed; border: 1px solid #c4b5fd; }
        .skill-2 { background: #fff7ed; color: #ea580c; border: 1px solid #fdba74; }
        .skill-3 { background: #f0fdf4; color: #16a34a; border: 1px solid #86efac; }
        .summary { font-size: 12.5px; line-height: 1.7; color: #374151; margin-bottom: 6px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header-table">
        <div class="header-left">
            <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
            @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
            <div class="contact">
                <div>{{ $info['email'] ?? 'email@example.com' }}</div>
                @if(!empty($info['phone'])) <div>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div> @endif
                @if(!empty($info['location'])) <div>{{ $info['location'] }}</div> @endif
            </div>
        </div>
        @if(!empty($style['show_photo']) && !empty($info['photo']))
        <div class="header-right">
            <img src="{{ $info['photo'] }}" alt="Profile" class="photo">
        </div>
        @endif
    </div>

    <div class="content-table">
        <div class="accent-col"></div>
        <div class="content-col">
            @if(!empty($info['summary']))
            <div class="summary" style="margin-top:2px;">{!! nl2br(e($info['summary'])) !!}</div>
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
            <h2>Portfolio &amp; Projects</h2>
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
                @foreach($skills->content as $i => $skill)
                <span class="skill-mosaic skill-{{ ($i % 3) + 1 }}">{{ $skill['name'] ?? '' }}</span>
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
            <div>
                @foreach($certifications->content as $cert)
                <span class="skill-mosaic skill-2">{{ $cert['name'] ?? '' }}</span>
                @endforeach
            </div>
            @endif

            @if($languages && !empty($languages->content))
            <h2>Languages</h2>
            <div>
                @foreach($languages->content as $lang)
                <span class="skill-mosaic skill-3">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
