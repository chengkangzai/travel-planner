<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(User::class)
                ->mutateDataUsing(function (array $data) {
                    $data['team_id'] = filament()->getTenant()->id;
                    $data['password'] = bcrypt(Str::random());

                    return $data;
                })
                ->schema([
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
                        ]),
                ]),

            Action::make('invite')
                ->label('Invite')
                ->icon('heroicon-o-user-plus')
                ->color('gray')
                ->action(function (array $data) {
                    $user = User::query()
                        ->withoutGlobalScopes()
                        ->findOrFail($data['user_id']);
                    $user->teams()->attach(filament()->getTenant()->id);
                })
                ->schema([
                    Select::make('user_id')
                        ->label('User')
                        ->searchable()
                        ->preload()
                        ->getSearchResultsUsing(fn (string $search): array => User::query()
                            ->withoutGlobalScopes()
                            ->whereDoesntHave('teams', fn ($query) => $query->where('team_id', filament()->getTenant()->id))
                            ->where('email', 'like', "%{$search}%")
                            ->limit(10)
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->getOptionLabelUsing(fn ($value): ?string => User::query()
                            ->withoutGlobalScopes()
                            ->find($value)?->name
                        ),
                ]),
        ];
    }
}
