<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brandy Invoice | فاتورة براندي</title>
    <!-- Metronic 8 CSS -->

    

    <link href="https://preview.keenthemes.com/metronic8/demo1/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="https://preview.keenthemes.com/metronic8/demo1/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!-- Custom Invoice Styles -->


    <link href="invoice-styles.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
        /* Invoice Styles using Metronic 8 Framework */
body {
            font-family: 'Amiri', serif; /* أو DejaVu Sans */
            direction: rtl;
            text-align: right;
        }
.invoice-container {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.invoice-header {
    background: #f8f9fa;
    border-bottom: 3px solid #e4a92a;
}

.company-logo {
    width: 60px;
    height: 60px;
    background: #e4a92a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.invoice-title {
    font-size: 24px;
    font-weight: bold;
    color: #181c32;
}

.invoice-title-ar {
    font-size: 20px;
    color: #7e8299;
}

.total-amount {
    font-size: 32px;
    font-weight: bold;
    color: #181c32;
}

.total-amount-green {
    font-size: 24px;
    font-weight: bold;
    color: #50cd89;
}


.text-primary{
    color: #e4a92a !important;
}



.btn.btn-primary:hover .show>.btn.btn-primary {
    border-color: #e4a92a;
    background-color: #e4a92a
 !important;
}

.qr-code {
    width: 120px;
    height: 120px;
    background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTAgMTBIMzBWMzBIMTBWMTBaIiBmaWxsPSJibGFjayIvPgo8cGF0aCBkPSJNNDAgMTBINjBWMzBINDBWMTBaIiBmaWxsPSJibGFjayIvPgo8cGF0aCBkPSJNNzAgMTBIOTBWMzBINzBWMTBaIiBmaWxsPSJibGFjayIvPgo8cGF0aCBkPSJNMTAgNDBIMzBWNjBIMTBWNDBaIiBmaWxsPSJibGFjayIvPgo8cGF0aCBkPSJNNzAgNDBIOTBWNjBINzBWNDBaIiBmaWxsPSJibGFjayIvPgo8cGF0aCBkPSJNMTAgNzBIMzBWOTBIMTBWNzBaIiBmaWxsPSJibGFjayIvPgo8cGF0aCBkPSJNNDAgNzBINjBWOTBINDBWNzBaIiBmaWxsPSJibGFjayIvPgo8cGF0aCBkPSJNNzAgNzBIOTBWOTBINzBWNzBaIiBmaWxsPSJibGFjayIvPgo8L3N2Zz4K') no-repeat center;
    background-size: contain;
    border: 1px solid #e1e3ea;
}

.table-header {
    background: #e4a92a;
    color: white;
}

.text-ar {
    direction: rtl;
    text-align: right;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .invoice-container {
        margin: 10px;
    }
    
    .invoice-title {
        font-size: 20px;
    }
    
    .total-amount {
        font-size: 24px;
    }
    
    .qr-code {
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }
}
 

    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-10 col-md-8" style="background: white;">
        <div class="invoice-container">
            <!-- Header -->
            <div class="invoice-header p-8">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex text-center">
                            <div class="company-logo me-4" style="background:#e4a92a">
                                <i class="ki-duotone ki-car fs-2x"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-gray-800"> تطبيق براندي</div>
                                <div class="text-muted">Brandy App</div>
                                <div class="fw-bold text-gray-800">التجارة الالكترونية</div>

                                <div class="text-muted fs-7">E-commerce</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="invoice-title">
                            <img src="{{url('assets/media/svg/brand-logos/logo.png')}}" width="55" />
                        </div>
                        <!-- <div class="invoice-title-ar text-ar">فاتورة ضريبية مبسطة</div> -->
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="text-ar text-center">
                            <div class="fw-bold">رقم الفاتورة: INV-{{(int) $invoice->amount}}-{{$invoice->order_number}}</div>
                            <div class="text-muted">Invoice Number</div>
                            <div class="fw-bold mt-2">التاريخ: {{$invoice->created_at}}</div>
                            <div class="text-muted">Date</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="p-8 border-bottom">
                <div class="row">
                    <div class="col-md-4">
                        <div class="fw-bold text-gray-800 mb-2 text-center">Customer / العميل</div>
                        <div class="text-muted text-center">{{$invoice->user->name}}</div>
                       
                    </div>


                    <div class="col-md-4">
                        <div class="fw-bold text-gray-800 text-center">فاتورة الكترونية</div>
                        <div class="text-ar text-muted text-center">E - Invoice</div>
                    </div>


                    <div class="col-md-4 text-end ">
                        <div class="text-ar">
                            <div class="fw-bold text-center">الرقم المرجعي: {{$invoice->track_id}}</div>
                            <div class="text-muted text-center">#REF</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="p-8">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-header">
                            <tr>
                                <th class="text-center">المجموع / Amount</th>
                                <th class="text-center">مبلغ الضريبة / Tax</th>
                                <th class="text-center"> الضريبة / Tax %</th>
                                <th class="text-center"> السعر/Price</th>
                                

                                <th class="text-center">الكمية / Quantity</th>
                                <th class="text-center">الوصف / Description</th>

                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                
                                <td class="text-center">{{$invoice->amount}}</td>
                                <td class="text-center">{{$invoice->amount * 15 /100}}</td>
                                <td class="text-center fw-bold">15</td>
                                <td class="text-center fw-bold">{{$invoice->amount}}</td>
                                <td class="text-center fw-bold">1.00</td>


                                <td class="text-center">
                                    <div class="fw-bold"> عمولة/Comission </div>
                                    <div class="text-muted" v-if="$invoice->payment_type">{{$invoice->payment_type}}</div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div class="row mt-8">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">المجموع الفرعي / Subtotal:</span>
                                    <span class="fw-bold">{{$invoice->amount}} ر.س</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>ضريبة القيمة المضافة (15%) / VAT (15%):</span>
                                    <span>0.00 ر.س</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-4 fw-bold">الإجمالي / Total:</span>
                                    <span class="total-amount">{{$invoice->amount}} ر.س</span>
                                </div>
                                <div class="text-center mt-4">
                                    <div class="total-amount-green">{{$invoice->amount}}ر.س</div>
                                    <div class="text-muted">المبلغ المستحق / Amount Due</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="row mt-8 pt-8 border-top">
                    <div class="col-md-4 text-center">
                        <div class="fw-bold text-primary" >الرقم الضريبي</div>
                        <div class="text-muted">Tax Number</div>
                        <div class="fw-bold">310122393500003</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="fw-bold text-primary">الرقم التجاري</div>
                        <div class="text-muted">Commercial Registration</div>
                        <div class="fw-bold">4030123456</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="fw-bold text-primary">تاريخ الإصدار</div>
                        <div class="text-muted">Issue Date</div>
                        <div class="fw-bold">{{$invoice->updated_at}}</div>
                    </div>
                </div>

                <a style="margin-top: 20px;background: #e4a92a" href="{{ route('user.order.download', $invoice->id) }}" target="_blank" class="btn btn-warning btn-hover">
                    تنزيل / Downlaod PDF
                </a>

            </div>
        </div>
    </div>

    <!-- Metronic 8 JS -->
    <script src="https://preview.keenthemes.com/metronic8/demo1/assets/plugins/global/plugins.bundle.js"></script>
    <script src="https://preview.keenthemes.com/metronic8/demo1/assets/js/scripts.bundle.js"></script>

    <iframe id="pdfDownloader" style="display:none;"></iframe>
<!-- <script>
    window.onload = function() {
        document.getElementById('pdfDownloader').src = "{{ route('user.order.download', $invoice->id) }}";
    }
</script> -->



</body>

</html> 