<?php

namespace App\Enums;

enum NotificationPreference: string
{
    case ON = 'ON';
    case OFF = 'OFF';
    case SILENT = 'SILENT';
}
