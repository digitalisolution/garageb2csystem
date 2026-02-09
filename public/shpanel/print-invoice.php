<!DOCTYPE html>
<html>
<head>
<title>Invoice - <?php echo $invoice['garage_name']; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap;" rel="stylesheet">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style type="text/css">
  /* ===== A4 PRINT SETTINGS ===== */
@page {size: A4;margin: 15mm;}
@media print {
  body {background: #fff !important;}
    .fullTable {width: 100% !important;}
    table {page-break-inside: avoid;}
    .hide-on-print {display: none !important;}
}
</style>
<?php
include 'http-auth.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conDb = mysqli_connect('178.128.171.57', 'vehicle_details_db', 'VPM1PpJkTGS7HP', 'vehicle_details_db');
$invoice_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$query = mysqli_query($conDb, "SELECT gi.*, g.garage_url, g.garage_address, g.garage_email, g.garage_phone 
    FROM garage_invoices_price gi JOIN garagewebsitelist g ON gi.garage_id = g.id WHERE gi.id = $invoice_id");
$invoice = mysqli_fetch_assoc($query);
if (!$invoice) {echo "Invoice not found.";exit;}?>
<style>
  .invoice-box {max-width: 800px; margin: auto;}
  .invoice-box h1 { text-align: center; text-transform: uppercase; margin-bottom: 30px; }
  .info-box { margin-bottom: 20px; margin-top:20px;}
  .info-box p { margin: 4px 0; }
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .print-button { text-align: center; margin-top: 30px; }
  .print-button button { padding: 10px 20px; font-size: 16px; }
  @media print {.print-button { display: none; }}
  body {
  font-family: "Roboto", sans-serif;
  background-color:#eee;
  }
  .invoice-body{background:#fff;box-shadow:0 0 20px rgba(0,0,0,0.15);padding:30px;border-radius:10px;}
  .my-5{margin:50px 0;}
  .bg-dark{background:#333;color:#fff;}
  .mt-3{margin-top:30px;}
  .service-row{display:flex;gap:30px;margin-bottom:10px;background:#eee;padding:6px;border-radius:4px;border:solid 1px rgba(0,0,0,0.2);align-items:center;}
</style>
</head>
<body>
<style type="text/css">
  body { margin: 0; padding: 0; background: #e1e1e1; }
  div, p, a, li, td { -webkit-text-size-adjust: none; }
  .ReadMsgBody { width: 100%; background-color: #ffffff; }
  .ExternalClass { width: 100%; background-color: #ffffff; }
  body { width: 100%; height: 100%; background-color: #e1e1e1; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
  html { width: 100%; }
  p { padding: 0 !important; margin-top: 0 !important; margin-right: 0 !important; margin-bottom: 0 !important; margin-left: 0 !important; }
  .visibleMobile { display: none; }
  .hiddenMobile { display: block; }

  @media only screen and (max-width: 600px) {
  body { width: auto !important; }
  table[class=fullTable] { width: 96% !important; clear: both; }
  table[class=fullPadding] { width: 85% !important; clear: both; }
  table[class=col] { width: 45% !important; }
  .erase { display: none; }
  }

  @media only screen and (max-width: 420px) {
  table[class=fullTable] { width: 100% !important; clear: both; }
  table[class=fullPadding] { width: 85% !important; clear: both; }
  table[class=col] { width: 100% !important; clear: both; }
  table[class=col] td { text-align: left !important; }
  .erase { display: none; font-size: 0; max-height: 0; line-height: 0; padding: 0; }
  .visibleMobile { display: block !important; }
  .hiddenMobile { display: none !important; }
  }
  @media print {
    .hide-on-print {
        display: none !important;
    }
  }
</style>

<!-- Header -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tr>
    <td height="20"></td>
  </tr>
  <tr>
    <td>
      <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="border-radius: 10px 10px 0 0;background:#fff;">
        <tr class="hiddenMobile">
          <td height="40"></td>
        </tr>
        <tr class="visibleMobile">
          <td height="30"></td>
        </tr>
        <tr>
          <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
              <tbody>
                <tr>
                  <td>
                    <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                      <tbody>
                        <tr>
                          <td align="left"> <img src="algorise-logo.jpg" alt="Logo" height="50"></td>
                        </tr>
                        <tr class="hiddenMobile">
                          <td height="30"></td>
                        </tr>
                        <tr class="visibleMobile">
                          <td height="20"></td>
                        </tr>
                        <tr>
                          <td style="font-size: 12px; color: #5b5b5b; font-family: 'arial', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">
                            <strong>To,</strong><br>
                            <?php echo $invoice['garage_name']; ?><br>
                            <?php echo $invoice['garage_url']; ?><br>
                            <?php echo nl2br($invoice['garage_address']); ?><br>
                            <strong>Phone:</strong> <?php echo $invoice['garage_phone']; ?><br>
                            <strong>Email:</strong> <?php echo $invoice['garage_email']; ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                      <tbody>
                        <tr class="visibleMobile">
                          <td height="20"></td>
                        </tr>
                        <tr>
                          <td height="5"></td>
                        </tr>
                        <tr>
                          <td style="font-size: 21px; color: #ff0000; letter-spacing: -1px; font-family: 'arial', sans-serif; line-height: 1; vertical-align: top; text-align: right;">
                            Invoice
                          </td>
                        </tr>
                        <tr>
                        <tr class="hiddenMobile">
                          <td height="50"></td>
                        </tr>
                        <tr class="visibleMobile">
                          <td height="20"></td>
                        </tr>
                        <tr>
                          <td style="font-size: 12px; color: #5b5b5b; font-family: 'arial', sans-serif; line-height: 18px; vertical-align: top; text-align: right;">
                            <strong>Date:</strong> <?php echo date("d/m/Y", strtotime($invoice['invoice_date'])); ?><br>
                            <strong>Due Date:</strong> <?php echo date("d/m/Y", strtotime($invoice['due_date'])); ?><br>
                            <strong>Ref. No.:</strong> DIS/<?php echo date('Y', strtotime($invoice['invoice_date'])); ?>-<?php echo $invoice['id']; ?><br>
                            <strong>Invoice Month:</strong> <?php echo $invoice['month']; ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!-- /Header -->
<!-- Order Details -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tbody>
    <tr>
      <td>
        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="background:#fff;">
          <tbody>
            <tr>
            <tr class="hiddenMobile">
              <td height="40"></td>
            </tr>
            <tr class="visibleMobile">
              <td height="30"></td>
            </tr>
            <tr>
              <td>
                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    <tr>
                      <th style="font-size: 12px; font-family: 'arial', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 10px 7px 0;" align="left">
                        <strong>Serial</strong>
                      </th>
                      <th style="font-size: 12px; font-family: 'arial', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="left">
                        <strong>Service</strong>
                      </th>
                      <th style="font-size: 12px; font-family: 'arial', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="left">
                        <strong>Billing Cycle</strong>
                      </th>
                      <th style="font-size: 12px; font-family: 'arial', sans-serif; color: #1e2b33; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px; text-align:right;" align="right">
                        <strong>Amount (GBP)</strong>
                      </th>
                    </tr>
                    <tr>
                      <td height="1" style="background: #bebebe;" colspan="4"></td>
                    </tr>
                    <tr>
                      <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:6px 0;" class="article">
                        1
                      </td>
                      <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:6px 0;">SEO Services</td>
                      <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:6px 0;" align="left">Monthly</td>
                      <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #1e2b33;  line-height: 18px;  vertical-align: top; padding:6px 0;" align="right"><?php echo number_format($invoice['seo_price'], 2); ?></td>
                    </tr>
                    <tr>
                      <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                    </tr>
                    <?php
                    $additional_services = json_decode($invoice['additional_services'], true);

                    $serial = 2; // since SEO = 1 
                    if (!empty($additional_services)) {
                        foreach ($additional_services as $service) {
                            echo "<tr>
                                    <td  style='font-size: 12px; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:6px 0;'>{$serial}</td>
                                    <td  style='font-size: 12px; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:6px 0;'>{$service['name']}</td>
                                    <td  style='font-size: 12px; color: #646a6e;  line-height: 18px;  vertical-align: top; padding:6px 0;'>Monthly</td>
                                    <td  style='font-size: 12px; color: #1e2b33;  line-height: 18px;  vertical-align: top; padding:6px 0;' class='text-right'>" . number_format($service['price'], 2) . "</td>
                                  </tr>";
                            $serial++;
                        }
                    }
                    ?>
                    <tr>
                      <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td height="10"></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<!-- /Order Details -->
<!-- Total -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tbody>
    <tr>
      <td>
        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="background:#fff;">
          <tbody>
            <tr>
              <td>

                <!-- Table Total -->
                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    
                    <tr>
                      <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                        <strong>Total</strong>
                      </td>
                      <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right;" width="80">
                        <strong><?php echo number_format($invoice['total_price'], 2); ?></strong>
                      </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right;" colspan="2"><strong>In Words GBP: <?php echo ucwords(convert_number_to_words($invoice['total_price'])); ?> only.</strong></td>
                    </tr>
                  </tbody>
                </table>
                <!-- /Table Total -->

              </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<!-- /Total -->
<!-- Information -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
  <tbody>
    <tr>
      <td>
        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="background:#fff;">
          <tbody>
            <tr>
            <tr class="hiddenMobile">
              <td height="40"></td>
            </tr>
            <tr class="visibleMobile">
              <td height="30"></td>
            </tr>
            <tr>
              <td>
                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    <tr>
                      <td>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                          <tbody>
                            <tr>
                              <td style="font-size: 13px; font-family: 'arial', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                <strong>ACCOUNT INFORMATION</strong>
                              </td>
                            </tr>
                            <tr>
                              <td width="100%" height="10"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                <strong>Beneficiary Bank:</strong> Federal Bank Ltd.<br>
                                <strong>Branch Address:</strong>Ground Floor | C-2 | Sector-15 | Vasundhra | Ghaziabad | U.P - 201012<br>
                                <strong>Account Holder:</strong> Algorise IT Private Limited<br>
                                <strong>Beneficiary Account Number:</strong> 21510200004458<br>
                                <strong>Bank's IFSC Code:</strong> FDRL0002151<br>
                                <strong>Regtd Office:</strong> L2/8,Shiksha Apartment, Sector-6, Vasundhara, Ghaziabad, UP-201012<br>
                                <strong>E-mail:</strong> info@algoriseit.com<br>
                                <strong>PAN:</strong> #ABECA2935N<br>
                                <strong>GSTIN:</strong> 09ABECA2935N1ZB
                              </td>
                            </tr>
                          </tbody>
                        </table> 

                           
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                  <tbody>
                    <tr>
                      <td>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="col">
                          <tbody style="text-align:center;">
                            <tr class="hiddenMobile">
                              <td height="35"></td>
                            </tr>
                            <tr class="visibleMobile">
                              <td height="20"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 11px; font-family: 'arial', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                <strong>Algorise IT Private Limited</strong>
                              </td>
                            </tr>
                            <tr>
                              <td width="100%" height="10"></td>
                            </tr>
                            <tr>
                              <td style="font-size: 12px; font-family: 'arial', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">L2/8,Shiksha Apartment, Sector-6, Vasundhara, Ghaziabad (UP) India-201012 <br>M: +91-9818688623 &nbsp; E: info@algoriseit.com
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr class="hiddenMobile">
              <td height="20"></td>
            </tr>
            <tr class="visibleMobile">
              <td height="10"></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<!-- /Information -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">

  <tr>
    <td>
      <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="border-radius: 0 0 10px 10px;background:#fff;">
        <tr>
          <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
              <tbody>
                <tr>
                  <td style="font-size: 12px; color: #5b5b5b; font-family: 'arial', sans-serif; line-height: 18px; vertical-align: top; text-align: center;">
                        <button class="btn btn-default btn-sm hide-on-print" onclick="window.print()">🖨️ Print Invoice</button>

                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr class="spacer">
          <td height="50"></td>
        </tr>

      </table>
    </td>
  </tr>
  <tr>
    <td height="20"></td>
  </tr>
</table>

</body>
</html>

<?php
function convert_number_to_words($number) {
    return (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($number);
}
?>
