<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    private array $validPayload = [
        'name'                   => 'John Doe',
        'identification_type_id' => 1,
        'identification_no'      => '123456789012',
        'email'                  => 'john@example.com',
        'password'               => 'password123',
        'password_confirmation'  => 'password123',
        'contact_no'             => '+60123456789',
        'occupation'             => 'Software Engineer',
        'industry_id'            => 1,
        'address_line_1'         => '123 Main Street',
        'city'                   => 'Kuala Lumpur',
        'postcode'               => '50000',
        'state_id'               => 14,
        'country_id'             => 'MY',
        'chk_agree'              => true,
        'introducer_code'        => 'CS001234',
    ];

    public function test_register_returns_success_response_from_upstream(): void
    {
        Http::fake([
            '*/auth/register' => Http::response([
                'success'                  => true,
                'email_validation_required'=> true,
                'message'                  => 'Your account has been created.',
            ], 200),
        ]);

        $response = $this->postJson('/api/register', $this->validPayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'email_validation_required' => true,
            ]);
    }

    public function test_register_returns_error_when_upstream_fails(): void
    {
        Http::fake([
            '*/auth/register' => Http::response([
                'success' => false,
                'message' => 'Email already exists.',
            ], 422),
        ]);

        $response = $this->postJson('/api/register', $this->validPayload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Email already exists.',
            ]);
    }

    public function test_register_returns_502_on_connection_failure(): void
    {
        Http::fake([
            '*/auth/register' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
            },
        ]);

        $response = $this->postJson('/api/register', $this->validPayload);

        $response->assertStatus(502)
            ->assertJson([
                'success' => false,
                'message' => 'Registration service temporarily unavailable.',
            ]);
    }

    public function test_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'identification_type_id',
                'identification_no',
                'email',
                'password',
                'password_confirmation',
                'contact_no',
                'occupation',
                'industry_id',
                'address_line_1',
                'city',
                'postcode',
                'state_id',
                'country_id',
                'chk_agree',
            ]);
    }

    public function test_register_allows_optional_introducer_code(): void
    {
        Http::fake([
            '*/auth/register' => Http::response([
                'success' => true,
                'message' => 'Your account has been created.',
            ], 200),
        ]);

        $payload = $this->validPayload;
        unset($payload['introducer_code']);

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
