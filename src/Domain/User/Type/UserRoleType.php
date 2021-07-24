<?php

namespace App\Domain\User\Type;

/**
 * Type.
 */
final class UserRoleType
{
    /** @var int */
    public const ROLE_ADMIN = 1;

    /** @var int */
    public const ROLE_USER = 2;
    
    /** @var int */
    public const ROLE_STORE = 3;

    /** @var int */
    public const ROLE_MANAGER = 4;

    /** @var int */
    public const ROLE_STORE_MANAGER = 5;

    /** @var int */
    public const ROLE_SUPER_ADMIN = 6;

    /** @var int */
    public const ROLE_FINANCE = 7;
}
