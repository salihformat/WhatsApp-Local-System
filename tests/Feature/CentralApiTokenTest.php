<?php

namespace Tests\Feature;

use App\Services\CentralApiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CentralApiTokenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:' . base64_encode(str_repeat('a', 32)),
            'app.central_api_url' => 'https://central.test/api',
            'app.company_id' => 1,
        ]);
    }

    public static function invalidTokens(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'spaces only' => ['   '],
            'newline only' => ["\n"],
            'quotes only' => ['""'],
            'inner space' => ['abc def'],
            'inner newline' => ["abc\ndef"],
            'control char' => ["abc\x07def"],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidTokens')]
    public function test_invalid_tokens_resolve_to_null($token): void
    {
        config(['app.central_api_token' => $token]);

        $this->assertNull(CentralApiService::resolveApiToken());
    }

    public static function validTokens(): array
    {
        return [
            'plain' => ['abc123', 'abc123'],
            'trailing newline' => ["abc123\n", 'abc123'],
            'surrounding spaces' => ['  abc123  ', 'abc123'],
            'surrounding quotes' => ['"abc123"', 'abc123'],
            'with symbols' => ['12|aB-_.xyz', '12|aB-_.xyz'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('validTokens')]
    public function test_valid_tokens_are_cleaned($token, $expected): void
    {
        config(['app.central_api_token' => $token]);

        $this->assertSame($expected, CentralApiService::resolveApiToken());
    }

    public function test_request_is_not_sent_when_token_is_empty(): void
    {
        Http::fake();
        config(['app.central_api_token' => '']);

        $result = (new CentralApiService())->makeApiRequest('POST', '/messages/send', ['a' => 1]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('CENTRAL_API_TOKEN', $result['error']);
        Http::assertNothingSent();
    }

    public function test_request_sends_well_formed_bearer_header(): void
    {
        Http::fake(['*' => Http::response(['success' => true, 'message_id' => 5], 200)]);
        config(['app.central_api_token' => "  tok123\n"]);

        (new CentralApiService())->makeApiRequest('POST', '/messages/send', ['a' => 1]);

        Http::assertSent(fn ($request) => $request->header('Authorization') === ['Bearer tok123']);
    }
}
