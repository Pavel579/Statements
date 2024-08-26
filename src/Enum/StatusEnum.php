<?php

namespace App\Enum;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case SIGNED = 'signed';
    case UNDER_CONSIDERATION = 'under_consideration';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case DELETED = 'deleted';


}
