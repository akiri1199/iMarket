<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceData extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_code',
        'company',
        'account_criteria',
        'aggency_individual',
        'fiscal',
        'fiscal_term',
        'from',
        'to',
        'account_item',
        'sales_amount',
        'operating_income',
        'odinary_profit',
        'net_income',
        'net_income_per_share',
        'after_profit',
        'net_assets',
        'total_assets',
        'total_assets_per_share',
        'operating_cash_flow',
        'investment_cash_flow',
        'financial_cash_flow',
        'update_date'
    ];
}