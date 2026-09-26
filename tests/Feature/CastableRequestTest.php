<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Tests\Fixtures\CastableRequest;
use Lionix\CastableRequest\Tests\Fixtures\PlainRequest;
use Lionix\CastableRequest\Tests\Fixtures\Status;
use Lionix\CastableRequest\Tests\TestCase;

class CastableRequestTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        Route::post('/castable', fn (CastableRequest $request): array => [
            'published_at' => $request->input('published_at') instanceof Carbon
                ? $request->input('published_at')->toDateTimeString()
                : 'not casted',
            'status' => $request->input('status') === Status::Published,
            'count' => $request->input('count'),
            'tags' => $request->input('tags'),
            'items' => $request->input('items'),
            'active' => $request->input('active'),
            'untouched' => $request->input('untouched'),
        ]);

        Route::match(['get', 'post'], '/validated', fn (CastableRequest $request): array => [
            'published_at' => $request->validated('published_at') instanceof Carbon,
            'status' => $request->validated('status') === Status::Published,
            'safe_count' => $request->safe()->only('count'),
            'input_count' => $request->input('count'),
        ]);

        Route::post('/plain', fn (PlainRequest $request): array => $request->all());
    }

    public function test_form_request_input_is_casted_after_validation(): void
    {
        $this->postJson('/castable', [
            'published_at' => '2024-05-10 13:45:00',
            'status' => 'published',
            'count' => '7',
            'tags' => ['a', 'b'],
            'items' => [['price' => '5'], ['price' => '12.5']],
            'untouched' => '1',
        ])->assertOk()->assertExactJson([
            'published_at' => '2024-05-10 13:45:00',
            'status' => true,
            'count' => 7,
            'tags' => [['a'], ['b']],
            'items' => [['price' => '5.00'], ['price' => '12.50']],
            'active' => null,
            'untouched' => '1',
        ]);
    }

    public function test_validation_runs_against_the_raw_input(): void
    {
        $this->postJson('/castable', ['count' => 7])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('count');
    }

    public function test_global_casts_are_applied_to_every_form_request(): void
    {
        $this->app->make(CastsRegistryInterface::class)->register('active', 'boolean');

        $this->postJson('/plain', ['active' => '1', 'other' => '1'])
            ->assertOk()
            ->assertExactJson(['active' => true, 'other' => '1']);

        $this->postJson('/castable', ['active' => '0'])
            ->assertOk()
            ->assertJsonPath('active', false);
    }

    public function test_request_casts_do_not_leak_into_other_requests(): void
    {
        $this->postJson('/castable', ['count' => '1'])->assertOk();

        $this->postJson('/plain', ['count' => '1', 'status' => 'draft'])
            ->assertOk()
            ->assertExactJson(['count' => '1', 'status' => 'draft']);

        $this->assertSame([], $this->app->make(CastsRegistryInterface::class)->all());
    }

    public function test_validated_and_safe_data_are_casted(): void
    {
        $this->postJson('/validated', [
            'published_at' => '2024-05-10',
            'status' => 'published',
            'count' => '3',
        ])->assertOk()->assertExactJson([
            'published_at' => true,
            'status' => true,
            'safe_count' => ['count' => 3],
            'input_count' => 3,
        ]);
    }

    public function test_query_string_input_is_casted(): void
    {
        $this->getJson('/validated?count=5&status=published')
            ->assertOk()
            ->assertJsonPath('input_count', 5)
            ->assertJsonPath('status', true);
    }

    public function test_form_encoded_input_is_casted(): void
    {
        $this->post('/validated', ['count' => '9', 'status' => 'published'], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('input_count', 9)
            ->assertJsonPath('status', true);
    }

    public function test_invalid_input_fails_validation_instead_of_casting(): void
    {
        $this->postJson('/castable', ['status' => 'unknown', 'published_at' => 'not a date'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status', 'published_at']);
    }
}
