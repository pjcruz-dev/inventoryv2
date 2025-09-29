<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format currency in Philippine Peso
     */
    public static function format($amount, $decimals = 2)
    {
        if (!is_numeric($amount)) {
            return '₱0.00';
        }
        
        return '₱' . number_format($amount, $decimals);
    }
    
    /**
     * Format currency with thousands separator
     */
    public static function formatWithSeparator($amount, $decimals = 2)
    {
        if (!is_numeric($amount)) {
            return '₱0.00';
        }
        
        return '₱' . number_format($amount, $decimals, '.', ',');
    }
    
    /**
     * Format currency for display in cards
     */
    public static function formatForCard($amount, $decimals = 0)
    {
        if (!is_numeric($amount)) {
            return '₱0';
        }
        
        if ($amount >= 1000000) {
            return '₱' . number_format($amount / 1000000, 1) . 'M';
        } elseif ($amount >= 1000) {
            return '₱' . number_format($amount / 1000, 1) . 'K';
        } else {
            return '₱' . number_format($amount, $decimals);
        }
    }
    
    /**
     * Format currency for tables
     */
    public static function formatForTable($amount, $decimals = 2)
    {
        if (!is_numeric($amount)) {
            return '₱0.00';
        }
        
        return '₱' . number_format($amount, $decimals, '.', ',');
    }
    
    /**
     * Format currency for export
     */
    public static function formatForExport($amount, $decimals = 2)
    {
        if (!is_numeric($amount)) {
            return '₱0.00';
        }
        
        return '₱' . number_format($amount, $decimals);
    }
    
    /**
     * Get currency symbol
     */
    public static function symbol()
    {
        return '₱';
    }
    
    /**
     * Get currency code
     */
    public static function code()
    {
        return 'PHP';
    }
    
    /**
     * Get currency name
     */
    public static function name()
    {
        return 'Philippine Peso';
    }
}
