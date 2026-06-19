<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Address;
use Illuminate\View\View;
use Livewire\Component;

class AccountAddresses extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $label = 'Nhà';

    public string $recipientName = '';

    public string $phone = '';

    public string $address = '';

    protected function rules(): array
    {
        return [
            'label'         => ['required', 'string', 'max:50'],
            'recipientName' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'regex:/^[0-9]{10,11}$/'],
            'address'       => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    protected array $messages = [
        'recipientName.required' => 'Vui lòng nhập tên người nhận.',
        'phone.required'         => 'Vui lòng nhập số điện thoại.',
        'phone.regex'            => 'Số điện thoại không hợp lệ.',
        'address.required'       => 'Vui lòng nhập địa chỉ.',
        'address.min'            => 'Địa chỉ phải có ít nhất 10 ký tự.',
    ];

    public function openCreate(): void
    {
        $this->reset(['editingId', 'label', 'recipientName', 'phone', 'address']);
        $this->label    = 'Nhà';
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        /** @var \App\Models\User $user */
        $user                = auth()->user();
        $address             = Address::where('user_id', $user->id)->findOrFail($id);
        $this->editingId     = $address->id;
        $this->label         = $address->label;
        $this->recipientName = $address->recipient_name;
        $this->phone         = $address->phone;
        $this->address       = $address->address;
        $this->showForm      = true;
    }

    public function save(): void
    {
        $this->validate();

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $data = [
            'label'          => $this->label,
            'recipient_name' => $this->recipientName,
            'phone'          => $this->phone,
            'address'        => $this->address,
        ];

        if ($this->editingId) {
            Address::where('user_id', $user->id)->findOrFail($this->editingId)->update($data);
        } else {
            $isFirst = $user->addresses()->count() === 0;
            $user->addresses()->create(array_merge($data, ['is_default' => $isFirst]));
        }

        $this->showForm = false;
        $this->dispatch('show-toast', message: 'Đã lưu địa chỉ.');
    }

    public function setDefault(int $id): void
    {
        /** @var \App\Models\User $user */
        $user   = auth()->user();
        $userId = (int) $user->id;
        Address::where('user_id', $userId)->update(['is_default' => false]);
        Address::where('user_id', $userId)->where('id', $id)->update(['is_default' => true]);
        $this->dispatch('show-toast', message: 'Đã đặt làm địa chỉ mặc định.');
    }

    public function delete(int $id): void
    {
        /** @var \App\Models\User $user */
        $user       = auth()->user();
        $address    = Address::where('user_id', $user->id)->findOrFail($id);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = $user->addresses()->first();
            $next?->update(['is_default' => true]);
        }

        $this->dispatch('show-toast', message: 'Đã xóa địa chỉ.');
    }

    public function cancel(): void
    {
        $this->showForm = false;
    }

    public function render(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return view('livewire.account-addresses', [
            'addresses' => $user->addresses()->get(),
        ]);
    }
}
