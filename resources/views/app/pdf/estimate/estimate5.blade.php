<!DOCTYPE html>
<html>
<head>
    <title>@lang('pdf_estimate_label') - {{ $estimate->estimate_number }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @php
        $tableColor = \App\Models\CompanySetting::getSetting('estimate_pdf_color', $estimate->company_id) ?? '#a47833';
        
        // Simple Amount to Words conversion for French (simplified for common use)
        function amountToWords($number) {
            $hyphen      = '-';
            $conjunction = ' et ';
            $separator   = ', ';
            $negative    = 'moins ';
            $decimal     = ' point ';
            $dictionary  = array(
                0                   => 'zéro',
                1                   => 'un',
                2                   => 'deux',
                3                   => 'trois',
                4                   => 'quatre',
                5                   => 'cinq',
                6                   => 'six',
                7                   => 'sept',
                8                   => 'huit',
                9                   => 'neuf',
                10                  => 'dix',
                11                  => 'onze',
                12                  => 'douze',
                13                  => 'treize',
                14                  => 'quatorze',
                15                  => 'quinze',
                16                  => 'seize',
                17                  => 'dix-sept',
                18                  => 'dix-huit',
                19                  => 'dix-neuf',
                20                  => 'vingt',
                30                  => 'trente',
                40                  => 'quarante',
                50                  => 'cinquante',
                60                  => 'soixante',
                70                  => 'soixante-dix',
                80                  => 'quatre-vingt',
                90                  => 'quatre-vingt-dix',
                100                 => 'cent',
                1000                => 'mille',
                1000000             => 'million',
                1000000000          => 'milliard'
            );
            
            if (!is_numeric($number)) return false;
            
            if ($number < 0) return $negative . amountToWords(abs($number));
            
            $string = null;
            $fraction = null;
            
            if (strpos($number, '.') !== false) {
                list($number, $fraction) = explode('.', $number);
            }
            
            switch (true) {
                case $number < 21:
                    $string = $dictionary[$number];
                    break;
                case $number < 100:
                    $tens   = ((int) ($number / 10)) * 10;
                    $units  = $number % 10;
                    $string = $dictionary[$tens];
                    if ($units) {
                        $string .= ($units == 1 && $tens != 80 ? $conjunction : $hyphen) . $dictionary[$units];
                    }
                    break;
                case $number < 1000:
                    $hundreds  = $number / 100;
                    $remainder = $number % 100;
                    $string = ($hundreds >= 2 ? $dictionary[$hundreds] . ' ' : '') . $dictionary[100];
                    if ($remainder) {
                        $string .= ' ' . amountToWords($remainder);
                    }
                    break;
                default:
                    $baseUnit = pow(1000, floor(log($number, 1000)));
                    $numBaseUnits = (int) ($number / $baseUnit);
                    $remainder = $number % $baseUnit;
                    $string = amountToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                    if ($numBaseUnits > 1 && $baseUnit != 1000) $string .= 's';
                    if ($remainder) {
                        $string .= ' ' . amountToWords($remainder);
                    }
                    break;
            }
            
            return $string;
        }
        
        $totalInWords = amountToWords($estimate->total);
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
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 150px 400px 0 0;
            border-color: #1a2332 transparent transparent transparent;
            z-index: -1;
        }
        .bottom-right-shape {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 0 150px 250px;
            border-color: transparent transparent {{ $tableColor }} transparent;
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
        .header-metadata {
            margin-top: 10px;
            font-size: 13px;
            font-weight: bold;
            color: #333;
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
            margin-bottom: 20px;
        }
        .client-label {
            color: {{ $tableColor }};
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .client-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .client-details {
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            font-weight: bold;
        }

        /* -- Items Table -- */
        .items-table {
            width: 100%;
            margin-top: 20px;
            border: 1px solid #000;
        }
        tr.item-table-heading-row {
            background-color: {{ $tableColor }} !important;
        }
        tr.item-table-heading-row th {
            padding: 10px;
            color: #FFF !important;
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
            margin-top: 10px;
        }
        .total-display-table {
            float: right;
            width: 35%;
            border-collapse: collapse;
        }
        .total-display-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            font-weight: bold;
        }
        .total-table-attribute-label {
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            background-color: #f9f9f9;
        }
        .total-table-attribute-value {
            text-align: right;
            font-size: 11px;
            color: #000 !important;
        }
        
        /* Specific override for total row */
        .total-display-table tr:last-child td {
            background-color: #f0f0f0;
        }
        .total-display-table tr:last-child .total-table-attribute-value {
            color: {{ $tableColor }} !important;
            font-size: 13px;
        }

        /* -- Amount in Words -- */
        .amount-in-words-section {
            margin-top: 40px;
            font-size: 11px;
            font-weight: bold;
            clear: both;
        }
        .amount-words {
            font-style: italic;
            text-transform: capitalize;
        }

        /* -- Notes -- */
        .notes {
            margin-top: 30px;
        }
        .notes-label {
            font-weight: bold;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        /* -- Footer -- */
        .footer-company-info {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            font-size: 10px;
            color: #000;
            line-height: 1.6;
        }
        .footer-company-name {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Background Shapes -->
    <div class="top-left-shape"></div>
    <div class="bottom-right-shape"></div>

    <div class="header-container">
        <table width="100%">
            <tr>
                <td width="60%" style="vertical-align: top; padding-top: 20px;">
                    <div class="header-title-text">
                        @lang('pdf_estimate_label')<span class="header-year">{{ date('Y') }}</span>
                    </div>
                    <div class="header-metadata">
                        {{ $estimate->estimate_number }} <br>
                        {{ $estimate->formattedEstimateDate }}
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
            <div class="client-label">@lang('pdf_estimate_to')</div>
            <div class="client-name">{{ $estimate->customer->name }}</div>
            @if($estimate->customer->company_name)
                <div class="client-name">{{ $estimate->customer->company_name }}</div>
            @endif
            <div class="client-details">
                {!! str_replace('<br>', ' - ', $billing_address) !!}
            </div>
        </div>

        <div style="clear: both;"></div>

        <!-- Table Partial Render -->
        <div style="position: relative; clear: both;">
            @include('app.pdf.estimate.partials.table')
        </div>

        <div style="clear: both;"></div>

        <!-- Amount in Words -->
        <div class="amount-in-words-section">
            Arrêté le présent devis à la somme de : <br>
            <span class="amount-words">{{ $totalInWords }} {{ $estimate->customer->currency->code }}</span>
        </div>

        <!-- Notes / Payment terms -->
        <div class="notes">
            @if ($notes)
                <div class="notes-label">
                    @lang('pdf_notes')
                </div>
                <div style="font-size: 10px; color: #444;">
                    {!! $notes !!}
                </div>
            @endif
        </div>
    </div>

    <!-- Absolute Footer Information -->
    @if ($estimate->company)
        <div class="footer-company-info">
            <div class="footer-company-name">{{ $estimate->company->name }}</div>
            {!! $company_address !!}
        </div>
    @endif

</body>
</html>
