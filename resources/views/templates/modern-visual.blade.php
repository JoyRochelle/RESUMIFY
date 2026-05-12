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
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lato:wght@400;700&display=swap');
        
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Lato' }}', sans-serif; background: #e9eaec; }
        .resume-page { 
            @if(!isset($isPdf) || !$isPdf) width: 210mm; min-height: 297mm; margin: 0 auto; box-shadow: 0 20px 40px rgba(0,0,0,0.08); @endif 
            box-sizing: border-box; 
            background: linear-gradient(to right, {{ $style['primary_color'] ?? '#10b981' }} 35%, {{ $style['background_color'] ?? '#ffffff' }} 35%);
        }
        .layout { width: 100%; height: 100%; min-height: 297mm; border-collapse: collapse; }
        .col-left { width: 35%; color: #ffffff; padding: 40px 25px; vertical-align: top; }
        .col-right { width: 65%; padding: 40px 35px; vertical-align: top; color: #333333; background: {{ $style['background_color'] ?? '#ffffff' }}; }
        .photo { width: 150px; height: 150px; border-radius: 50%; border: 4px solid rgba(255,255,255,0.3); margin: 0 auto 30px auto; display: block; object-fit: cover; }
        .name { font-family: '{{ $style['font_heading'] ?? 'Montserrat' }}', sans-serif; font-size: 32px; font-weight: 700; color: #ffffff; text-align: center; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px; line-height: 1.1; }
        .title { text-align: center; font-size: 16px; font-weight: 600; background: rgba(255,255,255,0.2); padding: 8px 10px; border-radius: 4px; margin-bottom: 30px; }
        .section-left-title { font-family: '{{ $style['font_heading'] ?? 'Montserrat' }}', sans-serif; font-size: 18px; text-transform: uppercase; border-bottom: 2px solid rgba(255,255,255,0.3); padding-bottom: 5px; margin-bottom: 15px; margin-top: 30px; }
        .contact-row { font-size: 14px; margin-bottom: 10px; }
        .skill-bar-container { background: rgba(255,255,255,0.2); height: 6px; border-radius: 3px; margin-top: 4px; margin-bottom: 12px;}
        .skill-bar { background: #ffffff; height: 6px; border-radius: 3px; }
        .section-right-title { font-family: '{{ $style['font_heading'] ?? 'Montserrat' }}', sans-serif; font-size: 22px; text-transform: uppercase; color: {{ $style['primary_color'] ?? '#10b981' }}; border-bottom: 2px solid {{ $style['secondary_color'] ?? '#e2e8f0' }}; padding-bottom: 8px; margin-bottom: 20px; margin-top: 0; }
        .timeline-item { margin-bottom: 25px; }
        .timeline-title { font-weight: 700; font-size: 18px; color: #1f2937; }
        .timeline-subtitle { font-size: 15px; color: #4b5563; font-weight: 600; margin-top: 2px; }
        .timeline-date { font-size: 13px; color: {{ $style['primary_color'] ?? '#10b981' }}; font-weight: 700; margin-bottom: 8px; }
        .timeline-desc { font-size: 14px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="resume-page">
        <table class="layout">
            <tr>
                <td class="col-left">
                    @if(!empty($style['show_photo']) && !empty($info['photo']))
                    <img src="{{ $info['photo'] }}" alt="Profile" class="photo">
                    @endif
                    
                    <h1 class="name">{{ $info['name'] ?? 'Your Name' }}</h1>
                    <div class="title">{{ $info['title'] ?? 'Professional Title' }}</div>
                    
                    <h2 class="section-left-title">Contact</h2>
                    <div class="contact-row">{{ $info['email'] ?? 'email@example.com' }}</div>
                    @if(!empty($info['phone']))<div class="contact-row">{{ !empty($info['country_code']) ? $info['country_code'].' ' : '' }}{{ $info['phone'] }}</div>@endif
                    @if(!empty($info['location']))<div class="contact-row">{{ $info['location'] }}</div>@endif
                    
                    @if($skills && !empty($skills->content))
                    <h2 class="section-left-title">Skills</h2>
                    @foreach($skills->content as $skill)
                        <div style="font-size: 14px; font-weight: 600;">{{ $skill['name'] ?? '' }}</div>
                        <div class="skill-bar-container">
                            <div class="skill-bar" style="width: 80%;"></div>
                        </div>
                    @endforeach
                    @endif
                </td>
                <td class="col-right">
                    @if(!empty($info['summary']))
                    <h2 class="section-right-title">Profile</h2>
                    <div class="timeline-desc" style="margin-bottom: 30px;">
                        {!! nl2br(e($info['summary'])) !!}
                    </div>
                    @endif
                    
                    @if($experience && !empty($experience->content))
                    <h2 class="section-right-title">Experience</h2>
                    @foreach($experience->content as $job)
                    <div class="timeline-item">
                        <div class="timeline-title">{{ $job['title'] ?? 'Title' }}</div>
                        <div class="timeline-subtitle">{{ $job['company'] ?? 'Company' }}</div>
                        <div class="timeline-date">{{ $job['start_date'] ?? '' }} – {{ $job['end_date'] ?? 'Present' }}</div>
                        <div class="timeline-desc">{!! nl2br(e($job['description'] ?? '')) !!}</div>
                    </div>
                    @endforeach
                    @endif

                    @if($education && !empty($education->content))
                    <h2 class="section-right-title" style="margin-top: 30px;">Education</h2>
                    @foreach($education->content as $edu)
                    <div class="timeline-item">
                        <div class="timeline-title">{{ $edu['degree'] ?? 'Degree' }}</div>
                        <div class="timeline-subtitle">{{ $edu['school'] ?? 'School' }}</div>
                        <div class="timeline-date">{{ $edu['start_date'] ?? '' }} – {{ $edu['end_date'] ?? 'Present' }}</div>
                        @if(!empty($edu['description']))
                        <div class="timeline-desc">{!! nl2br(e($edu['description'])) !!}</div>
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
