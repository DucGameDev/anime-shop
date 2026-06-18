<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Actions\PlaceOrderAction;
use App\Services\CartService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Component;

class Checkout extends Component
{
    public string $customerName = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';

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

    public function placeOrder(PlaceOrderAction $action): mixed
    {
        $this->validate();

        $cartService = app(CartService::class);
        if ($cartService->getItemCount() === 0) {
            return redirect()->route('cart.index');
        }

        $key = 'checkout:' . md5($this->email . '|' . request()->ip());
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('customerName', "Bạn đặt hàng quá nhanh. Vui lòng thử lại sau {$seconds} giây.");
            return null;
        }
        RateLimiter::hit($key, 300);

        try {
            $order = $action->execute([
                'customer_name'  => $this->customerName,
                'customer_email' => $this->email,
                'phone'          => $this->phone,
                'address'        => $this->address,
            ]);

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
