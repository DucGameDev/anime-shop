<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class AccountProfile extends Component
{
    // Personal info
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    // Password change
    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    public string $activeTab = 'info'; // 'info' | 'password'

    public function mount(): void
    {
        $user        = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function saveInfo(): void
    {
        $user = auth()->user();
        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{10,11}$/'],
        ], [
            'name.required'  => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique'   => 'Email này đã được sử dụng.',
            'phone.regex'    => 'Số điện thoại không hợp lệ.',
        ]);

        $user->update([
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $this->dispatch('show-toast', message: 'Đã cập nhật thông tin.');
    }

    public function savePassword(): void
    {
        $this->validate([
            'currentPassword'         => ['required'],
            'newPassword'             => ['required', 'min:8', 'same:newPasswordConfirmation'],
            'newPasswordConfirmation' => ['required'],
        ], [
            'currentPassword.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'newPassword.required'     => 'Vui lòng nhập mật khẩu mới.',
            'newPassword.min'          => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'newPassword.same'         => 'Mật khẩu xác nhận không khớp.',
        ]);

        if (! Hash::check($this->currentPassword, auth()->user()->password)) {
            $this->addError('currentPassword', 'Mật khẩu hiện tại không đúng.');

            return;
        }

        auth()->user()->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword         = '';
        $this->newPassword             = '';
        $this->newPasswordConfirmation = '';

        $this->dispatch('show-toast', message: 'Đã đổi mật khẩu thành công.');
    }

    public function render(): View
    {
        return view('livewire.account-profile');
    }
}
