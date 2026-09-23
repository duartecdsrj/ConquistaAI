<?php
declare(strict_types=1);

namespace App\Application\Identity\DTO\Response;

use JsonSerializable;

/** Safe authentication payload intended for HTTP responses. */
final readonly class AuthenticationPublicResponseDto implements JsonSerializable
{
    public function __construct(
        public string $accessToken,
        public CurrentUserResponseDto $user,
    ) {
    }

    /** @return array{access_token: string, user: CurrentUserResponseDto} */
    public function jsonSerialize(): array
    {
        return ['access_token' => $this->accessToken, 'user' => $this->user];
    }
}
