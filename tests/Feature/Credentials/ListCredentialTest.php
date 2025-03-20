<?php

namespace Tests\Feature\Credentials;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListCredentialTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_fetch_a_single_credential(): void
    {
        $user = User::factory()->create();
        $credential = Credential::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('credentials.show', $credential));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $credential->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('credentials', 1);
    }

    #[Test]
    public function can_fetch_all_credential(): void
    {
        $user = User::factory()->create();
        Credential::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);



    }



    #[Test]
    public function cannot_fetch_credential_of_another_user()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $credential = Credential::factory()->create([
            'user_id' => $owner->id,
            'name' => 'GitHub',
            'username' => 'testuser',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($otherUser)->get(route('credentials.show', $credential));
        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Unauthorized',
        ]);
    }


    #[Test]
    public function can_create_a_credential(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('credentials.store'), [
            'name' => 'Twitter',
            'username' => 'newuser',
            'password' => 'secret123',
        ]);

        $response->assertStatus(201); // 201 Created
        $this->assertDatabaseHas('credentials', [
            'user_id' => $user->id,
            'name' => 'Twitter',
            'username' => 'newuser',
            'password' => 'secret123',
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_credential(): void
    {
        $response = $this->post(route('credentials.store'), [
            'name' => 'Twitter',
            'username' => 'newuser',
            'password' => 'mypassword123',
        ]);

        $response->assertStatus(401); // 401 Unauthorized
        $this->assertDatabaseMissing('credentials', [
            'name' => 'Twitter',
            'username' => 'newuser',
            'password' => 'mypassword123',
        ]);
    }

    #[Test]
    public function can_update_own_credential(): void
    {
        $user = User::factory()->create();
        $credential = Credential::factory()->create([
            'user_id' => $user->id,
            'name' => 'GitHub',
            'username' => 'testuser',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->put(route('credentials.update', $credential), [
            'name' => 'GitHub Updated',
            'username' => 'updateduser',
            'password' => 'newsecret456',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('credentials', [
            'id' => $credential->id,
            'name' => 'GitHub Updated',
            'username' => 'updateduser',
            'password' => 'newsecret456',
        ]);
    }

    #[Test]
    public function cannot_update_credential_of_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $credential = Credential::factory()->create([
            'user_id' => $owner->id,
            'name' => 'GitHub',
            'username' => 'testuser',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($otherUser)->put(route('credentials.update', $credential), [
            'name' => 'GitHub Updated',
            'username' => 'updateduser',
            'password' => 'newsecret456',
        ]);

        $response->assertStatus(403); // 403 Forbidden
        $response->assertJson(['error' => 'Unauthorized']);
        $this->assertDatabaseHas('credentials', [
            'id' => $credential->id,
            'name' => 'GitHub', // No se actualiza
            'username' => 'testuser',
            'password' => 'secret123',
        ]);
    }

    #[Test]
    public function can_delete_own_credential(): void
    {
        $user = User::factory()->create();
        $credential = Credential::factory()->create([
            'user_id' => $user->id,
            'name' => 'GitHub',
            'username' => 'testuser',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->delete(route('credentials.destroy', $credential));

        $response->assertStatus(204); // 204 No Content
        $this->assertDatabaseMissing('credentials', [
            'id' => $credential->id,
        ]);
    }


    #[Test]
    public function cannot_delete_credential_of_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $credential = Credential::factory()->create([
            'user_id' => $owner->id,
            'name' => 'GitHub',
            'username' => 'testuser',
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($otherUser)->delete(route('credentials.destroy', $credential));

        $response->assertStatus(403);
        $response->assertJson(['error' => 'Unauthorized']);
        $this->assertDatabaseHas('credentials', [
            'id' => $credential->id,
            'name' => 'GitHub',
            'username' => 'testuser',
            'password' => 'secret123',
        ]);
    }


}
