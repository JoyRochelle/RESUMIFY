@php
    $personal   = $cv->sections->where('type', 'personal_info')->first();
    $experience = $cv->sections->where('type', 'work_experience')->first();
    $education  = $cv->sections->where('type', 'education')->first();
    $skills     = $cv->sections->where('type', 'skills')->first();
    $info       = $personal?->content ?? [];
    $style      = $cv->template->style_config ?? [];
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $cv->title ?? 'Resume' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&display=swap');
        
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Source Sans Pro' }}', sans-serif; }
        .resume-page { width: 210mm; min-height: 297mm; box-sizing: border-box; margin: 0 auto; background: {{ $style['background_color'] ?? '#ffffff' }}; }
        .layout { width: 100%; height: 100%; border-collapse: collapse; }
        .sidebar { width: 28%; background: {{ $style['secondary_color'] ?? '#1e293b' }}; color: #f8fafc; padding: 40px 25px; vertical-align: top; }
        .main { width: 72%; padding: 50px 40px; vertical-align: top; color: #334155; }
        .name { font-family: '{{ $style['font_heading'] ?? 'Source Sans Pro' }}', sans-serif; font-size: 38px; font-weight: 700; color: {{ $style['primary_color'] ?? '#3b82f6' }}; margin: 0 0 5px 0; line-height: 1.1; }
        .title { font-size: 18px; color: #64748b; font-weight: 600; margin-bottom: 40px; }
        .section-title { font-family: '{{ $style['font_heading'] ?? 'Source Sans Pro' }}', sans-serif; font-size: 20px; color: #0f172a; margin: 0 0 20px 0; font-weight: 700; }
        .block { margin-bottom: 25px; }
        .block-header { display: table; width: 100%; }
        .block-title { display: table-cell; font-weight: 700; font-size: 16px; color: #0f172a; }
        .block-date { display: table-cell; text-align: right; font-size: 13px; color: #64748b; font-weight: 600; }
        .block-subtitle { font-size: 15px; color: {{ $style['primary_color'] ?? '#3b82f6' }}; margin: 4px 0 8px 0; font-weight: 600; }
        .block-desc { font-size: 14px; line-height: 1.5; }
        .side-title { font-size: 16px; font-weight: 700; border-bottom: 1px solid #334155; padding-bottom: 5px; margin-bottom: 15px; margin-top: 35px; color: #cbd5e1; text-transform: uppercase; }
        .side-item { font-size: 14px; margin-bottom: 10px; color: #94a3b8; }
        .side-item strong { color: #f8fafc; display: block; margin-bottom: 2px;}
    </style>
</head>
<body>
    <div class="resume-page">
        <table class="layout">
            <tr>
                <td class="sidebar">
                    <div style="margin-top: 20px;">
                        <div class="side-title" style="margin-top: 0;">Contact</div>
                        <div class="side-item"><strong>Email</strong> {{ $info['email'] ?? 'email@example.com' }}</div>
                        @if(!empty($info['phone']))<div class="side-item"><strong>Phone</strong> {{ $info['phone'] }}</div>@endif
                        @if(!empty($info['location']))<div class="side-item"><strong>Location</strong> {{ $info['location'] }}</div>@endif
                    </div>
                    
                    @if($skills && !empty($skills->content))
                    <div>
                        <div class="side-title">Skills</div>
                        @foreach($skills->content as $skill)
                            <div class="side-item"><strong>{{ $skill['name'] ?? '' }}</strong> {{ $skill['level'] ?? '' }}</div>
                        @endforeach
                    </div>
                    @endif
                </td>
                <td class="main">
                    <h1 class="name">{{ $info['name'] ?? 'Your Name' }}</h1>
                    <div class="title">{{ $info['title'] ?? 'Professional Title' }}</div>
                    
                    @if(!empty($info['summary']))
                    <div class="block-desc" style="margin-bottom: 40px;">
                        {!! nl2br(e($info['summary'])) !!}
                    </div>
                    @endif

                    @if($experience && !empty($experience->content))
                    <div class="section-title">Experience</div>
                    @foreach($experience->content as $job)
                    <div class="block">
                        <div class="block-header">
                            <div class="block-title">{{ $job['title'] ?? 'Job Title' }}</div>
                            <div class="block-date">{{ $job['start_date'] ?? '' }} – {{ $job['end_date'] ?? 'Present' }}</div>
                        </div>
                        <div class="block-subtitle">{{ $job['company'] ?? 'Company' }}</div>
                        <div class="block-desc">{!! nl2br(e($job['description'] ?? '')) !!}</div>
                    </div>
                    @endforeach
                    @endif

                    @if($education && !empty($education->content))
                    <div class="section-title" style="margin-top: 40px;">Education</div>
                    @foreach($education->content as $edu)
                    <div class="block">
                        <div class="block-header">
                            <div class="block-title">{{ $edu['degree'] ?? 'Degree' }}</div>
                            <div class="block-date">{{ $edu['start_date'] ?? '' }} – {{ $edu['end_date'] ?? 'Present' }}</div>
                        </div>
                        <div class="block-subtitle">{{ $edu['school'] ?? 'School' }}</div>
                        @if(!empty($edu['description']))
                        <div class="block-desc">{!! nl2br(e($edu['description'])) !!}</div>
                        @endif
                    </div>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
