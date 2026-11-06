@props([
    'value' => 0,
    'prefix' => '',
    'suffix' => '',
    'duration' => 680,
    'class' => '',
])

@php
    $numValue = (float) $value;
    $formattedInitial = $prefix . number_format($numValue, 0, ',', '.') . $suffix;
@endphp

<span 
    wire:key="anim-{{ md5($prefix . $numValue . $suffix) }}"
    x-data="{
        target: {{ $numValue }},
        prefix: @js($prefix),
        suffix: @js($suffix),
        duration: {{ $duration }},
        display: @js($formattedInitial),
        lastRounded: -1,
        formatNum(n) {
            window.__pkdiNumFmt = window.__pkdiNumFmt || new Intl.NumberFormat('id-ID');
            return this.prefix + window.__pkdiNumFmt.format(n) + this.suffix;
        },
        startAnimation() {
            if (this.target === 0) {
                this.display = this.formatNum(0);
                return;
            }
            const startTime = performance.now();
            const step = (now) => {
                const elapsed = Math.max(0, now - startTime);
                const progress = Math.min(elapsed / this.duration, 1);
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                const rounded = Math.round(this.target * easeOutQuart);
                if (rounded !== this.lastRounded) {
                    this.lastRounded = rounded;
                    this.display = this.formatNum(rounded);
                }
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    this.display = this.formatNum(Math.round(this.target));
                }
            };
            requestAnimationFrame(step);
        }
    }"
    x-init="startAnimation()"
    x-text="display"
    class="tabular-nums inline-block {{ $class }}"
>{{ $formattedInitial }}</span>
