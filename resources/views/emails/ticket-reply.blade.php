<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Support Reply — Resumify</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5ede6; margin: 0; padding: 32px 16px; color: #3d2e25; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; }
        .header { background: #4f3b2f; padding: 28px 32px; }
        .header-title { color: #ffffff; font-size: 20px; font-weight: bold; margin: 0; }
        .header-sub { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 4px; }
        .body { padding: 32px; }
        .label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #8b7355; margin-bottom: 6px; }
        .subject { font-size: 16px; font-weight: bold; color: #3d2e25; margin-bottom: 20px; }
        .reply-box { background: #f5ede6; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .reply-text { font-size: 14px; line-height: 1.7; color: #3d2e25; white-space: pre-wrap; }
        .sender { font-size: 12px; color: #8b7355; margin-top: 12px; }
        .cta { text-align: center; margin: 24px 0; }
        .cta a { background: #4f3b2f; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 13px; font-weight: bold; }
        .footer { padding: 20px 32px; border-top: 1px solid #f0ebe6; font-size: 11px; color: #a89880; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <p class="header-title">Resumify Support</p>
            <p class="header-sub">Ticket #{{ substr($ticket->id, -8) }}</p>
        </div>
        <div class="body">
            <p>Hi {{ $ticket->user?->name ?? 'there' }},</p>
            <p style="margin: 12px 0 20px; font-size: 14px; color: #6b5740;">
                Our support team has replied to your ticket.
            </p>

            <div class="label">Your Ticket</div>
            <div class="subject">{{ $ticket->subject }}</div>

            <div class="label">Reply</div>
            <div class="reply-box">
                <div class="reply-text">{{ $reply->body }}</div>
                <div class="sender">— {{ $reply->sender?->name ?? 'Resumify Support' }}, {{ $reply->created_at?->format('d M Y, H:i') }}</div>
            </div>

            <div class="cta">
                <a href="{{ config('app.url') }}/help/tickets/{{ $ticket->id }}">View Full Thread</a>
            </div>

            <p style="font-size: 13px; color: #8b7355;">
                You can reply by visiting your ticket thread. If you have more questions, don't hesitate to reach out.
            </p>
        </div>
        <div class="footer">
            Resumify &mdash; AI-Powered Resume Builder<br>
            This email was sent because you have an open support ticket.
        </div>
    </div>
</body>
</html>
