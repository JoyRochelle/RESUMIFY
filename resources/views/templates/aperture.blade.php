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
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; background: #1a1a1a; color: #ffffff; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; background: #1a1a1a; }
        .header { background: #111111; padding: 30px 30px 24px 30px; border-bottom: 3px solid {{ $style['secondary_color'] ?? '#eab308' }}; display: table; width: 100%; box-sizing: border-box; }
        .header-name-col { display: table-cell; vertical-align: middle; }
        .header-photo-col { display: table-cell; vertical-align: middle; width: 120px; text-align: right; }
        .photo { width: 90px; height: 90px; object-fit: cover; border-radius: 4px; border: 2px solid {{ $style['secondary_color'] ?? '#eab308' }}; }
        .name { font-size: 34px; font-weight: 800; color: #ffffff; margin: 0 0 3px 0; line-height: 1.1; }
        .title-role { font-size: 13px; color: {{ $style['secondary_color'] ?? '#eab308' }}; margin: 0 0 12px 0; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; }
        .contact { font-size: 11.5px; color: #9ca3af; line-height: 1.7; }
        .body { padding: 26px 30px; }
        h2 { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 3px; color: {{ $style['secondary_color'] ?? '#eab308' }}; margin: 22px 0 10px 0; }
        .h2-rule { border: none; border-top: 1px solid #2d2d2d; margin-bottom: 12px; }
        .entry { margin-bottom: 15px; }
        .entry-row { display: table; width: 100%; }
        .entry-main { display: table-cell; vertical-align: top; }
        .entry-date { display: table-cell; vertical-align: top; text-align: right; font-size: 11px; color: #6b7280; white-space: nowrap; font-weight: 600; }
        .entry-title { font-size: 14px; font-weight: 700; color: #f9fafb; }
        .entry-sub { font-size: 12.5px; color: #9ca3af; margin-top: 1px; font-weight: 400; }
        .entry-desc { font-size: 12px; line-height: 1.65; color: #d1d5db; margin-top: 5px; }
        .skill-tag { display: inline-block; background: #222222; border: 1px solid #333333; color: #e5e7eb; padding: 3px 10px; border-radius: 3px; font-size: 11.5px; margin: 2px 4px 2px 0; font-weight: 500; }
        .summary { font-size: 12.5px; line-height: 1.75; color: #d1d5db; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="header-name-col">
            <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
            @if(!empty($info['title'])) <div class="title-role">{{ $info['title'] }}</div> @endif
            <div class="contact">
                <span>{{ $info['email'] ?? 'email@example.com' }}</span>
                @if(!empty($info['phone'])) &nbsp;·&nbsp; <span>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</span> @endif
                @if(!empty($info['location'])) &nbsp;·&nbsp; <span>{{ $info['location'] }}</span> @endif
            </div>
        </div>
        @if(!empty($style['show_photo']) && !empty($info['photo']))
        <div class="header-photo-col">
            <img src="{{ $info['photo'] }}" alt="Profile" class="photo">
        </div>
        @endif
    </div>
    <div class="body">
        @if(!empty($info['summary']))
        <div class="summary" style="margin-top:4px;">{!! nl2br(e($info['summary'])) !!}</div>
        @endif

        @if($experience && !empty($experience->content))
        <h2>Experience</h2>
        <hr class="h2-rule">
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
        <h2>Portfolio &amp; Work</h2>
        <hr class="h2-rule">
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
        <h2>Skills &amp; Tools</h2>
        <hr class="h2-rule">
        <div>
            @foreach($skills->content as $skill)
            <span class="skill-tag">{{ $skill['name'] ?? '' }}</span>
            @endforeach
        </div>
        @endif

        @if($education && !empty($education->content))
        <h2>Education</h2>
        <hr class="h2-rule">
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
        <hr class="h2-rule">
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
        <hr class="h2-rule">
        <div>
            @foreach($languages->content as $lang)
            <span class="skill-tag">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
            @endforeach
        </div>
        @endif
    </div>
</div>
</body>
</html>
