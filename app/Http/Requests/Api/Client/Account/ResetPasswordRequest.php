<?php

namespace Pterodactyl\Http\Requests\Api\Client\Account;

use Pterodactyl\Http\Requests\Api\Client\ClientApiRequest;

class ResetPasswordRequest extends ClientApiRequest
{
    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        return true;
    }
}
