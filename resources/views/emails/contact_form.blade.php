<!DOCTYPE html>
<html>

<head>
    <title>Contact Form Inquiry</title>
</head>

<body>
	
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    	<tr>
    		<td style="background:#272727;padding:10px 15px;text-align:center;color:#fff;font-size:25px;font-weight:bold;">Contact Form Inquiry</td>
    	</tr>
    	<tr>
    		<td style="background:#ddd; padding:50px;">
    			<table border="0" cellpadding="5" cellspacing="0" width="100%" style="font-size:13px;font-family:'calibri',arial,verdana;line-height:1.4;background:#fff;padding:30px;border-radius:10px;box-shadow:0 0 15px rgba(0,0,0,0.3);margin:0 auto;max-width:600px;display:inline-block;">
    <tbody>
        <tr style="border-collapse:collapse">
            <td width="25%" style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                <strong>Name:</strong>
            </td>
            <td width="70%" style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                {{ $name }}</td>
        </tr>
        <tr style="border-collapse:collapse">
            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                <strong>Email Address:</strong>
            </td>
            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                <a href="mailto:{{ $email }}" style="color:#0078be;font-weight:bold;text-decoration:none" target="_blank">{{ $email }}</a>
            </td>
        </tr>
        <tr style="border-collapse:collapse">
            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                <strong>Subject:</strong>
            </td>
            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                {{ $subject }}
            </td>
        </tr>
        
        
        <tr style="border-collapse:collapse">
            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                <strong>Message:</strong>
            </td>
            <td style="font-family:HelveticaNeue,sans-serif;border-collapse:collapse">
                {{ $user_message }}</td>
        </tr>
    </tbody>
</table>
    		</td>
    	</tr>
    </table>
</body>

</html>

