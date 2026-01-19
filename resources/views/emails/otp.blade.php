<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login Verification Code</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px 0;">
        <tr>
            <td align="center">

                <table width="100%" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; max-width:500px; border-radius:6px; padding:25px;">

                    <tr>
                        <td style="color:#2d3748; font-size:15px; line-height:22px;">
                            <p style="margin:0 0 12px 0;">
                                Hello, <strong>{{ ucwords(@$name) }}</strong>,
                            </p>

                            <p style="margin:0 0 12px 0;">
                                Your login verification code is:
                            </p>

                            <p style="font-size:24px; font-weight:bold; letter-spacing:3px; margin:10px 0 18px 0;">
                                {{ $otp }}
                            </p>

                            <p style="margin:0 0 12px 0;">
                                Use this code to sign in to the
                                <strong>Easylogics Technology</strong> app.
                            </p>

                            <p style="margin:0 0 12px 0;">
                                This code will expire in <strong>5 minutes</strong> and can be used only once.
                            </p>

                            <p style="margin:0 0 20px 0;">
                                If you did not try to log in, please ignore this email.
                            </p>

                            <p style="margin:0;">
                                Best regards,<br>
                                <strong>Easylogics Technology</strong><br>
                                <a href="mailto:support@easylogicstechnology.com"
                                   style="color:#2b6cb0; text-decoration:none;">
                                    support@easylogicstechnology.com
                                </a>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
