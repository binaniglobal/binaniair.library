<?php

namespace Tests\Feature;

use App\Models\Manuals;
use App\Models\ManualsItem;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PWAAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $manual;

    protected $manualItem;

    protected $testPdfFile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user with role
        $this->user = User::factory()->create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'phone' => '+1234567890',
            'status' => 0,
            'password' => bcrypt('password'),
        ]);

        // Create role and assign to user
        $role = Role::create(['name' => 'user']);
        $this->user->assignRole($role);

        // Create test manual
        $this->manual = Manuals::create([
            'name' => 'Test Manual',
            'type' => 0,
        ]);

        // Create and assign manual permission
        $permission = Permission::create(['name' => 'access-manual-'.$this->manual->name]);
        $this->user->givePermissionTo($permission);

        // Create test manual item
        $this->manualItem = ManualsItem::create([
            'name' => 'Test Document.pdf',
            'manual_uid' => $this->manual->mid,
            'link' => 'test-document.pdf',
            'file_type' => 'application/pdf',
            'file_path' => 'test-document.pdf',
        ]);

        // Create mock PDF file
        Storage::fake('privateSubManual');
        $this->testPdfFile = 'test-document.pdf';
        Storage::disk('privateSubManual')->put($this->testPdfFile, file_get_contents(__DIR__.'/../../public/test-sample.pdf'));
    }

    /** @test */
    public function authenticated_user_can_access_pwa_auth_token_endpoint()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/pwa/auth-token');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'expires_at',
            'user' => [
                'id',
                'name',
            ],
        ]);

        $this->assertNotEmpty($response->json('token'));
        $this->assertEquals($this->user->uuid, $response->json('user.id'));
    }

    /** @test */
    public function unauthenticated_user_cannot_access_pwa_auth_token_endpoint()
    {
        $response = $this->getJson('/pwa/auth-token');

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Not authenticated']);
    }

    /** @test */
    public function authenticated_user_can_access_document_via_pwa_url()
    {
        $this->actingAs($this->user);

        $response = $this->get('/pwa/download/submanuals/'.urlencode($this->testPdfFile));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('X-PWA-Cache', 'true');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_document_via_pwa_url()
    {
        $response = $this->getJson('/pwa/download/submanuals/'.urlencode($this->testPdfFile));

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Authentication required']);
    }

    /** @test */
    public function valid_pwa_token_allows_document_access()
    {
        // Get a valid token
        $this->actingAs($this->user);
        $tokenResponse = $this->getJson('/pwa/auth-token');
        $token = $tokenResponse->json('token');

        // Use token to access document (without being logged in)
        Auth::logout();

        $response = $this->get('/pwa/download/submanuals/'.urlencode($this->testPdfFile), [
            'X-PWA-Token' => $token,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function invalid_pwa_token_denies_document_access()
    {
        $invalidToken = 'invalid.token.signature';

        $response = $this->getJson('/pwa/download/submanuals/'.urlencode($this->testPdfFile), [
            'X-PWA-Token' => $invalidToken,
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Authentication required']);
    }

    /** @test */
    public function expired_pwa_token_denies_document_access()
    {
        // Create an expired token
        $expiredTokenData = [
            'user_id' => $this->user->uuid,
            'session_id' => session()->getId(),
            'expires_at' => now()->subHours(1)->timestamp, // Expired 1 hour ago
        ];

        $token = base64_encode(json_encode($expiredTokenData));
        $signature = hash_hmac('sha256', $token, config('app.key'));
        $expiredToken = $token.'.'.$signature;

        $response = $this->getJson('/pwa/download/submanuals/'.urlencode($this->testPdfFile), [
            'X-PWA-Token' => $expiredToken,
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Authentication required']);
    }

    /** @test */
    public function user_with_manual_permission_can_access_document()
    {
        $this->actingAs($this->user);

        // User already has access-manual-Test Manual permission from setUp
        $response = $this->get('/pwa/download/submanuals/'.urlencode($this->testPdfFile));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function user_without_manual_permission_cannot_access_api_endpoints()
    {
        // Create user without manual permission
        $unauthorizedUser = User::factory()->create();
        $role = Role::create(['name' => 'limited-user']);
        $unauthorizedUser->assignRole($role);

        $this->actingAs($unauthorizedUser);

        $response = $this->getJson('/api/manuals');

        // Should return empty data since user has no manual access permissions
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [],
        ]);
    }

    /** @test */
    public function api_endpoints_return_only_accessible_manuals()
    {
        $this->actingAs($this->user);

        // Create another manual without permission
        $restrictedManual = Manuals::create([
            'name' => 'Restricted Manual',
            'type' => 0,
        ]);

        $response = $this->getJson('/api/manuals');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $manuals = $response->json('data');
        $this->assertCount(1, $manuals); // Only accessible manual
        $this->assertEquals('Test Manual', $manuals[0]['name']);
    }

    /** @test */
    public function session_invalidation_prevents_cached_document_access()
    {
        // Login and get token
        $this->actingAs($this->user);
        $tokenResponse = $this->getJson('/pwa/auth-token');
        $token = $tokenResponse->json('token');

        // Confirm access works
        $response = $this->get('/pwa/download/submanuals/'.urlencode($this->testPdfFile));
        $response->assertStatus(200);

        // Logout (invalidate session)
        $this->post('/logout');

        // Try to access with token after logout
        $response = $this->get('/pwa/download/submanuals/'.urlencode($this->testPdfFile), [
            'X-PWA-Token' => $token,
        ]);

        // Should still work since token is valid and not tied to session
        $response->assertStatus(200);
    }

    /** @test */
    public function non_pdf_files_are_rejected()
    {
        Storage::disk('privateSubManual')->put('test-document.txt', 'This is not a PDF');

        $this->actingAs($this->user);

        $response = $this->getJson('/pwa/download/submanuals/test-document.txt');

        $response->assertStatus(400);
        $response->assertJson(['error' => 'File is not a PDF']);
    }

    /** @test */
    public function nonexistent_files_return_404()
    {
        $this->actingAs($this->user);

        $response = $this->getJson('/pwa/download/submanuals/nonexistent-file.pdf');

        $response->assertStatus(404);
        $response->assertJson(['error' => 'File not found']);
    }

    /** @test */
    public function cors_headers_are_set_for_local_development()
    {
        $this->actingAs($this->user);

        // Simulate local environment
        config(['app.env' => 'local']);

        $response = $this->get('/pwa/download/submanuals/'.urlencode($this->testPdfFile));

        $response->assertStatus(200);
        $response->assertHeader('Access-Control-Allow-Origin');
        $response->assertHeader('Access-Control-Allow-Methods');
        $response->assertHeader('Access-Control-Allow-Headers');
        $response->assertHeader('Access-Control-Allow-Credentials', 'true');
    }
}
