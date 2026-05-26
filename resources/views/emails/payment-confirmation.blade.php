<!DOCTYPE html>
<html>
<head>
    <title>Payment Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #0F6E56;">Payment Successful!</h2>
        <p>Hi {{ $user->name }},</p>
        <p>Thank you for your purchase. Your payment was successfully processed, and your account has been upgraded to Premium.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Invoice Number:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">{{ $transaction->midtrans_order_id }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Plan:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">Premium (50 AI Credits)</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Amount Paid:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Date:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">{{ $transaction->paid_at->format('d M Y, H:i') }}</td>
            </tr>
        </table>
        
        <p style="margin-top: 30px;">You can now enjoy all the premium features, including AI-powered resume optimization and parallel CV generation.</p>
        
        <p>Best regards,<br>The {{ config('app.name') }} Team</p>
    </div>
</body>
</html>
