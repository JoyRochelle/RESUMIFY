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
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&family=Roboto:wght@400;500;700&display=swap');
        
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Roboto' }}', sans-serif; background: #ffffff; color: #333333; }
        .resume-page { @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; @endif padding: 0; box-sizing: border-box; background: {{ $style['background_color'] ?? '#ffffff' }}; }
        .layout-table { width: 100%; height: 100%; border-collapse: collapse; }
        .sidebar { width: 30%; background: {{ $style['secondary_color'] ?? '#f1f5f9' }}; vertical-align: top; padding: 30px 20px; }
        .main-content { width: 70%; vertical-align: top; padding: 40px 30px; }
        .photo-container { text-align: center; margin-bottom: 20px; }
        .photo { width: 140px; height: 140px; object-fit: cover; border-radius: 8px; border: 2px solid {{ $style['primary_color'] ?? '#0ea5e9' }}; }
        h1 { font-family: '{{ $style['font_heading'] ?? 'Roboto Mono' }}', monospace; font-size: 28px; margin: 0 0 5px 0; color: #0f172a; }
        .role { font-size: 16px; font-weight: 700; color: {{ $style['primary_color'] ?? '#0ea5e9' }}; margin-bottom: 20px; }
        h2 { font-family: '{{ $style['font_heading'] ?? 'Roboto Mono' }}', monospace; font-size: 18px; color: #0f172a; border-bottom: 1px solid {{ $style['primary_color'] ?? '#0ea5e9' }}; padding-bottom: 5px; margin-top: 0; margin-bottom: 15px; }
        .contact-item { font-size: 13px; margin-bottom: 10px; word-break: break-all; }
        .contact-label { font-weight: 700; font-size: 11px; color: #64748b; text-transform: uppercase; }
        .skill-list { list-style: none; padding: 0; margin: 0; font-size: 13px; }
        .skill-list li { margin-bottom: 6px; padding-left: 12px; position: relative; }
        .skill-list li::before { content: "▹"; position: absolute; left: 0; color: {{ $style['primary_color'] ?? '#0ea5e9' }}; }
        .entry { margin-bottom: 25px; }
        .entry-header { margin-bottom: 5px; }
        .entry-title { font-weight: 700; font-size: 16px; color: #0f172a; }
        .entry-company { font-weight: 500; font-size: 15px; color: {{ $style['primary_color'] ?? '#0ea5e9' }}; }
        .entry-date { font-family: 'Roboto Mono', monospace; font-size: 12px; color: #64748b; margin-top: 3px; }
        .entry-desc { font-size: 13px; line-height: 1.5; color: #334155; }
    </style>
</head>
<body>
    <div class="resume-page">
        <table class="layout-table">
            <tr>
                <td class="sidebar">
                    @if(!empty($style['show_photo']) && !empty($info['photo']))
                    <div class="photo-container">
                        <img src="{{ $info['photo'] }}" alt="Profile" class="photo">
                    </div>
                    @endif
                    
                    <div class="contact-item">
                        <div class="contact-label">Email</div>
                        {{ $info['email'] ?? 'email@example.com' }}
                    </div>
                    @if(!empty($info['phone']))
                    <div class="contact-item">
                        <div class="contact-label">Phone</div>
                        {{ $info['phone'] }}
                    </div>
                    @endif
                    @if(!empty($info['location']))
                    <div class="contact-item">
                        <div class="contact-label">Location</div>
                        {{ $info['location'] }}
                    </div>
                    @endif

                    @if($skills && !empty($skills->content))
                    <div style="margin-top: 40px;">
                        <h2>Skills</h2>
                        <ul class="skill-list">
                        @foreach($skills->content as $skill)
                            <li>{{ $skill['name'] ?? '' }} @if(!empty($skill['level'])) ({{ $skill['level'] }}) @endif</li>
                        @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
                <td class="main-content">
                    <h1>{{ $info['name'] ?? 'Your Name' }}</h1>
                    <div class="role">{{ $info['title'] ?? 'Professional Title' }}</div>
                    
                    @if(!empty($info['summary']))
                    <div class="entry-desc" style="margin-bottom: 30px;">
                        {!! nl2br(e($info['summary'])) !!}
                    </div>
                    @endif

                    @if($experience && !empty($experience->content))
                    <h2>Experience</h2>
                    @foreach($experience->content as $job)
                    <div class="entry">
                        <div class="entry-header">
                            <span class="entry-title">{{ $job['title'] ?? 'Job Title' }}</span> | 
                            <span class="entry-company">{{ $job['company'] ?? 'Company' }}</span>
                        </div>
                        <div class="entry-date">{{ $job['start_date'] ?? '' }} – {{ $job['end_date'] ?? 'Present' }}</div>
                        <div class="entry-desc">{!! nl2br(e($job['description'] ?? '')) !!}</div>
                    </div>
                    @endforeach
                    @endif

                    @if($education && !empty($education->content))
                    <h2 style="margin-top: 30px;">Education</h2>
                    @foreach($education->content as $edu)
                    <div class="entry">
                        <div class="entry-header">
                            <span class="entry-title">{{ $edu['degree'] ?? 'Degree' }}</span>
                        </div>
                        <div class="entry-company">{{ $edu['school'] ?? 'School' }}</div>
                        <div class="entry-date">{{ $edu['start_date'] ?? '' }} – {{ $edu['end_date'] ?? 'Present' }}</div>
                        @if(!empty($edu['description']))
                        <div class="entry-desc">{!! nl2br(e($edu['description'])) !!}</div>
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
