<?php

namespace Tests\Feature\Filament\Resources\User;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Tests\FilamentTestCase;

class ViewUserTest extends FilamentTestCase
{
    public function test_view_page_loads(): void
    {
        [$actor, $team] = $this->loginAsTenantMember();

        $this->get(UserResource::getUrl('view', ['record' => $actor, 'tenant' => $team]))
            ->assertSuccessful();
    }

    public function test_reset_password_dispatches_notification(): void
    {
        Notification::fake();

        [$actor, $team] = $this->loginAsTenantMember();

        $token = app('auth.password.broker')->createToken($actor);
        $notification = new ResetPassword($token);
        $actor->notify($notification);

        Notification::assertSentTo($actor, ResetPassword::class);
    }

    public function test_reset_password_not_sent_to_other_user_by_default(): void
    {
        Notification::fake();

        [$actor, $team] = $this->loginAsTenantMember();
        $otherUser = User::factory()->create();

        $token = app('auth.password.broker')->createToken($actor);
        $actor->notify(new ResetPassword($token));

        Notification::assertNotSentTo($otherUser, ResetPassword::class);
    }
}
