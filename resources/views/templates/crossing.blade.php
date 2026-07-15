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

    $primary = $style['primary_color'] ?? '#0F6E56';
    $accent  = $style['secondary_color'] ?? '#0d9488';
    $soft    = $style['soft_color'] ?? '#e6f4f0';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@400;600;700&family=Inter:wght@400;500;600&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Inter' }}', sans-serif; background: {{ $style['background_color'] ?? '#ffffff' }}; color: #1f2937; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 20mm 22mm; box-sizing: border-box; }

        /* Header — centered, distinct from Meridian's left-aligned header */
        .header { text-align: center; margin-bottom: 4px; }
        .name { font-family: '{{ $style['font_heading'] ?? 'Fraunces' }}', serif; font-size: 30px; font-weight: 700; color: {{ $primary }}; margin: 0; }
        .headline { font-size: 13px; color: {{ $accent }}; margin: 3px 0 0 0; font-weight: 500; }
        .contact { text-align: center; font-size: 11.5px; color: #6b7280; margin: 6px 0 0 0; }
        .contact .sep { color: #d1d5db; margin: 0 7px; }
        .divider { height: 1px; background: #e5e7eb; margin: 14px 0 16px 0; }

        /* Positioning statement — the career switcher's transition narrative */
        .positioning { background: {{ $soft }}; border-left: 3px solid {{ $primary }}; border-radius: 0; padding: 12px 16px; margin-bottom: 18px; }
        .positioning-text { font-size: 12.5px; line-height: 1.6; color: #374151; margin: 0; }

        /* Transferable skills block — pushed to the top, prominent chips */
        .block-label { font-family: '{{ $style['font_heading'] ?? 'Fraunces' }}', serif; font-size: 13px; font-weight: 700; color: {{ $primary }}; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px 0; }
        .skills-wrap { margin-bottom: 20px; }
        .chip { display: inline-block; background: {{ $soft }}; color: {{ $primary }}; border-radius: 4px; padding: 4px 11px; font-size: 12px; font-weight: 500; margin: 0 5px 6px 0; }

        h2 { font-family: '{{ $style['font_heading'] ?? 'Fraunces' }}', serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: {{ $primary }}; margin: 20px 0 8px 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }

        .entry { margin-bottom: 13px; }
        .entry-head { display: table; width: 100%; }
        .entry-left { display: table-cell; vertical-align: top; }
        .entry-right { display: table-cell; vertical-align: top; text-align: right; white-space: nowrap; font-size: 11.5px; color: #9ca3af; }
        .entry-title { font-size: 13.5px; font-weight: 600; color: #111827; }
        .entry-sub { font-size: 12px; color: {{ $accent }}; margin-top: 1px; }
        .entry-desc { font-size: 12px; line-height: 1.55; color: #374151; margin-top: 4px; }

        .inline-list { font-size: 12px; color: #374151; line-height: 1.7; }
        .inline-item { display: inline; }
        .inline-item::after { content: ' · '; color: #9ca3af; }
        .inline-item:last-child::after { content: ''; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
        @if(!empty($info['title'])) <div class="headline">{{ $info['title'] }}</div> @endif
        <div class="contact">
            {{ $info['email'] ?? 'email@example.com' }}
            @if(!empty($info['phone']))<span class="sep">|</span>{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}@endif
            @if(!empty($info['location']))<span class="sep">|</span>{{ $info['location'] }}@endif
        </div>
    </div>
    <div class="divider"></div>

    {{-- Positioning statement: where the switcher frames their transition --}}
    @if(!empty($info['summary']))
    <div class="positioning">
        <p class="positioning-text">{!! nl2br(e($info['summary'])) !!}</p>
    </div>
    @endif

    {{-- Transferable skills FIRST — the whole point for a career switcher --}}
    @if($skills && !empty($skills->content))
    <div class="skills-wrap">
        <div class="block-label">Transferable Skills</div>
        @foreach($skills->content as $skill)
        <span class="chip">{{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) · {{ $skill['level'] }}@endif</span>
        @endforeach
    </div>
    @endif

    {{-- Projects — evidence of capability outside the old job title --}}
    @if($projects && !empty($projects->content))
    <h2>Projects &amp; Portfolio</h2>
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

    {{-- Work experience — kept chronological with dates so ATS stays happy --}}
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

    {{-- Certifications — often key for switchers proving new-field credentials --}}
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
    <div class="inline-list">
        @foreach($languages->content as $lang)
        <span class="inline-item">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
        @endforeach
    </div>
    @endif

</div>
</body>
</html>
