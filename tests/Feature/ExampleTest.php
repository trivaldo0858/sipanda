<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test halaman login Super Admin dapat diakses.
     */
    public function test_superadmin_login_page_returns_successful_response(): void
    {
        $response = $this->get('/superadmin/login');

        $response->assertStatus(200);
    }

    /**
     * Test endpoint API login mengembalikan validasi error
     * jika tidak ada input (bukan 404).
     */
    public function test_api_login_endpoint_exists(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        // Harusnya 422 (validasi) bukan 404 (tidak ditemukan)
        $response->assertStatus(422);
    }

    /**
     * Test endpoint login orang tua tersedia.
     */
    public function test_api_login_ortu_endpoint_exists(): void
    {
        $response = $this->postJson('/api/v1/auth/login-ortu', []);

        $response->assertStatus(422);
    }
}