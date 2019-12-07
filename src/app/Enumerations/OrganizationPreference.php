<?php

namespace Bluewing\Enumerations;

use MyCLabs\Enum\Enum;

/**
 * Class OrganizationPreference
 * @package App\Enums
 *
 * @method static OrganizationPreference Boolean()
 * @method static OrganizationPreference File()
 * @method static OrganizationPreference String()
 * @method static OrganizationPreference Number()
 * @method static OrganizationPreference Json()
 */
final class OrganizationPreference extends Enum
{
    private const Boolean = 0;
    private const File = 1;
    private const String = 2;
    private const Number = 3;
    private const Json = 4;
}
