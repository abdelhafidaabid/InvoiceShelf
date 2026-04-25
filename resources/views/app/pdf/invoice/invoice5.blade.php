<!DOCTYPE html>
<html>
<head>
    <title>{{ $invoice->getPdfLabel('invoice_pdf_label', 'pdf_invoice_label') }} - {{ $invoice->invoice_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @php
        $mainColor = $invoice->company->pdf_main_color ?? '#a47833';
        $secondaryColor = $invoice->company->pdf_secondary_color ?? '#1a2332';
    @endphp
    <style type="text/css">
        /* -- Base & Fonts -- */
        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
            color: #000000;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        html {
            margin: 0px;
            padding: 0px;
        }

        table {
            border-collapse: collapse;
        }

        /* typography */
        .text-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-uppercase { text-transform: uppercase; }

        /* -- Corner Shapes -- */
        .top-left-shape {
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 120px 300px 0 0;
            border-color: {{ $secondaryColor }} transparent transparent transparent;
            z-index: -1;
        }
        .bottom-right-shape {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 0 120px 200px;
            border-color: transparent transparent {{ $secondaryColor }} transparent;
            z-index: -1;
        }
        .bottom-right-shape-gold {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 0 100px 180px;
            border-color: transparent transparent {{ $mainColor }} transparent;
            z-index: -1;
        }

        /* -- Header -- */
        .header-container {
            width: 100%;
            padding: 40px 60px 0 60px;
            position: relative;
        }
        .header-logo {
            height: 110px;
            width: auto;
        }
        .header-title-text {
            font-size: 42px;
            color: #000;
            font-weight: bold;
        }
        .header-year {
            font-size: 18px;
            margin-left: 5px;
        }
        .header-metadata {
            margin-top: 15px;
            font-size: 14px;
            color: #000;
        }
        .metadata-label {
            color: {{ $mainColor }};
            font-weight: bold;
        }
        .metadata-value {
            font-weight: bold;
        }

        /* -- Content Wrapper -- */
        .content-wrapper {
            display: block;
            padding: 0 60px;
            margin-top: 30px;
        }

        /* -- Client Info -- */
        .client-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .client-label {
            color: {{ $mainColor }};
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .client-address {
            font-size: 14px;
            line-height: 1.5;
        }

        /* -- Items Table -- */
        .items-table {
            width: 100%;
            border-top: 1px solid {{ $mainColor }};
            border-bottom: 1px solid {{ $mainColor }};
        }
        tr.item-table-heading-row {
            background-color: {{ $mainColor }} !important;
        }
        tr.item-table-heading-row th {
            padding: 10px;
            color: #FFF !important;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
        }
        tr.item-row td {
            padding: 12px 10px;
            font-size: 11px;
            border-bottom: 0.5px solid {{ $mainColor }};
            color: #000;
        }
        .item-description {
            color: #333;
            font-size: 10px;
            display: block;
            margin-top: 5px;
            line-height: 1.4;
        }

        /* -- Totals -- */
        .total-display-container {
            width: 100%;
            margin-top: 5px;
        }
        .total-display-table {
            float: right;
            width: auto;
            min-width: 280px;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .total-table-attribute-label {
            text-align: left;
            padding: 10px 15px;
            border-bottom: 1px solid #EEE;
            font-size: 12px;
            color: #555;
            font-weight: bold;
        }
        .total-table-attribute-value {
            text-align: right;
            padding: 10px 15px;
            border-bottom: 1px solid #EEE;
            font-size: 12px;
            font-weight: bold;
        }
        .total-row {
            background-color: #F9FAFB;
        }
        .total-row td {
            border-top: 1px solid {{ $mainColor }};
            border-bottom: 1px solid {{ $mainColor }};
            padding: 12px 15px;
        }
        .notes {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .notes-label {
            font-weight: bold;
            text-transform: uppercase;
            color: {{ $mainColor }};
            margin-bottom: 5px;
            font-size: 11px;
        }
        .notes-content {
            font-size: 10px;
            line-height: 1.4;
        }

        /* -- Footer -- */
        .footer-company-info {
            position: absolute;
            bottom: 40px;
            left: 60px;
            right: 60px;
            font-size: 10px;
            color: #000;
            line-height: 1.6;
        }
        .footer-row {
            margin-bottom: 2px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .footer-label {
            font-weight: bold;
        }
        .footer-link {
            color: #004d99;
            text-decoration: none;
        }

        /* -- Helpers -- */
        .text-center { text-align: center }
        .text-left { text-align: left }
        .text-right { text-align: right }
        .border-0 { border: none !important; }
        .py-2 { padding-top: 2px; padding-bottom: 2px; }
        .py-3 { padding: 3px 0; }
        .py-8 { padding-top: 8px; padding-bottom: 8px; }
        .pr-20 { padding-right: 20px; }
        .pr-10 { padding-right: 10px; }
        .pl-20 { padding-left: 20px; }
        .pl-10 { padding-left: 10px; }
        .pl-0 { padding-left: 0; }
        .total-border-left { border: 1px solid {{ $mainColor }} !important; border-right: 0px !important; padding: 8px !important; }
        .total-border-right { border: 1px solid {{ $mainColor }} !important; border-left: 0px !important; padding: 8px !important; }
        .item-cell-table-hr { margin: 0 30px 0 30px; color: rgba(0, 0, 0, 0.2); border: 0.5px solid #EAF1FB; }
    </style>
</head>
<body>
    <!-- Background Shapes -->
    <div class="top-left-shape"></div>
    <div class="bottom-right-shape"></div>
    <div class="bottom-right-shape-gold"></div>

    <div class="header-container">
        <table width="100%">
            <tr>
                <td width="60%" style="vertical-align: top; padding-top: 10px;">
                    <div class="header-title-text text-uppercase">
                        {{ $invoice->getPdfLabel('invoice_pdf_label', 'pdf_invoice_label') }} <span class="header-year">{{ date('Y') }}</span>
                    </div>
                    <div class="header-metadata">
                        <span class="metadata-label">{{ $invoice->getPdfLabel('invoice_pdf_number_label', 'pdf_invoice_number') }} :</span> <span class="metadata-value">{{ $invoice->invoice_number }}</span> <br>
                        <span class="metadata-label">{{ $invoice->getPdfLabel('invoice_pdf_date_label', 'pdf_invoice_date') }} :</span> <span class="metadata-value">{{ $invoice->formattedInvoiceDate }}</span>
                    </div>
                </td>
                <td width="40%" class="text-right" style="vertical-align: top;">
                    @if ($logo)
                        <img class="header-logo" src="{{ \App\Space\ImageUtils::toBase64Src($logo) }}" alt="Logo">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="content-wrapper">
        <div class="client-section">
            <div class="client-label">{{ $invoice->getPdfLabel('invoice_pdf_bill_to_label', 'pdf_bill_to') }}</div>
            <div class="client-address">
                {!! $billing_address !!}
            </div>
            
            @if($show_shipping_address && $shipping_address)
                <div class="client-label" style="margin-top: 20px;">{{ $invoice->getPdfLabel('invoice_pdf_ship_to_label', 'pdf_ship_to') }}</div>
                <div class="client-address">
                    {!! $shipping_address !!}
                </div>
            @endif
        </div>

        <div style="clear: both;"></div>

        <div style="position: relative; clear: both;">
            @include('app.pdf.invoice.partials.table5')
        </div>

        <div style="clear: both;"></div>

        <!-- Signature Area -->
        <div style="margin-top: 40px; text-align: right; padding-right: 60px;">
            <div style="display: inline-block; text-align: center;">
                <div style="font-weight: bold; margin-bottom: 10px;">{{ $invoice->getPdfLabel('invoice_pdf_signature_stamp_label', 'pdf_signature_stamp') }}</div>
                @if($stamp)
                    <div style="margin-bottom: 5px;">
                        <img src="{{ \App\Space\ImageUtils::toBase64Src($stamp) }}" style="max-height: 100px; max-width: 200px;">
                    </div>
                @else
                    <div style="margin-bottom: 50px;"></div>
                @endif
                <div style="border-bottom: 1px solid #ccc; width: 200px;"></div>
            </div>
        </div>
    </div>

    <!-- Absolute Footer Information -->
    @if ($invoice->company)
        <div class="footer-company-info">
            {!! $company_address !!}
        </div>
    @endif

</body>
</html>
