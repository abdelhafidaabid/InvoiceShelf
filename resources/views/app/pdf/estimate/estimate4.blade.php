<!DOCTYPE html>
<html>
<head>
    <title>{{ $estimate->getPdfLabel('estimate_pdf_label', 'pdf_estimate_label') }} - {{ $estimate->estimate_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type="text/css">
        /* -- Base & Fonts -- */
        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
            color: #222222;
            font-size: 11px;
        }

        html {
            margin: 0px;
            padding: 0px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        table {
            border-collapse: collapse;
        }

        hr {
            margin: 0 30px;
            color: rgba(0, 0, 0, 0.2);
            border: 0.5px solid #EAF1FB;
        }

        /* typography */
        .text-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }

        /* -- Header -- */
        .header-container {
            width: 100%;
            padding: 0 40px;
            margin-bottom: 30px;
            margin-top: -20px;
        }
        .header-logo {
            height: 100px;
            width: auto;
            max-width: 250px;
        }
        .header-title-text {
            font-size: 22px;
            color: #111;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        
        .header-bottom-divider {
            margin: 0 40px 30px 40px;
            border: none;
            border-bottom: 2px solid #DDE2E7;
        }

        /* -- Content Wrapper -- */
        .content-wrapper {
            display: block;
            padding: 0 40px;
        }

        /* -- Meta Info (Client / Company) -- */
        .customer-address-container {
            display: block;
            float: left;
            width: 45%;
        }
        .billing-address-container {
            display: block;
            margin-bottom: 15px;
        }
        .billing-address {
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            margin-top: 5px;
        }
        .shipping-address-container {
            display: block;
        }
        .shipping-address {
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            margin-top: 5px;
        }

        /*  -- Estimate Details -- */
        .invoice-details-container {
            display: block;
            float: right;
            width: 45%;
        }
        .invoice-details-container table {
            width: 100%;
        }
        .attribute-label {
            font-size: 11px;
            line-height: 20px;
            text-align: left;
            color: #555;
            font-weight: bold;
        }
        .attribute-value {
            font-size: 11px;
            line-height: 20px;
            text-align: right;
            color: #111;
        }

        /* -- Items Table Override from Partial -- */
        .items-table {
            width: 100%;
            margin-top: 40px;
            border: none;
            border-top: 2px solid #DDE2E7;
            border-bottom: 2px solid #DDE2E7;
        }
        tr.item-table-heading-row {
            background-color: #F8F9FA;
        }
        tr.item-table-heading-row th {
            padding: 12px 10px;
            color: #333;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border: none;
        }
        tr.item-row td {
            padding: 12px 10px;
            font-size: 11px;
            border-bottom: 1px solid #EBEBEB;
            color: #222;
        }
        .item-description {
            color: #666;
            font-size: 9px;
            display: block;
            margin-top: 4px;
        }
        .item-cell-table-hr { display: none; }

        /* -- Total Display Table Override -- */
        .total-display-container {
            width: 100%;
            margin-top: 20px;
        }
        .total-display-table {
            float: right;
            width: 40%;
            border-collapse: collapse;
        }
        .total-display-table tr {
            border-bottom: 1px solid #EBEBEB;
        }
        .total-display-table tr:last-child {
            border-bottom: 2px solid #DDE2E7;
        }
        .total-table-attribute-label {
            font-size: 11px;
            font-weight: bold;
            padding: 12px 10px;
            text-align: left;
            color: #555;
        }
        .total-table-attribute-value {
            font-size: 12px;
            font-weight: bold;
            padding: 12px 10px;
            text-align: right;
            color: #111;
        }
        .total-border-left, .total-border-right, .border-0 {
            border: none;
        }

        /* -- Notes & Footer -- */
        .notes {
            margin-top: 60px;
            width: 50%;
            font-size: 10px;
            line-height: 1.5;
            color: #444;
        }
        .notes-label {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #111;
        }
        
        .footer-company-info {
            position: absolute;
            bottom: -10px;
            left: 40px;
            right: 40px;
            width: 100%;
            font-size: 9px;
            color: #666;
            line-height: 1.4;
            border-top: 1px solid #DDE2E7;
            padding-top: 15px;
            text-align: center;
        }

        /* -- Helpers -- */
        .py-2 { padding-top: 2px; padding-bottom: 2px; }
        .py-8 { padding-top: 8px; padding-bottom: 8px; }
        .py-3 { padding: 3px 0; }
        .pr-20 { padding-right: 20px; }
        .pr-10 { padding-right: 10px; }
        .pl-20 { padding-left: 20px; }
        .pl-10 { padding-left: 10px; }
        .pl-0 { padding-left: 0; }
        .item-cell { text-align: center; }
        .text-primary { color: #5851DB; }
    </style>
</head>
<body>
    <div class="header-container">
        <table width="100%">
            <tr>
                <td width="50%" class="text-left" style="vertical-align: middle;">
                    @if ($logo)
                        <img class="header-logo" src="{{ \App\Space\ImageUtils::toBase64Src($logo) }}" alt="Logo">
                    @else
                        @if ($estimate->customer->company)
                            <h2 class="header-logo" style="margin-top: 0;">{{ $estimate->customer->company->name }}</h2>
                        @endif
                    @endif
                </td>
                <td width="50%" style="vertical-align: middle;">
                    <div class="header-title-text">
                        {{ $estimate->getPdfLabel('estimate_pdf_label', 'pdf_estimate_label') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <hr class="header-bottom-divider">

    <div class="content-wrapper">
        
        <!-- Company & Client Details block -->
        <div>
            <div class="customer-address-container">
                <div class="billing-address-container">
                    @if ($billing_address)
                        <div class="billing-address">
                            <b>{{ $estimate->getPdfLabel('estimate_pdf_bill_to_label', 'pdf_estimate_to') }}</b> <br>
                            {!! $billing_address !!}
                        </div>
                    @endif
                </div>

                @if ($show_shipping_address)
                    <div class="shipping-address-container">
                        @if ($shipping_address)
                            <div class="shipping-address">
                                <b>{{ $estimate->getPdfLabel('estimate_pdf_ship_to_label', 'pdf_ship_to') }}</b> <br>
                                {!! $shipping_address !!}
                            </div>
                        @endif
                    </div>
                @endif
                <div style="clear: both;"></div>
            </div>

            <div class="invoice-details-container">
                <table>
                    <tr>
                        <td class="attribute-label">{{ $estimate->getPdfLabel('estimate_pdf_number_label', 'pdf_estimate_number') }}</td>
                        <td class="attribute-value">{{ $estimate->estimate_number }}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">{{ $estimate->getPdfLabel('estimate_pdf_date_label', 'pdf_estimate_date') }}</td>
                        <td class="attribute-value">{{ $estimate->formattedEstimateDate }}</td>
                    </tr>
                    <tr>
                        <td class="attribute-label">{{ $estimate->getPdfLabel('estimate_pdf_expiry_date_label', 'pdf_estimate_expire_date') }}</td>
                        <td class="attribute-value">{{ $estimate->formattedExpiryDate }}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div style="clear: both;"></div>

        <!-- Table Partial Render -->
        <div style="position: relative; clear: both;">
            @include('app.pdf.estimate.partials.table')
        </div>

        <div style="clear: both;"></div>

        <!-- Notes / Payment terms -->
        <div class="notes">
            @if ($notes)
                <div class="notes-label">
                    {{ $estimate->getPdfLabel('estimate_pdf_notes_label', 'pdf_notes') }}
                </div>
                <div>
                    {!! $notes !!}
                </div>
            @endif
        </div>
    </div>

    <!-- Absolute Footer Information -->
    @if ($estimate->company)
        <div class="footer-company-info">
            {!! $company_address !!}
        </div>
    @endif

</body>
</html>
