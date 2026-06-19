<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\PlaceOrderAction;
use App\Models\Address;
use App\Models\Voucher;
use App\Services\CartService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class Checkout extends Component
{
    public string  $customerName      = '';
    public string  $email             = '';
    public string  $phone             = '';
    public string  $address           = '';
    public string  $note              = '';
    public string  $paymentMethod     = 'bank_transfer';
    public string  $website           = '';         // honeypot
    public string  $recaptchaToken    = '';
    public bool    $isLoggedIn        = false;

    // Address book
    public ?int    $selectedAddressId = null;

    // Voucher
    public string  $voucherInput      = '';
    public string  $appliedVoucher    = '';
    public float   $discountAmount    = 0.0;
    public string  $voucherError      = '';

    protected array $rules = [
        'customerName' => 'required|string|max:255',
        'email'        => 'required|email|max:255',
        'phone'        => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
        'address'      => 'required|string|min:10|max:500',
    ];

    protected array $validationAttributes = [
        'customerName' => 'Tên khách hàng',
        'email'        => 'Email',
        'phone'        => 'Số điện thoại',
        'address'      => 'Địa chỉ',
    ];

    public function mount(): void
    {
        if (! auth()->check()) {
            return;
        }

        $user             = auth()->user();
        $this->customerName = $user->name;
        $this->email      = $user->email;
        $this->isLoggedIn = true;

        // Auto-fill from default address if exists
        $default = $user->addresses()->where('is_default', true)->first();
        if ($default) {
            $this->selectedAddressId = $default->id;
            $this->phone             = $default->phone;
            $this->address           = $default->address;
        } else {
            $this->phone   = $user->phone   ?? '';
            $this->address = $user->address ?? '';
        }
    }

    public function selectAddress(int $id): void
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $addr = $user->addresses()->find($id);

        if (! $addr) {
            return;
        }

        $this->selectedAddressId = $id;
        $this->phone             = $addr->phone;
        $this->address           = $addr->address;
    }

    public function applyVoucher(): void
    {
        $this->voucherError = '';
        $code = trim(strtoupper($this->voucherInput));

        if ($code === '') {
            $this->voucherError = 'Vui lòng nhập mã giảm giá.';
            return;
        }

        $voucher = Voucher::where('code', $code)->first();

        if (! $voucher || ! $voucher->isValid()) {
            $this->voucherError = 'Mã giảm giá không hợp lệ hoặc đã hết hạn.';
            return;
        }

        $cartService = app(CartService::class);
        $total       = $cartService->getTotal();

        $discount = $voucher->calculateDiscount((float) $total);
        if ($discount <= 0) {
            $this->voucherError = 'Đơn hàng chưa đạt giá trị tối thiểu để dùng mã này.';
            return;
        }

        $this->appliedVoucher = $code;
        $this->discountAmount = $discount;
        $this->voucherInput   = '';
    }

    public function removeVoucher(): void
    {
        $this->appliedVoucher = '';
        $this->discountAmount = 0.0;
        $this->voucherError   = '';
        $this->voucherInput   = '';
    }

    public function placeOrder(PlaceOrderAction $action): mixed
    {
        $this->validate();

        if ($this->website !== '') {
            Log::warning('Honeypot triggered', ['ip' => request()->ip()]);
            return redirect()->route('orders.show', 0);
        }

        $siteKey = config('services.recaptcha.site_key');
        if ($siteKey !== '') {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $this->recaptchaToken,
                'remoteip' => request()->ip(),
            ]);

            $score = $response->json('score', 0);
            if (! $response->json('success') || $score < config('services.recaptcha.threshold')) {
                Log::warning('reCAPTCHA failed', ['score' => $score, 'ip' => request()->ip()]);
                $this->addError('customerName', 'Xác minh bảo mật thất bại. Vui lòng thử lại.');
                return null;
            }
        }

        $cartService = app(CartService::class);
        if ($cartService->getItemCount() === 0) {
            return redirect()->route('cart.index');
        }

        try {
            $order = $action->execute([
                'customer_name'   => $this->customerName,
                'customer_email'  => $this->email,
                'phone'           => $this->phone,
                'address'         => $this->address,
                'note'            => $this->note ?: null,
                'payment_method'  => $this->paymentMethod,
                'voucher_code'    => $this->appliedVoucher ?: null,
                'discount_amount' => $this->discountAmount,
            ]);

            if (auth()->check()) {
                auth()->user()->update([
                    'phone'   => $this->phone,
                    'address' => $this->address,
                ]);
            }

            return redirect()->route('orders.show', $order);
        } catch (\Exception $e) {
            Log::error('Place order failed', ['error' => $e->getMessage()]);
            $this->addError('customerName', 'Có lỗi xảy ra khi đặt hàng, vui lòng thử lại.');
            return null;
        }
    }

    public function render(): View
    {
        $cartService = app(CartService::class);
        $total       = $cartService->getTotal();
        $finalTotal  = max(0.0, $total - $this->discountAmount);

        $addresses = [];
        if (auth()->check()) {
            $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();
        }

        return view('livewire.checkout', [
            'items'      => $cartService->getItems(),
            'total'      => $total,
            'finalTotal' => $finalTotal,
            'addresses'  => $addresses,
        ]);
    }
}
