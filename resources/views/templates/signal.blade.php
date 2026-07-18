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

    // Monochrome by design — maximum parser compatibility, zero decorative color.
    $ink = $style['primary_color'] ?? '#000000';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Arial&display=swap');
        @page { margin: 0; }
        /* Arial/Helvetica: the most reliably-parsed sans on ATS systems. */
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Arial' }}', Helvetica, sans-serif; background: #ffffff; color: {{ $ink }}; font-size: 11px; line-height: 1.4; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 14mm 16mm; box-sizing: border-box; }

        /* Header — compact, single line of contact, no wasted vertical space */
        .name { font-size: 20px; font-weight: 700; color: {{ $ink }}; margin: 0 0 2px 0; letter-spacing: 0.3px; }
        .headline { font-size: 12px; font-weight: 700; color: {{ $ink }}; margin: 0 0 3px 0; }
        .contact { font-size: 10.5px; color: {{ $ink }}; margin-bottom: 8px; }
        .contact .sep { margin: 0 5px; }

        h2 { font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: {{ $ink }}; margin: 11px 0 4px 0; border-bottom: 1.5px solid {{ $ink }}; padding-bottom: 2px; }

        .summary { font-size: 11px; line-height: 1.45; color: {{ $ink }}; margin-bottom: 4px; }

        /* Tight entries — dates inline, minimal spacing between items */
        .entry { margin-bottom: 7px; }
        .entry-head { display: table; width: 100%; }
        .entry-left { display: table-cell; vertical-align: top; }
        .entry-right { display: table-cell; vertical-align: top; text-align: right; white-space: nowrap; font-size: 10.5px; font-weight: 700; color: {{ $ink }}; }
        .entry-title { font-size: 11.5px; font-weight: 700; color: {{ $ink }}; }
        .entry-sub { font-size: 11px; font-style: italic; color: {{ $ink }}; }
        .entry-desc { font-size: 10.5px; line-height: 1.4; color: {{ $ink }}; margin-top: 2px; }
        .entry-desc ul { margin: 2px 0 0 0; padding-left: 16px; }

        /* Skills as a dense comma list — keyword-rich, no chips/graphics */
        .skills-line { font-size: 11px; line-height: 1.5; color: {{ $ink }}; }
        .skill-item { display: inline; }
        .skill-item::after { content: ', '; }
        .skill-item:last-child::after { content: ''; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
    @if(!empty($info['title'])) <div class="headline">{{ $info['title'] }}</div> @endif
    <div class="contact">
        {{ $info['email'] ?? 'email@example.com' }}
        @if(!empty($info['phone']))<span class="sep">•</span>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}@endif
        @if(!empty($info['location']))<span class="sep">•</span>{{ $info['location'] }}@endif
    </div>

    {{-- Professional summary — keyword-dense opening --}}
    @if(!empty($info['summary']))
    <h2>Professional Summary</h2>
    <div class="summary">{!! nl2br(e($info['summary'])) !!}</div>
    @endif

    {{-- Skills near the top — ATS keyword matching happens here --}}
    @if($skills && !empty($skills->content))
    <h2>Core Skills</h2>
    <div class="skills-line">
        @foreach($skills->content as $skill)
        <span class="skill-item">{{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) ({{ $skill['level'] }})@endif</span>
        @endforeach
    </div>
    @endif

    {{-- Experience — the main event, dense and dated --}}
    @if($experience && !empty($experience->content))
    <h2>Professional Experience</h2>
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

    {{-- Projects --}}
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

    {{-- Education --}}
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

    {{-- Certifications --}}
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

    {{-- Languages --}}
    @if($languages && !empty($languages->content))
    <h2>Languages</h2>
    <div class="skills-line">
        @foreach($languages->content as $lang)
        <span class="skill-item">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
        @endforeach
    </div>
    @endif

</div>
</body>
</html>
