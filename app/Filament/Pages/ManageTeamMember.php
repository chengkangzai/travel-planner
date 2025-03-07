<?php

namespace App\Filament\Pages;

use App\Models\Pivot\UserTeam;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageTeamMember extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.manage-team-member';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserTeam::query()->where('team_id', filament()->getTenant()->id))
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('user.email'),
            ])
            ->actions([
                Action::make('Kick out')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn($record) => $record->user_id !== auth()->id())
                    ->action(fn(UserTeam $record) => $record->delete())
            ]);
    }

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
