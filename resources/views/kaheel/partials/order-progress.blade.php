@php
    $step = $order->progressStep();
    $percent = $order->progressPercent();
@endphp
<div class="order-progress">
    <div class="order-progress-top">
        <span data-ar="التقدم" data-en="Progress">التقدم</span>
        <strong>{{ $percent }}%</strong>
    </div>
    <div class="order-track">
        <div class="order-track-fill" style="width: {{ $percent }}%"></div>
        <div class="order-track-dots">
            @for ($i = 0; $i < 8; $i++)
                <span class="order-dot {{ $i < $step ? 'is-on' : '' }}"></span>
            @endfor
        </div>
    </div>
</div>
