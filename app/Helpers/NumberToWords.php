<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen'
    ];

    private static $tens = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    public static function convert($number)
    {
        $number = floor($number);
        
        if ($number == 0) return 'Zero';
        
        return self::convertNumber($number);
    }

    private static function convertNumber($num)
    {
        if ($num < 20) {
            return self::$ones[$num];
        }
        
        if ($num < 100) {
            return self::$tens[intval($num/10)] . ($num%10 ? ' ' . self::$ones[$num%10] : '');
        }
        
        if ($num < 1000) {
            return self::$ones[intval($num/100)] . ' Hundred' . ($num%100 ? ' ' . self::convertNumber($num%100) : '');
        }
        
        if ($num < 100000) {
            return self::convertNumber(intval($num/1000)) . ' Thousand' . ($num%1000 ? ' ' . self::convertNumber($num%1000) : '');
        }
        
        if ($num < 10000000) {
            return self::convertNumber(intval($num/100000)) . ' Lakh' . ($num%100000 ? ' ' . self::convertNumber($num%100000) : '');
        }
        
        return self::convertNumber(intval($num/10000000)) . ' Crore' . ($num%10000000 ? ' ' . self::convertNumber($num%10000000) : '');
    }
}