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
        @import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Open+Sans:wght@400;600&display=swap');
        
        body { margin: 0; padding: 0; font-family: '{{ $style['font_body'] ?? 'Open Sans' }}', sans-serif; background: {{ $style['background_color'] ?? '#faf9f6' }}; color: #2c2c2c; }
        .resume-page { width: 210mm; min-height: 297mm; padding: 25mm 20mm; box-sizing: border-box; margin: 0 auto; }
        header { text-align: center; margin-bottom: 25px; }
        h1 { font-family: '{{ $style['font_heading'] ?? 'Lora' }}', serif; font-size: 26px; margin: 0; font-weight: 600; color: {{ $style['primary_color'] ?? '#111111' }}; }
        .contact { font-size: 13px; margin-top: 8px; color: #555555; }
        h2 { font-family: '{{ $style['font_heading'] ?? 'Lora' }}', serif; font-size: 16px; text-transform: uppercase; letter-spacing: 1.5px; color: {{ $style['secondary_color'] ?? '#333333' }}; border-bottom: 1px solid #d1d1d1; padding-bottom: 4px; margin-top: 25px; margin-bottom: 15px; }
        .item-container { margin-bottom: 15px; }
        .item-header { display: table; width: 100%; margin-bottom: 4px; }
        .item-title-col { display: table-cell; text-align: left; }
        .item-date-col { display: table-cell; text-align: right; font-size: 12.5px; color: #555555; white-space: nowrap; width: 150px; }
        .bold { font-weight: 600; }
        .italic { font-style: italic; }
        .desc { font-size: 13px; line-height: 1.6; }
        ul.skills-list { column-count: 2; margin: 0; padding: 0 0 0 15px; font-size: 13px; }
        ul.skills-list li { margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="resume-page">
        <header>
            <h1>{{ $info['name'] ?? 'Your Name' }}</h1>
            <div class="contact">
                {{ $info['email'] ?? 'email@example.com' }} 
                @if(!empty($info['phone'])) | {{ $info['phone'] }} @endif
                @if(!empty($info['location'])) | {{ $info['location'] }} @endif
            </div>
        </header>

        @if(!empty($info['summary']))
        <div class="desc" style="margin-bottom: 20px; text-align: justify;">
            {!! nl2br(e($info['summary'])) !!}
        </div>
        @endif

        @if($education && !empty($education->content))
        <section>
            <h2>Education</h2>
            @foreach($education->content as $edu)
            <div class="item-container">
                <div class="item-header">
                    <div class="item-title-col">
                        <span class="bold">{{ $edu['school'] ?? 'School Name' }}</span>, {{ $edu['degree'] ?? 'Degree' }}
                    </div>
                    <div class="item-date-col">{{ $edu['start_date'] ?? '' }} – {{ $edu['end_date'] ?? 'Present' }}</div>
                </div>
                @if(!empty($edu['description']))
                <div class="desc">{!! nl2br(e($edu['description'])) !!}</div>
                @endif
            </div>
            @endforeach
        </section>
        @endif

        @if($experience && !empty($experience->content))
        <section>
            <h2>Professional Experience</h2>
            @foreach($experience->content as $job)
            <div class="item-container">
                <div class="item-header">
                    <div class="item-title-col">
                        <span class="bold">{{ $job['title'] ?? 'Title' }}</span> <span class="italic">at {{ $job['company'] ?? 'Company' }}</span>
                    </div>
                    <div class="item-date-col">{{ $job['start_date'] ?? '' }} – {{ $job['end_date'] ?? 'Present' }}</div>
                </div>
                <div class="desc">{!! nl2br(e($job['description'] ?? '')) !!}</div>
            </div>
            @endforeach
        </section>
        @endif

        @if($skills && !empty($skills->content))
        <section>
            <h2>Skills</h2>
            <ul class="skills-list">
            @foreach($skills->content as $skill)
                <li><span class="bold">{{ $skill['name'] ?? '' }}</span>: {{ $skill['level'] ?? '' }}</li>
            @endforeach
            </ul>
        </section>
        @endif
    </div>
</body>
</html>
