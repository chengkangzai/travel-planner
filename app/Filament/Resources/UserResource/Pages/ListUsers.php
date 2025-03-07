<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->model(User::class)
                ->mutateFormDataUsing(function (array $data) {
                    $data['team_id'] = filament()->getTenant()->id;
                    $data['password'] = bcrypt(\Str::random());
                    return $data;
                })
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->unique('users')
                        ->required()
                        ->maxLength(255)
                        ->email()
                        ->validationMessages([
                            'unique' => 'The member already have an account, invite them instead.',
                        ])
                ]),

            \Filament\Actions\Action::make('invite')
                ->label('Invite')
                ->icon('heroicon-o-user-plus')
                ->color('gray')
                ->action(function (array $data) {
                    $user = User::query()->findOrFail($data['user_id']);
                    $user->teams()->attach(filament()->getTenant()->id);
                })
                ->form([
                    Select::make('user_id')
                        ->label('User')
                        ->searchable()
                        ->getSearchResultsUsing(fn(string $search): array => User::query()
                            ->whereDoesntHave('teams', fn($query) => $query->where('team_id', filament()->getTenant()->id))
                            ->where('email', 'like', "%{$search}%")
                            ->limit(10)
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                ])
        ];
    }
}
