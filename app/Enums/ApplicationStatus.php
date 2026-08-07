<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case UNDER_REVIEW = 'UNDER_REVIEW';
    case DOCUMENTS_REQUIRED = 'DOCUMENTS_REQUIRED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case E_FILING = 'E_FILING';            
    case OTP_VERIFICATION = 'OTP_VERIFICATION'; 
    case COMPLETED = 'COMPLETED';
    case REJECTED = 'REJECTED';
    case CANCELLED = 'CANCELLED';
}
