<!DOCTYPE html>
<!-- 
Template Name: Brunette - Responsive Bootstrap 4 Admin Dashboard Template
Author: Hencework
Contact: https://hencework.ticksy.com/

License: You must have a valid license purchased only from templatemonster to legally use the template for your project.
-->
<html lang="en">


<!-- Mirrored from hencework.com/theme/brunette/invoice.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 20 Jan 2019 17:38:49 GMT -->
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Work Shop Invoice Invoice</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="{{ asset('/bruneet/dist/css/style.css') }}" rel="stylesheet" type="text/css">
    <style type="text/css">
         @media print {
            color:black;
            font-size: 10px;
         }
    </style>
</head>
<body>
            <!-- Container -->
            <div class="container">
                <!-- Row -->
                <div class="row">
                    <div class="col-xl-12">
                        <section class="hk-sec-wrapper hk-invoice-wrap pa-35">
                            <div class="invoice-from-wrap">
                                <div class="row">
                                    <div class="col-sm-7 mb-20">
                                        <img class="img-fluid invoice-brand-img d-block mb-20" src="dist/img/invoice-logo.png" alt="brand" />
                                        <h6 class="mb-5">Sai Auto Care</h6>
                                        <address>
												<span class="d-block">2376, Puri Bypass NH</span>
												<span class="d-block">Badagada, Bhubaneswar-751019</span>
												<span class="d-block">AutoCare18@gmail.com</span>
                                                <span class="d-block">07683855014/09437284700</span>                
											</address>
                                    </div>
                                    <div class="col-sm-5 mb-20">
                                        <h4 class="mb-35 font-weight-600">Receipt</h4>
                                        <span class="d-block">Date:<span class="pl-10 text-dark">{{  $created_at }} }}</span></span>
                                        <span class="d-block">Job Id #<span class="pl-10 text-dark">{{ $id }}</span></span>
                                        <span class="d-block">Customer #<span class="pl-10 text-dark">321434</span></span>
                                    </div>
                                </div>
                            </div>
                            <hr class="mt-0">
                            <div class="invoice-to-wrap pb-20">
                                <div class="row">
                                    <div class="col-sm-7 mb-30">
                                        <span class="d-block text-uppercase mb-5 font-13">billing to</span>
                                        <h6 class="mb-5">Supersonic Co.</h6>
                                        <address>
												<span class="d-block">Sycamore Street</span>
												<span class="d-block">San Antonio Valley, CA 34668</span>
												<span class="d-block">thompson_peter@super.co</span>
												<span class="d-block">ABC:325487</span>
											</address>
                                    </div>
                                    <div class="col-sm-5 mb-30">
                                        <span class="d-block text-uppercase mb-5 font-13">Payment info</span>
                                        <span class="d-block">Scott L Thompson</span>
                                        <span class="d-block">MasterCard#########1234</span>
                                        <span class="d-block">Customer #<span class="text-dark">324148</span></span>
                                        <span class="d-block text-uppercase mt-20 mb-5 font-13">amount due</span>
                                        <span class="d-block text-dark font-18 font-weight-600">$22,010</span>
                                    </div>
                                </div>
                            </div>
                            <h5>Parts/Services Details</h5>
                            <hr>
                            <div class="invoice-details">
                                <div class="table-wrap">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-border mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="w-70">Items</th>
                                                    <th class="text-right">Number</th>
                                                    <th class="text-right">Unit Cost</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="w-70">Design Service</td>
                                                    <td class="text-right">2</td>
                                                    <td class="text-right">$1500</td>
                                                    <td class="text-right">$3000</td>
                                                </tr>
                                                <tr>
                                                    <td class="w-70">Website Development</td>
                                                    <td class="text-right">1</td>
                                                    <td class="text-right">$7500</td>
                                                    <td class="text-right">$7500</td>
                                                </tr>
                                                <tr>
                                                    <td class="w-70">Social Media Services</td>
                                                    <td class="text-right">15</td>
                                                    <td class="text-right">$180</td>
                                                    <td class="text-right">$9000</td>
                                                </tr>
                                                <tr class="bg-transparent">
                                                    <td colspan="3" class="text-right text-light">Subtotals</td>
                                                    <td class="text-right">$19,500</td>
                                                </tr>
                                                <tr class="bg-transparent">
                                                    <td colspan="3" class="text-right text-light border-top-0">Tax</td>
                                                    <td class="text-right border-top-0">$3510</td>
                                                </tr>
                                                <tr class="bg-transparent">
                                                    <td colspan="3" class="text-right text-light border-top-0">Discount</td>
                                                    <td class="text-right border-top-0">$1000</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="border-bottom border-1">
                                                <tr>
                                                    <th colspan="3" class="text-right font-weight-600">total</th>
                                                    <th class="text-right font-weight-600">$22,010</th>
                                                </tr>
                                            </tfoot>
                                            <table class="table table-striped table-border mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="w-70">Items</th>
                                                    <th class="text-right">Number</th>
                                                    <th class="text-right">Unit Cost</th>
                                                    <th class="text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="w-70">Design Service</td>
                                                    <td class="text-right">2</td>
                                                    <td class="text-right">$1500</td>
                                                    <td class="text-right">$3000</td>
                                                </tr>
                                                <tr>
                                                    <td class="w-70">Website Development</td>
                                                    <td class="text-right">1</td>
                                                    <td class="text-right">$7500</td>
                                                    <td class="text-right">$7500</td>
                                                </tr>
                                                <tr>
                                                    <td class="w-70">Social Media Services</td>
                                                    <td class="text-right">15</td>
                                                    <td class="text-right">$180</td>
                                                    <td class="text-right">$9000</td>
                                                </tr>
                                                <tr class="bg-transparent">
                                                    <td colspan="3" class="text-right text-light">Subtotals</td>
                                                    <td class="text-right">$19,500</td>
                                                </tr>
                                                <tr class="bg-transparent">
                                                    <td colspan="3" class="text-right text-light border-top-0">Tax</td>
                                                    <td class="text-right border-top-0">$3510</td>
                                                </tr>
                                                <tr class="bg-transparent">
                                                    <td colspan="3" class="text-right text-light border-top-0">Discount</td>
                                                    <td class="text-right border-top-0">$1000</td>
                                                </tr>
                                            </tbody>
                                            <tfoot class="border-bottom border-1">
                                                <tr>
                                                    <th colspan="3" class="text-right font-weight-600">total</th>
                                                    <th class="text-right font-weight-600">$22,010</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="invoice-sign-wrap text-right py-60">
                                    <img class="img-fluid d-inline-block" src="dist/img/signature.png" alt="sign" />
                                    <span class="d-block text-light font-14">Digital Signature</span>
                                </div>
                            </div>
                            <hr>
                            <ul class="invoice-terms-wrap font-14 list-ul">
                                <li>A buyer must settle his or her account within 30 days of the date listed on the invoice.</li>
                                <li>The conditions under which a seller will complete a sale. Typically, these terms specify the period allowed to a buyer to pay off the amount due.</li>
                            </ul>
                        </section>
                    </div>
                </div>
                <!-- /Row -->
            </div>
            <!-- /Container -->

           

        </div>
        <!-- /Main Content -->

    </div>
  

</body>


<!-- Mirrored from hencework.com/theme/brunette/invoice.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 20 Jan 2019 17:38:49 GMT -->
</html>