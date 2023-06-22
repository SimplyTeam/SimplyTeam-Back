<?php

namespace App\Inputs;

class WorkspaceListInput {
    public const ARG_ID = 'nanoId';

    public const ARG_USER_ID = 'identity';

    public ?string $id;

    public ?string $userId;

    /**
     * @throws DataCannotBeEmpty
     * @throws NumberParseException
     * @throws PhoneNumberFormatIsInvalid
     */
    public function __construct(array $args)
    {

    }

    /**
     * @throws DataCannotBeEmpty
     * @throws NumberParseException
     * @throws PhoneNumberFormatIsInvalid
     */
    public static function fromRequest(array $args): self
    {
        return new self(
            [
                'nanoId' => $args[self::ARG_NANOID] ?? null,
                'identity' => $args[self::ARG_IDENTITY] ?? null,
                'email' => $args[self::ARG_EMAIL] ?? null,
                'phoneNumber' => $args[self::ARG_PHONE_NUMBER] ?? null,
                'intercomId' => $args[self::ARG_INTERCOM_ID] ?? null,
            ]
        );
    }

}
