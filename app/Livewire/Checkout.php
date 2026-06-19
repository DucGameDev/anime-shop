<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\PlaceOrderAction;
use App\Services\CartService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class Checkout extends Component
{
    public string $customerName  = '';
    public string $email         = '';
    public string $phone         = '';
    public string $address       = '';
    public string $note          = '';
    public string $paymentMethod = 'bank_transfer';
    public string $website       = '';  // honeypot
    public string $recaptchaToken = '';  // reCAPTCHA v3 token
    public bool   $isLoggedIn     = false;

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
        if (auth()->check()) {
            $user = auth()->user();
            $this->customerName = $user->name;
            $this->email        = $user->email;
            $this->phone        = $user->phone ?? '';
            $this->address      = $user->address ?? '';
            $this->isLoggedIn   = true;
        }
    }

    public function placeOrder(PlaceOrderAction $action): mixed
    {
        $this->validate();

        // Honeypot — bot điền vào field ẩn
        if ($this->website !== '') {
            Log::warning('Honeypot triggered', ['ip' => request()->ip()]);
            return redirect()->route('orders.show', 0); // silent fail
        }

        // reCAPTCHA v3 — bỏ qua khi chưa cấu hình (local dev)
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
                'customer_name'  => $this->customerName,
                'customer_email' => $this->email,
                'phone'          => $this->phone,
                'address'        => $this->address,
                'note'           => $this->note ?: null,
                'payment_method' => $this->paymentMethod,
            ]);

            // Lưu phone/address vào profile để tự điền lần sau
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

        return view('livewire.checkout', [
            'items' => $cartService->getItems(),
            'total' => $cartService->getTotal(),
        ]);
    }
}
