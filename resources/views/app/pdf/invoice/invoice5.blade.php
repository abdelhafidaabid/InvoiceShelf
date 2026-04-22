<!DOCTYPE html>
<html>
<head>
    <title>@lang('pdf_invoice_label') - {{ $invoice->invoice_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @php
        $tableColor = \App\Models\CompanySetting::getSetting('invoice_pdf_color', $invoice->company_id) ?? '#a47833';
    @endphp
    <style type="text/css">
        /* -- Base & Fonts -- */
        body {
            font-family: "DejaVu Sans", "Helvetica Neue", Arial, sans-serif;
            color: #222222;
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
            z-index: -1;
        }
        .bottom-right-shape {
            position: absolute;
            bottom: 0;
            right: 0;
            z-index: -1;
        }

        /* -- Header -- */
        .header-container {
            width: 100%;
            padding: 40px 40px 0 40px;
            position: relative;
        }
        .header-logo {
            height: 100px;
            width: auto;
            max-width: 250px;
        }
        .header-title-text {
            font-size: 48px;
            color: #000;
            font-weight: bold;
        }
        .header-year {
            font-size: 18px;
            vertical-align: super;
            margin-left: 2px;
        }

        /* -- Content Wrapper -- */
        .content-wrapper {
            display: block;
            padding: 0 40px;
            margin-top: 20px;
        }

        /* -- Client Info -- */
        .client-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .client-label {
            color: {{ $tableColor }};
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .client-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .client-details {
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        /* -- Items Table -- */
        .items-table {
            width: 100%;
            margin-top: 20px;
            border: 1px solid #000;
        }
        tr.item-table-heading-row {
            background-color: {{ $tableColor }};
        }
        tr.item-table-heading-row th {
            padding: 10px;
            color: #FFF;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #000;
        }
        tr.item-row td {
            padding: 10px;
            font-size: 11px;
            border: 1px solid #000;
            color: #000;
            font-weight: bold;
        }
        .item-description {
            color: #333;
            font-size: 10px;
            font-weight: normal;
            display: block;
            margin-top: 4px;
        }

        /* -- Totals -- */
        .total-display-container {
            width: 100%;
            margin-top: 0px;
        }
        .total-display-table {
            float: right;
            width: 30%;
            border-collapse: collapse;
        }
        .total-display-table td {
            border: 1px solid #000;
            padding: 5px 10px;
            font-weight: bold;
        }
        .total-label {
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
        }
        .total-value {
            text-align: right;
            font-size: 11px;
        }

        /* -- Amount in Words -- */
        .amount-in-words-section {
            margin-top: 40px;
            font-size: 11px;
            font-weight: bold;
        }
        .amount-highlight {
            color: #1a2332;
        }
        .amount-words {
            font-size: 18px;
            color: #1a2332;
            font-family: serif;
        }

        /* -- Footer -- */
        .footer-company-info {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 200px;
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }
        .footer-label {
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Background Shapes -->
    <div class="top-left-shape">
        <svg width="400" height="150">
            <path d="M 0 0 L 400 0 L 0 150 Z" fill="#1a2332" />
        </svg>
    </div>
    <div class="bottom-right-shape">
        <svg width="250" height="150">
            <path d="M 250 150 L 250 0 L 0 150 Z" fill="{{ $tableColor }}" />
        </svg>
    </div>

    <div class="header-container">
        <table width="100%">
            <tr>
                <td width="60%" style="vertical-align: top; padding-top: 20px;">
                    <div class="header-title-text">
                        Facture<span class="header-year">{{ date('Y') }}</span>
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
            <div class="client-label">CLIENT</div>
            <div class="client-name">{{ $invoice->customer->name }}</div>
            <div class="client-details">
                {!! str_replace('<br>', ' - ', $billing_address) !!}
            </div>
        </div>

        <div style="clear: both;"></div>

        <!-- Table Partial Render -->
        <div style="position: relative; clear: both;">
            @include('app.pdf.invoice.partials.table')
        </div>

        <div style="clear: both;"></div>

        <!-- Notes / Payment terms -->
        <div class="notes" style="margin-top: 40px;">
            @if ($notes)
                <div class="notes-label" style="font-weight: bold; margin-bottom: 5px;">
                    @lang('pdf_notes')
                </div>
                <div style="font-size: 10px; color: #444;">
                    {!! $notes !!}
                </div>
            @endif
        </div>
    </div>

    <!-- Absolute Footer Information -->
    @if ($invoice->company)
        <div class="footer-company-info">
            <div class="footer-label">{{ $invoice->company->name }}</div>
            {!! $company_address !!}
        </div>
    @endif

</body>
</html>
