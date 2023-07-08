<?php

namespace App\Enums;

enum UserLevelOfAuthenticatedEnum: string
{
    case PASSED = 'passed';
    case CURRENT = 'current';
    case FUTURE = 'future';
}
