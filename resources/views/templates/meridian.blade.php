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

    $primary = $style['primary_color'] ?? '#2C3E50';
    $accent  = $style['secondary_color'] ?? '#5D6D7E';

    // Fresh-grad relabel: if work experience is empty, the Work label reads as optional.
    $hasWork = $experience && !empty($experience->content);
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@400;600;700&family=Inter:wght@400;500;600&display=swap');
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Inter' }}', sans-serif; background: {{ $style['background_color'] ?? '#ffffff' }}; color: #1f2937; }
        .page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 20mm 22mm; box-sizing: border-box; }

        /* Header */
        .name { font-family: '{{ $style['font_heading'] ?? 'Source Serif 4' }}', serif; font-size: 28px; font-weight: 700; letter-spacing: 1px; color: {{ $primary }}; margin: 0 0 2px 0; }
        .headline { font-size: 13px; color: {{ $accent }}; margin: 0 0 8px 0; }
        .rule { height: 2px; background: {{ $primary }}; margin: 6px 0 8px 0; }
        .contact { font-size: 11.5px; color: {{ $accent }}; margin-bottom: 6px; }
        .contact .ic { color: {{ $primary }}; }
        .contact .sep { color: #cbd5e1; margin: 0 8px; }

        /* Left-label section table (dompdf-safe: real <table>, not flex) */
        .section { width: 100%; border-collapse: collapse; }
        .section td { vertical-align: top; padding: 8px 0; border-top: 1px solid #e5e7eb; }
        .section .label-cell { width: 90px; padding-right: 14px; }
        .label { font-family: '{{ $style['font_heading'] ?? 'Source Serif 4' }}', serif; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: {{ $primary }}; }
        .label.optional { color: #9ca3af; }

        .entry { margin-bottom: 10px; }
        .entry:last-child { margin-bottom: 0; }
        .entry-head { display: table; width: 100%; }
        .entry-left { display: table-cell; vertical-align: top; }
        .entry-right { display: table-cell; vertical-align: top; text-align: right; white-space: nowrap; font-size: 11.5px; color: #9ca3af; }
        .entry-title { font-size: 13px; font-weight: 600; color: #111827; }
        .entry-sub { font-size: 12px; color: {{ $accent }}; margin-top: 1px; }
        .entry-desc { font-size: 12px; line-height: 1.55; color: #374151; margin-top: 4px; }

        .inline-list { font-size: 12px; color: #374151; line-height: 1.6; }
        .inline-item { display: inline; }
        .inline-item::after { content: ' · '; color: #9ca3af; }
        .inline-item:last-child::after { content: ''; }

        .summary-text { font-size: 12px; line-height: 1.6; color: #374151; }
        .placeholder { font-size: 11.5px; color: #9ca3af; font-style: italic; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="name">{{ $info['name'] ?? 'Your Name' }}</div>
    @if(!empty($info['title'])) <div class="headline">{{ $info['title'] }}</div> @endif
    <div class="rule"></div>
    <div class="contact">
        <span class="ic">&#9993;</span> {{ $info['email'] ?? 'email@example.com' }}
        @if(!empty($info['phone']))
            <span class="sep">|</span><span class="ic">&#9742;</span> {{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}
        @endif
        @if(!empty($info['location']))
            <span class="sep">|</span><span class="ic">&#8962;</span> {{ $info['location'] }}
        @endif
    </div>

    <table class="section">

        {{-- Career objective / summary --}}
        @if(!empty($info['summary']))
        <tr>
            <td class="label-cell"><span class="label">Profile</span></td>
            <td><div class="summary-text">{!! nl2br(e($info['summary'])) !!}</div></td>
        </tr>
        @endif

        {{-- Education first — the anchor for a fresh graduate --}}
        @if($education && !empty($education->content))
        <tr>
            <td class="label-cell"><span class="label">Education</span></td>
            <td>
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
            </td>
        </tr>
        @endif

        {{-- Projects & organizations — the main experience block for fresh grads --}}
        @if($projects && !empty($projects->content))
        <tr>
            <td class="label-cell"><span class="label">Projects</span></td>
            <td>
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
            </td>
        </tr>
        @endif

        {{-- Skills --}}
        @if($skills && !empty($skills->content))
        <tr>
            <td class="label-cell"><span class="label">Skills</span></td>
            <td>
                <div class="inline-list">
                    @foreach($skills->content as $skill)
                    <span class="inline-item">{{ $skill['name'] ?? '' }}@if(!empty($skill['level'])) ({{ $skill['level'] }})@endif</span>
                    @endforeach
                </div>
            </td>
        </tr>
        @endif

        {{-- Certifications --}}
        @if($certifications && !empty($certifications->content))
        <tr>
            <td class="label-cell"><span class="label">Certs</span></td>
            <td>
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
            </td>
        </tr>
        @endif

        {{-- Languages --}}
        @if($languages && !empty($languages->content))
        <tr>
            <td class="label-cell"><span class="label">Languages</span></td>
            <td>
                <div class="inline-list">
                    @foreach($languages->content as $lang)
                    <span class="inline-item">{{ $lang['name'] ?? '' }}@if(!empty($lang['level'])) ({{ $lang['level'] }})@endif</span>
                    @endforeach
                </div>
            </td>
        </tr>
        @endif

        {{-- Work experience — optional, appears last for fresh grads --}}
        @if($hasWork)
        <tr>
            <td class="label-cell"><span class="label">Experience</span></td>
            <td>
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
            </td>
        </tr>
        @endif

    </table>
</div>
</body>
</html>
