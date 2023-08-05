<?php

namespace App\Enums;

enum TransactionStatus: string
{
    const AUTHORIZED = 1;
    const DECLINE = 2;
    const REFUNDED = 3;

    public static function get_statue($statusCode)
    {
        switch ($statusCode) {
            case self::AUTHORIZED:
                return 'Authorized';
            case self::DECLINE:
                return 'Decline';
            case self::REFUNDED:
                return 'Refunded';
            default:
                return 'Unknown';
        }
    }

    public static function get_status_code($keyword)
    {
        $keyword = strtolower($keyword);
        switch ($keyword) {
            case 'authorized':
                return self::AUTHORIZED;
            case 'decline':
                return self::DECLINE;
            case 'refunded':
                return  self::REFUNDED;
            default:
                return 'Unknown';
        }
    }
}
