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
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;600;700&family=Roboto:wght@300;400;500;700&display=swap');
        
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Roboto' }}', sans-serif; background: #e9eaec; color: #334155; }
        
        .resume-page { 
            @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; box-shadow: 0 20px 40px rgba(0,0,0,0.08); @endif 
            padding: 0; box-sizing: border-box; 
            background: {{ $style['secondary_color'] ?? '#f8fafc' }}; 
        }
        
        .layout-table { width: 100%; height: 100%; min-height: 297mm; border-collapse: collapse; }
        
        .sidebar { width: 33%; vertical-align: top; padding: 45px 30px; border-right: 1px solid rgba(0,0,0,0.03); }
        
        .main-content { width: 67%; vertical-align: top; padding: 55px 45px; background: {{ $style['background_color'] ?? '#ffffff' }}; }
        
        .photo-container { text-align: center; margin-bottom: 30px; }
        .photo { width: 150px; height: 150px; object-fit: cover; border-radius: 12px; border: 3px solid {{ $style['primary_color'] ?? '#0ea5e9' }}; box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        
        h1 { font-family: '{{ $style['font_heading'] ?? 'Roboto Mono' }}', monospace; font-size: 36px; line-height: 1.1; margin: 0 0 8px 0; color: #0f172a; font-weight: 700; letter-spacing: -0.5px; }
        .role { font-size: 18px; font-weight: 500; color: {{ $style['primary_color'] ?? '#0ea5e9' }}; margin-bottom: 30px; letter-spacing: 0.5px; text-transform: uppercase; }
        
        h2 { font-family: '{{ $style['font_heading'] ?? 'Roboto Mono' }}', monospace; font-size: 20px; color: #0f172a; border-bottom: 2px solid {{ $style['primary_color'] ?? '#0ea5e9' }}; padding-bottom: 8px; margin-top: 0; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 1px; }
        
        .contact-item { font-size: 13.5px; margin-bottom: 15px; word-break: break-word; display: block; line-height: 1.5; color: #475569; }
        .contact-label { font-weight: 700; font-size: 11.5px; color: {{ $style['primary_color'] ?? '#0ea5e9' }}; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        
        .skill-list { list-style: none; padding: 0; margin: 0; font-size: 13.5px; color: #475569; }
        .skill-list li { margin-bottom: 10px; padding-left: 16px; position: relative; line-height: 1.4; }
        .skill-list li::before { content: "▹"; position: absolute; left: 0; color: {{ $style['primary_color'] ?? '#0ea5e9' }}; font-weight: bold; }
        
        .entry { margin-bottom: 30px; }
        .entry:last-child { margin-bottom: 0; }
        .entry-header { margin-bottom: 6px; display: flex; align-items: baseline; }
        .entry-title { font-weight: 700; font-size: 17px; color: #0f172a; }
        .entry-company { font-weight: 500; font-size: 15.5px; color: {{ $style['primary_color'] ?? '#0ea5e9' }}; }
        .entry-date { font-family: 'Roboto Mono', monospace; font-size: 12.5px; color: #64748b; margin-top: 4px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .entry-desc { font-size: 14px; line-height: 1.65; color: #475569; }
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
                        {{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}
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
                    <header style="margin-bottom: 40px;">
                        <h1>{{ $info['name'] ?? 'Your Name' }}</h1>
                        <div class="role">{{ $info['title'] ?? 'Professional Title' }}</div>
                    </header>
                    
                    @if(!empty($info['summary']))
                    <div class="entry-desc" style="margin-bottom: 40px;">
                        {!! nl2br(e($info['summary'])) !!}
                    </div>
                    @endif

                    @if($experience && !empty($experience->content))
                    <h2>Experience</h2>
                    @foreach($experience->content as $job)
                    <div class="entry">
                        <div class="entry-header">
                            <span class="entry-title">{{ $job['title'] ?? 'Job Title' }}</span> <span style="margin: 0 6px; color:#cbd5e1;">|</span> 
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
                            <span class="entry-title">{{ $edu['degree'] ?? 'Degree' }}</span> <span style="margin: 0 6px; color:#cbd5e1;">|</span>
                            <span class="entry-company">{{ $edu['school'] ?? 'School' }}</span>
                        </div>
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
