<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $statusOrder = ['unpaid' => 0, 'pending' => 1, 'shipped' => 2, 'completed' => 3];

        $original = $this->record->getOriginal('status');
        $new      = $this->data['status'] ?? $original;

        if (
            isset($statusOrder[$original], $statusOrder[$new])
            && $statusOrder[$new] < $statusOrder[$original]
        ) {
            Notification::make()
                ->title('Không thể hạ trạng thái')
                ->body('Đơn đã ở trạng thái "' . $original . '", không thể quay về "' . $new . '".')
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
