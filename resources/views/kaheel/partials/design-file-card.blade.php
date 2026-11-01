@php
    $href = url(app()->getLocale().'/orders/'.$order->id.'/files/'.$file->id);
    $size = $file->humanSize();
@endphp
<article class="design-file-card">
    <div class="design-file-top">
        <span class="design-file-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
        </span>
        <div class="design-file-copy">
            <p class="design-file-title">
                <span data-ar="ملف التصميم — النسخة {{ $version }}" data-en="Design file — version {{ $version }}">ملف التصميم — النسخة {{ $version }}</span>
            </p>
            <p class="design-file-meta">{{ $file->original_name }}@if ($size) · {{ $size }}@endif</p>
        </div>
    </div>
    <div class="design-file-actions">
        <a class="design-file-btn design-file-btn-preview" href="{{ $href }}" target="_blank" rel="noopener noreferrer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
            <span data-ar="معاينة" data-en="Preview">معاينة</span>
        </a>
        <a class="design-file-btn design-file-btn-download" href="{{ $href }}?download=1">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
            <span data-ar="تحميل" data-en="Download">تحميل</span>
        </a>
    </div>
</article>
