<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\SourceData;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class SourceImport implements ToModel, WithStartRow
{
    public function model(array $row)
    {
        $temp = explode(",", $row[1]);
        $company = $temp[1];
        $company = str_replace('"', '', $company, );
        $company = str_replace(')', '', $company, );
        return new SourceData([
            'stock_code' => $row[0],
            'company' => $company,
            'account_criteria' => $row[2],
            'aggency_individual' => $row[3],
            'fiscal' => $row[4],
            'fiscal_term' => $row[5],
            'from' => Date::excelToDateTimeObject($row[6]),
            'to' => Date::excelToDateTimeObject($row[7]),
            'account_item' => $row[8],
            'sales_amount' => $row[9],
            'operating_income' => $row[10],
            'odinary_profit' => $row[11],
            'net_income' => $row[12],
            'net_income_per_share' => $row[13],
            'after_profit' => $row[14],
            'net_assets' => $row[15],
            'total_assets' => $row[16],
            'total_assets_per_share' => $row[17],
            'operating_cash_flow' => $row[18],
            'investment_cash_flow' => $row[19],
            'financial_cash_flow' => $row[20],
            'update_date' => Date::excelToDateTimeObject($row[21]),
        ]);

    }

    public function startRow(): int
    {
        return 2; // Skip the first row (header row)
    }


}