<?php

namespace App\Tests\Requests;

use App\Requests\CreateUserRequest;
use PHPUnit\Framework\TestCase;
use React\Http\Message\ServerRequest;
use Trunk\Validation\Exception\ValidationException;

class CreateUserRequestTest extends TestCase
{
    public function testValidDataPassesAndIsExposedThroughValidated(): void
    {
        $request = (new ServerRequest('POST', '/users'))->withParsedBody([
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ]);

        $formRequest = new CreateUserRequest($request);
        $formRequest->validate();

        $this->assertSame(
            ['name' => 'Alice', 'email' => 'alice@example.com'],
            $formRequest->validated()
        );
    }

    public function testMissingNameFailsValidation(): void
    {
        $request = (new ServerRequest('POST', '/users'))->withParsedBody([
            'email' => 'alice@example.com',
        ]);

        $formRequest = new CreateUserRequest($request);

        try {
            $formRequest->validate();
            $this->fail('Expected a ValidationException to be thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('name', $e->errors);
        }
    }

    public function testInvalidEmailFailsValidation(): void
    {
        $request = (new ServerRequest('POST', '/users'))->withParsedBody([
            'name' => 'Alice',
            'email' => 'not-an-email',
        ]);

        $formRequest = new CreateUserRequest($request);

        try {
            $formRequest->validate();
            $this->fail('Expected a ValidationException to be thrown');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('email', $e->errors);
        }
    }
}
