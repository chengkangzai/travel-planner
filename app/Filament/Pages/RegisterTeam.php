<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Database\Eloquent\Model;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $team = Team::create($data);

        $team->users()->attach(auth()->user());

        return $team;
    }
}
