<?php

namespace Tests\Feature\Domain\User;

use Src\Domain\Settings\Data\ProfileUpdateData;
use Src\Domain\User\Actions\UpdateUserProfileAction;
use Src\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserProfileActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_user_profile(): void
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $profileData = new ProfileUpdateData(
            name: 'New Name',
            email: 'new@example.com'
        );

        // Act
        $action = new UpdateUserProfileAction();
        $updatedUser = $action($user, $profileData);

        // Assert
        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertEquals('new@example.com', $updatedUser->email);
        $this->assertNull($updatedUser->email_verified_at);
    }

    public function test_email_verified_at_is_reset_when_email_changes(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'email_verified_at' => now(),
        ]);

        $profileData = new ProfileUpdateData(
            name: $user->name,
            email: 'new@example.com'
        );

        // Act
        $action = new UpdateUserProfileAction();
        $updatedUser = $action($user, $profileData);

        // Assert
        $this->assertNull($updatedUser->email_verified_at);
    }
} 