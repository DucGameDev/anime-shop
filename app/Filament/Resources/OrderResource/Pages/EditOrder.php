<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    public function getTitle(): string
    {
        return 'Chi tiết đơn hàng #' . str_pad((string) $this->record->id, 6, '0', STR_PAD_LEFT);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Xóa đơn hàng')
                ->color('gray')
                ->outlined()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Xóa đơn hàng?')
                ->modalDescription('Hành động này không thể hoàn tác. Tất cả sản phẩm trong đơn sẽ bị xóa.')
                ->modalSubmitActionLabel('Xóa'),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Lưu thay đổi');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()->label('Hủy');
    }

    protected function beforeSave(): void
    {
        $statusOrder = ['unpaid' => 0, 'pending' => 1, 'shipped' => 2, 'completed' => 3];

        $labels = [
            'unpaid'    => 'Chưa thanh toán',
            'pending'   => 'Chờ xử lý',
            'shipped'   => 'Đang giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];

        $original = $this->record->getOriginal('status');
        $new      = $this->data['status'] ?? $original;

        if (
            isset($statusOrder[$original], $statusOrder[$new])
            && $statusOrder[$new] < $statusOrder[$original]
        ) {
            Notification::make()
                ->title('Không thể hạ trạng thái')
                ->body('Đơn đang ở "' . ($labels[$original] ?? $original) . '", không thể quay về "' . ($labels[$new] ?? $new) . '".')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
