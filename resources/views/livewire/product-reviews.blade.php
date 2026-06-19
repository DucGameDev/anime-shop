<div class="space-y-8">

    {{-- ===== TỔNG QUAN ĐÁNH GIÁ ===== --}}
    <div class="flex items-center gap-6">
        <div class="text-center">
            <p class="text-5xl font-bold text-neutral-text">
                {{ $totalReviews > 0 ? number_format($averageRating, 1) : '—' }}
            </p>
            <div class="flex items-center justify-center gap-0.5 mt-1">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="h-5 w-5 {{ $i <= round($averageRating) ? 'text-warning' : 'text-gray-200' }}"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
            </div>
            <p class="text-sm text-neutral-muted mt-1">{{ $totalReviews }} đánh giá</p>
        </div>
    </div>

    {{-- ===== FORM ĐÁNH GIÁ ===== --}}
    @auth
        @if ($canReview && ! $hasReviewed)
            <div class="rounded-xl border border-gray-200 bg-white p-5">
                <h3 class="font-semibold text-neutral-text mb-4">Viết đánh giá</h3>

                <form wire:submit="submit" class="space-y-4"
                      x-data="{ rating: @entangle('rating') }">

                    {{-- Chọn sao --}}
                    <div>
                        <p class="text-sm font-medium text-neutral-text mb-2">Đánh giá</p>
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    @click="rating = {{ $i }}"
                                    class="transition-transform hover:scale-110"
                                    aria-label="{{ $i }} sao"
                                >
                                    <svg class="h-8 w-8 transition-colors"
                                         :class="rating >= {{ $i }} ? 'text-warning' : 'text-gray-200'"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <span class="ml-2 text-sm text-neutral-muted" x-text="['', 'Tệ', 'Không tốt', 'Bình thường', 'Tốt', 'Xuất sắc'][rating]"></span>
                        </div>
                        @error('rating') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nhận xét --}}
                    <div>
                        <label class="text-sm font-medium text-neutral-text" for="review-comment">
                            Nhận xét <span class="font-normal text-neutral-muted">(tùy chọn)</span>
                        </label>
                        <textarea
                            id="review-comment"
                            wire:model="comment"
                            rows="3"
                            placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."
                            class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-neutral-text placeholder-neutral-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors resize-none"
                        ></textarea>
                        @error('comment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2 text-sm font-medium text-white hover:bg-primary-dark transition-colors disabled:opacity-60"
                    >
                        <svg wire:loading class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Gửi đánh giá
                    </button>

                    {{-- Success toast --}}
                    <div
                        x-data="{ show: false }"
                        x-on:review-submitted.window="show = true; setTimeout(() => show = false, 3000)"
                        x-show="show"
                        x-transition
                        class="text-sm text-green-600 font-medium"
                    >
                        ✓ Đánh giá của bạn đã được lưu!
                    </div>
                </form>
            </div>
        @elseif (! $hasReviewed)
            <p class="text-sm text-neutral-muted italic">
                Chỉ khách hàng đã mua và hoàn thành đơn hàng mới có thể đánh giá sản phẩm này.
            </p>
        @endif
    @else
        <p class="text-sm text-neutral-muted italic">
            <a href="{{ route('login') }}" class="text-primary hover:underline">Đăng nhập</a>
            để đánh giá sản phẩm này.
        </p>
    @endauth

    {{-- ===== DANH SÁCH ĐÁNH GIÁ ===== --}}
    @if ($reviews->isEmpty())
        <p class="text-sm text-neutral-muted">Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
    @else
        <div class="space-y-4">
            @foreach ($reviews as $review)
                <div class="rounded-xl bg-white border border-gray-100 p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            {{-- Avatar --}}
                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-primary-light text-primary font-semibold text-sm">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-neutral-text">{{ $review->user->name }}</p>
                                <div class="flex items-center gap-0.5 mt-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'text-warning' : 'text-gray-200' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span class="text-xs text-neutral-muted flex-shrink-0">
                            {{ $review->created_at->diffForHumans() }}
                        </span>
                    </div>

                    @if ($review->comment)
                        <p class="mt-3 text-sm text-neutral-text leading-relaxed">
                            {{ $review->comment }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
