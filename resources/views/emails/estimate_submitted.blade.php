<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .emailbg{background:#eee;padding:60px 0;color:#333;}
        .emailbg h4, .emailbg p, .emailbg h2{color:#333;}
        .email-container {max-width:600px; margin: auto; border: 1px solid #bbb; border-radius:10px; background:#fff;padding-bottom:30px;box-shadow:0 0 15px rgba(0,0,0,0.2);overflow:hidden;}
        .email-container h2{color: #fff;text-align: center;background: #480622;padding: 10px;margin: 0;font-size: 30px;}
        .footer { margin-top: 20px; font-size: 0.9em; color: #999; text-align: center; }
        .total { font-weight: bold;background:#bd155d;}
    </style>
</head>
<body>
    <div class="emailbg">
    <div class="email-container">
        <h2>Enquiry Confirmation #{{ $estimate->id }}</h2>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td style="width:50%;"><table width="90%" border="0" align="left" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:30px;">
                    <p>Hello {{ $customer['customer_name'] ?? 'Customer' }},</p>
                    <p>Thank you for submitting your enquiry. We have received your request and will contact you shortly.</p>
                    <h4><strong>Estimate ID:</strong> {{ $estimate->id }}</h4>
                    <h4><strong>Vehicle Reg:</strong> <span style="background:yellow;color:black;border:solid 1px #000;border-radius:5px;padding:5px 10px;">{{ $estimate->vehicle_reg_number ?? 'N/A' }}</span></h4>
                </td>
              </tr>
            </table></td>
            <td style="width:50%;"><table width="90%" border="0" align="left" cellpadding="0" cellspacing="0">
              <tr>
                <td valign="top" style="background:#e22174;border-radius:10px;padding:15px;height:145px;verticle-align:top;color:#fff;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="8">
                        <thead style="background:#111;">
                          <tr>
                            <td><strong>Service</strong></td>
                            <td align="right"><strong>Price</strong></td>
                          </tr>
                      </thead>
                      <tbody>
                        @foreach ($workshopProducts as $service)
                            <tr>
                                <td style="text-transform:capitalize;">{{ $service->service_name }}</td>
                                <td align="right">£{{ number_format($service->service_price, 2) }}</td>
                            </tr>
                        @endforeach
                        <tfoot>
                            <tr class="total">
                                <td><strong>Total</strong></td>
                                <td align="right"><strong>£{{ number_format($estimate->grandTotal ?? 0, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </tbody>
                    </table>
                </td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2" style="margin-top:30px;background:#eee;padding:10px 30px;"><p>If you have any questions, feel free to contact us at any time.</p>
        <p><strong>Best regards,</strong><br>{{ $garage->garage_name ?? config('mail.from.name') }}</p></td>
          </tr>
          <tr>
            <td colspan="2">
                <div class="footer">
            &copy; {{ now()->year }} {{ $garage->garage_name ?? config('mail.from.name') }}<br>
            {{ $garage->address ?? '123 Garage Street, Birmingham' }}<br>
            {{ $garage->email ?? config('mail.from.address') }}
        </div></td>
          </tr>
        </table>
    </div>
    </div>
</body>
</html>