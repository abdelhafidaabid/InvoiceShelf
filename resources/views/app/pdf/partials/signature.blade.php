@if($model->show_signature)
<div style="margin-top: 40px; text-align: right; padding-right: 20px; page-break-inside: avoid;">
    <div style="display: inline-block; text-align: center;">
        <div style="font-weight: bold; margin-bottom: 10px; font-size: 12px; color: {{ $secondaryColor ?? '#000' }}">
            {{ $model->getPdfLabel($type . '_pdf_signature_stamp_label', 'pdf_signature_stamp') }}
        </div>
        @if($stamp)
            <div style="margin-bottom: 5px;">
                <img src="{{ \App\Space\ImageUtils::toBase64Src($stamp) }}" style="max-height: 100px; max-width: 200px;">
            </div>
        @else
            <div style="margin-bottom: 50px;"></div>
        @endif
        <div style="border-bottom: 1px solid #ccc; width: 200px; margin: 0 auto;"></div>
    </div>
</div>
@endif
