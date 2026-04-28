<?php

namespace Tests\Feature\Filament\Resources\User;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class CreateUserTest extends FilamentTestCase
{
    public function test_create_page_loads(): void
    {
        [, $team] = $this->loginAsTenantMember();

        $this->get(UserResource::getUrl('create', ['tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_name_is_required(): void
    {
        $this->loginAsTenantMember();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => '',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_email_is_required(): void
    {
        $this->loginAsTenantMember();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'John',
                'email' => '',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'required']);
    }

    public function test_password_confirmation_must_match(): void
    {
        $this->loginAsTenantMember();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'John',
                'email' => 'john@example.com',
                'password' => 'password',
                'password_confirmation' => 'different',
            ])
            ->call('create')
            ->assertHasFormErrors(['password' => 'confirmed']);
    }

    public function test_created_user_password_is_hashed(): void
    {
        $this->loginAsTenantMember();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'johndoe@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_email_must_be_valid(): void
    {
        $this->loginAsTenantMember();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'John',
                'email' => 'not-an-email',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'email']);
    }
}
