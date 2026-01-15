<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px 0;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="100%" max-width="500" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); padding:30px; max-width:500px;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding-bottom:20px;">
                            <h2 style="margin:0; color:#2d3748;">OTP Verification</h2>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="color:#4a5568; font-size:15px; line-height:22px;">
                            <p style="margin:0 0 10px 0;">Hello <strong>{{ ucwords(@$name) }}</strong>,</p>
                            <p style="margin:0;">Your One-Time Password (OTP) is:</p>
                        </td>
                    </tr>

                    <!-- OTP Box -->
                    <tr>
                        <td align="center" style="padding:20px 0;">
                            <div style="display:inline-block; padding:15px 30px; background:#edf2f7; border:1px dashed #2d3748; border-radius:6px;">
                                <span style="font-size:28px; letter-spacing:4px; font-weight:bold; color:#2d3748;">
                                    {{ $otp }}
                                </span>
                            </div>
                        </td>
                    </tr>

                    <!-- Info -->
                    <tr>
                        <td style="color:#4a5568; font-size:14px; line-height:22px;">
                            <p style="margin:0 0 10px 0;">
                                This OTP is valid for <strong>5 minutes</strong>. Please do not share it with anyone.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="border-top:1px solid #e2e8f0; padding-top:15px; color:#718096; font-size:13px;">
                            <p style="margin:0;">
                                Thank you,<br>
                                <strong>{{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>

</body>
</html>
