<div x-on:cart-updated.window="$wire.$refresh()">
    <a href="{{ route('cart.index') }}"
       class="relative flex items-center p-2 text-neutral-text hover:text-primary transition-colors"
       aria-label="Giỏ hàng{{ $count > 0 ? " ($count sản phẩm)" : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
        </svg>

        @if ($count > 0)
            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-secondary text-[10px] font-bold text-white leading-none">
                {{ $count > 99 ? '99+' : $count }}
            </span>
        @endif
    </a>
</div>
