<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset_password')
                ->icon('heroicon-o-lock-closed')
                ->requiresConfirmation()
                ->label('Send Reset Password Email')
                ->action(function (User $record) {
                    $token = app('auth.password.broker')->createToken($record);
                    $notification = new ResetPassword($token);
                    $notification->url = Filament::getResetPasswordUrl($token, $record);
                    $record->notify($notification);
                })
        ];
    }
}
