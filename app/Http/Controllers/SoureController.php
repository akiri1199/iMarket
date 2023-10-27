<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SourceData;
use App\Imports\SourceImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SoureController extends Controller
{
    public function index()
    {
        return view('source.index', [
            'update_date' => SourceData::select("update_date")->orderBy('update_date', 'DESC')->distinct()->get(),
        ]);
    }

    public function import(Request $request)
    {

        Excel::import(new SourceImport, $request->file('file')->store('temp'));
        return redirect('/')->with('success', 'All good!');
    }
    private function calculateRate($currentValue, $lastValue)
    {
        return ($lastValue != 0) ? round(($currentValue - $lastValue) / $lastValue * 100, 1) : "";
    }
    public function load(Request $request)
    {
        $update_date = $request->input('update_date');

        if ($update_date == "" || $update_date == "-1" || $update_date == null) {
            $tableData = DB::select('SELECT *
                                            FROM source_data
                                            WHERE SUBSTRING(fiscal_term, 2, 1) = (SELECT MAX(SUBSTRING(fiscal_term, 2, 1))
                                                                                                                FROM source_data
                                                                                                                WHERE fiscal_term != "通期") 
                                            AND (fiscal_term) IN (
                                                SELECT MAX(fiscal_term)
                                                FROM source_data
                                                GROUP BY stock_code
                                            )');
            $expectData = DB::select('SELECT *
                                        FROM source_data
                                        WHERE fiscal_term = "通期" and aggency_individual!="個別"
                                        AND (fiscal_term) IN (
                                            SELECT MAX(fiscal_term)
                                            FROM source_data
                                            GROUP BY stock_code
                                        )');
            $combinedData = [];
            $rowCount = count($tableData);
            $expectCount = count($expectData);
            $pattern = "";
            for ($i = 0; $i < $rowCount - 2; $i++) {
                if ($pattern != $tableData[$i]->stock_code) {

                    if ($tableData[$i]->stock_code == $tableData[$i + 1]->stock_code) { //normal

                        if ($tableData[$i]->stock_code == $tableData[$i + 2]->stock_code) {
                            $tableData[$i + 1]->sales_rate = $this->calculateRate($tableData[$i + 1]->sales_amount, $tableData[$i + 2]->sales_amount);
                            $tableData[$i + 1]->operating_rate = $this->calculateRate($tableData[$i + 1]->operating_income, $tableData[$i + 2]->operating_income);
                            $tableData[$i + 1]->odinary_rate = $this->calculateRate($tableData[$i + 1]->odinary_profit, $tableData[$i + 2]->odinary_profit);
                            $tableData[$i + 1]->net_rate = $this->calculateRate($tableData[$i + 1]->net_income, $tableData[$i + 2]->net_income);
                        } else {
                            $tableData[$i + 1]->sales_rate = "";
                            $tableData[$i + 1]->operating_rate = "";
                            $tableData[$i + 1]->odinary_rate = "";
                            $tableData[$i + 1]->net_rate = "";
                        }
                        $combinedData[] = $tableData[$i + 1];

                        $tableData[$i]->sales_rate = $this->calculateRate($tableData[$i]->sales_amount, $tableData[$i + 1]->sales_amount);
                        $tableData[$i]->operating_rate = $this->calculateRate($tableData[$i]->operating_income, $tableData[$i + 1]->operating_income);
                        $tableData[$i]->odinary_rate = $this->calculateRate($tableData[$i]->odinary_profit, $tableData[$i + 1]->odinary_profit);
                        $tableData[$i]->net_rate = $this->calculateRate($tableData[$i]->net_income, $tableData[$i + 1]->net_income);

                        $combinedData[] = $tableData[$i];
                        $isExpect = false;
                        for ($j = 0; $j < $expectCount - 1; $j++) {
                            if ($tableData[$i]->stock_code == $expectData[$j]->stock_code && $tableData[$i]->fiscal == $expectData[$j]->fiscal) {
                                if ($expectData[$j]->stock_code == $expectData[$j + 1]->stock_code) {
                                    $expectData[$j]->sales_rate = $this->calculateRate($expectData[$j]->sales_amount, $expectData[$j + 1]->sales_amount);
                                    $expectData[$j]->operating_rate = $this->calculateRate($expectData[$j]->operating_income, $expectData[$j + 1]->operating_income);
                                    $expectData[$j]->odinary_rate = $this->calculateRate($expectData[$j]->odinary_profit, $expectData[$j + 1]->odinary_profit);
                                    $expectData[$j]->net_rate = $this->calculateRate($expectData[$j]->net_income, $expectData[$j + 1]->net_income);
                                } else {
                                    $expectData[$j]->sales_rate = "";
                                    $expectData[$j]->operating_rate = "";
                                    $expectData[$j]->odinary_rate = "";
                                    $expectData[$j]->net_rate = "";
                                }
                                $combinedData[] = $expectData[$j];
                                $isExpect = true;
                            }
                        }
                        if (!$isExpect) {
                            $combinedData[] = $this->insertEmptyRow($tableData[$i]->stock_code, $tableData[$i]->company, $tableData[$i]->fiscal, $tableData[$i]->fiscal_term);
                        }
                        $pattern = $tableData[$i]->stock_code;
                    } else {

                        $combinedData[] = $this->insertEmptyRow($tableData[$i]->stock_code, $tableData[$i]->company, $tableData[$i]->fiscal, $tableData[$i]->fiscal_term);
                        $tableData[$i]->sales_rate = "";
                        $tableData[$i]->operating_rate = "";
                        $tableData[$i]->odinary_rate = "";
                        $tableData[$i]->net_rate = "";
                        $combinedData[] = $tableData[$i];
                        $isExpect = false;
                        for ($j = 0; $j < $expectCount; $j++) {
                            if ($tableData[$i]->stock_code == $expectData[$j]->stock_code && $tableData[$i]->fiscal == $expectData[$j]->fiscal) {
                                $expectData[$j]->sales_rate = "";
                                $expectData[$j]->operating_rate = "";
                                $expectData[$j]->odinary_rate = "";
                                $expectData[$j]->net_rate = "";
                                $combinedData[] = $expectData[$j];
                                $isExpect = true;
                            }
                        }
                        if (!$isExpect) {
                            $combinedData[] = $this->insertEmptyRow($tableData[$i]->stock_code, $tableData[$i]->company, $tableData[$i]->fiscal, $tableData[$i]->fiscal_term);
                        }
                        $pattern = $tableData[$i]->stock_code;
                    }
                }
            }
        } else {
            $tableData = DB::select('SELECT *
                                            FROM source_data
                                            WHERE SUBSTRING(fiscal_term, 2, 1) = (SELECT MAX(SUBSTRING(fiscal_term, 2, 1))
                                                                                                                FROM source_data
                                                                                                                WHERE fiscal_term != "通期") 
                                            AND (fiscal_term) IN (
                                                SELECT MAX(fiscal_term)
                                                FROM source_data
                                                GROUP BY stock_code
                                            )
                                            AND update_date<=?', [$update_date]);
            $expectData = DB::select('SELECT *
                                        FROM source_data
                                        WHERE fiscal_term = "通期" and aggency_individual!="個別"
                                        AND (fiscal_term) IN (
                                            SELECT MAX(fiscal_term)
                                            FROM source_data
                                            GROUP BY stock_code
                                        )AND update_date<=?', [$update_date]);
            $combinedData = [];
            $rowCount = count($tableData);
            $expectCount = count($expectData);
            $pattern = "";
            for ($i = 0; $i < $rowCount - 2; $i++) {
                if ($pattern != $tableData[$i]->stock_code) {

                    if ($tableData[$i]->stock_code == $tableData[$i + 1]->stock_code) { //normal

                        if ($tableData[$i]->stock_code == $tableData[$i + 2]->stock_code) {
                            $tableData[$i + 1]->sales_rate = $this->calculateRate($tableData[$i + 1]->sales_amount, $tableData[$i + 2]->sales_amount);
                            $tableData[$i + 1]->operating_rate = $this->calculateRate($tableData[$i + 1]->operating_income, $tableData[$i + 2]->operating_income);
                            $tableData[$i + 1]->odinary_rate = $this->calculateRate($tableData[$i + 1]->odinary_profit, $tableData[$i + 2]->odinary_profit);
                            $tableData[$i + 1]->net_rate = $this->calculateRate($tableData[$i + 1]->net_income, $tableData[$i + 2]->net_income);
                        } else {
                            $tableData[$i + 1]->sales_rate = "";
                            $tableData[$i + 1]->operating_rate = "";
                            $tableData[$i + 1]->odinary_rate = "";
                            $tableData[$i + 1]->net_rate = "";
                        }
                        $combinedData[] = $tableData[$i + 1];

                        $tableData[$i]->sales_rate = $this->calculateRate($tableData[$i]->sales_amount, $tableData[$i + 1]->sales_amount);
                        $tableData[$i]->operating_rate = $this->calculateRate($tableData[$i]->operating_income, $tableData[$i + 1]->operating_income);
                        $tableData[$i]->odinary_rate = $this->calculateRate($tableData[$i]->odinary_profit, $tableData[$i + 1]->odinary_profit);
                        $tableData[$i]->net_rate = $this->calculateRate($tableData[$i]->net_income, $tableData[$i + 1]->net_income);

                        $combinedData[] = $tableData[$i];
                        $isExpect = false;
                        for ($j = 0; $j < $expectCount - 1; $j++) {
                            if ($tableData[$i]->stock_code == $expectData[$j]->stock_code && $tableData[$i]->fiscal == $expectData[$j]->fiscal) {
                                if ($expectData[$j]->stock_code == $expectData[$j + 1]->stock_code) {
                                    $expectData[$j]->sales_rate = $this->calculateRate($expectData[$j]->sales_amount, $expectData[$j + 1]->sales_amount);
                                    $expectData[$j]->operating_rate = $this->calculateRate($expectData[$j]->operating_income, $expectData[$j + 1]->operating_income);
                                    $expectData[$j]->odinary_rate = $this->calculateRate($expectData[$j]->odinary_profit, $expectData[$j + 1]->odinary_profit);
                                    $expectData[$j]->net_rate = $this->calculateRate($expectData[$j]->net_income, $expectData[$j + 1]->net_income);
                                } else {
                                    $expectData[$j]->sales_rate = "";
                                    $expectData[$j]->operating_rate = "";
                                    $expectData[$j]->odinary_rate = "";
                                    $expectData[$j]->net_rate = "";
                                }
                                $combinedData[] = $expectData[$j];
                                $isExpect = true;
                            }
                        }
                        if (!$isExpect) {
                            $combinedData[] = $this->insertEmptyRow($tableData[$i]->stock_code, $tableData[$i]->company, $tableData[$i]->fiscal, $tableData[$i]->fiscal_term);
                        }
                        $pattern = $tableData[$i]->stock_code;
                    } else {
                        // $combinedData[] = $this->insertEmptyRow($tableData[$i]->stock_code, $tableData[$i]->company, $tableData[$i]->fiscal, $tableData[$i]->fiscal_term);
                        $tableData[$i]->sales_rate = "";
                        $tableData[$i]->operating_rate = "";
                        $tableData[$i]->odinary_rate = "";
                        $tableData[$i]->net_rate = "";
                        $combinedData[] = $tableData[$i];
                        $isExpect = false;
                        for ($j = 0; $j < $expectCount; $j++) {
                            if ($tableData[$i]->stock_code == $expectData[$j]->stock_code && $tableData[$i]->fiscal == $expectData[$j]->fiscal) {
                                $expectData[$j]->sales_rate = "";
                                $expectData[$j]->operating_rate = "";
                                $expectData[$j]->odinary_rate = "";
                                $expectData[$j]->net_rate = "";
                                $combinedData[] = $expectData[$j];
                                $isExpect = true;
                            }
                        }
                        if (!$isExpect) {
                            $combinedData[] = $this->insertEmptyRow($tableData[$i]->stock_code, $tableData[$i]->company, $tableData[$i]->fiscal, $tableData[$i]->fiscal_term);
                        }
                        $pattern = $tableData[$i]->stock_code;
                    }
                }
            }
        }




        return DataTables::of($combinedData)->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '<a href="/detail/' . $row->stock_code . '"  >' . $row->stock_code . '</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
        // dd($combinedData);


    }
    private function insertEmptyRow($stock_code, $company, $fiscal, $fiscal_term)
    {
        return (object) [
            "id" => "000",
            "update_date" => "",
            "company" => $company,
            "stock_code" => $stock_code,
            "fiscal" => $fiscal,
            "fiscal_term" => $fiscal_term,
            "sales_amount" => "",
            "operating_income" => "",
            "odinary_profit" => "",
            "net_income" => "",
            "net_income_per_share" => "",
            "sales_rate" => "",
            "operating_rate" => "",
            "odinary_rate" => "",
            "net_rate" => ""
        ];
    }




    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
    }

    function detail(Request $request, $id)
    {
        $main_info = SourceData::where('stock_code', $id)->orWhere('company', $id)->distinct()->get();
        $main_data = SourceData::where(
            function ($query) use ($id) {
                $query->where('stock_code', $id)
                    ->orWhere('company', $id);
            }
        )->where('aggency_individual', '連結')->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(fiscal, '年', 1), '年', -1), UNSIGNED INTEGER) ASC")
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(fiscal, '月', 1), '月', -1), UNSIGNED INTEGER) ASC")->get();

        if (count($main_data) > 0) {
            foreach ($main_data as $item) {
                $currentYearArr = explode('年', $item->fiscal);
                $currentYear = $currentYearArr[0] - 1;
                $currentMonth = substr($currentYearArr[1], 0, 1);
                foreach ($main_data as $sub_item) {
                    if (($sub_item->fiscal == ($currentYear . '年' . $currentMonth . '月期')) && ($item->fiscal_term == $sub_item->fiscal_term) && $item->id != $sub_item->id) {
                        $item->income_per_share = ($sub_item->operating_income != 0) ? round(($item->operating_income - $sub_item->operating_income) / $sub_item->operating_income * 100, 1) : 0;
                        $item->profit_per_share = ($sub_item->odinary_profit != 0) ? round(($item->odinary_profit - $sub_item->odinary_profit) / $sub_item->odinary_profit * 100, 1) : 0;
                        $item->diff_sales = ($sub_item->sales_amount != 0) ? round(($item->sales_amount - $sub_item->sales_amount) / $sub_item->sales_amount * 100, 1) : 0;
                        $item->diff_income = ($sub_item->operating_income != 0) ? round(($item->operating_income - $sub_item->operating_income) / $sub_item->operating_income * 100, 1) : 0;
                        $item->diff_profit = ($sub_item->odinary_profit != 0) ? round(($item->odinary_profit - $sub_item->odinary_profit) / $sub_item->odinary_profit * 100, 1) : 0;
                        $item->diff_net = ($sub_item->net_income != 0) ? round(($item->net_income - $sub_item->net_income) / $sub_item->net_income * 100, 1) : 0;
                    }
                }
            }
        }
        //////////

        $sub_data = SourceData::where(
            function ($query) use ($id) {
                $query->where('stock_code', $id)
                    ->orWhere('company', $id);
            }
        )->where('fiscal_term', '!=', '通期')->where('aggency_individual', '連結')->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(fiscal, '年', 1), '年', -1), UNSIGNED INTEGER) ASC")
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(fiscal, '月', 1), '月', -1), UNSIGNED INTEGER) ASC")->get();
        if (count($sub_data) > 0) {
            foreach ($sub_data as $item) {
                $currentFiscalTerm = $item->fiscal_term;
                $currentFiscalTermArray = explode('第', $currentFiscalTerm);
                $prevQ = substr($currentFiscalTermArray[1], 0, 1) - 1;

                $currentYearArr = explode('年', $item->fiscal);
                $currentYear = $currentYearArr[0];
                $currentMonth = substr($currentYearArr[1], 0, 1);

                if ($prevQ == 0) {
                    $prevQ = 3;
                    $currentYear = $currentYearArr[0] - 1;
                }

                foreach ($sub_data as $sub_item) {
                    if (($sub_item->fiscal_term == ('第' . $prevQ . '四半期')) && ($sub_item->fiscal == $currentYear . '年' . $currentMonth . '月期')) { //
                        $item->income_per_share = ($sub_item->operating_income != 0) ? round(($item->operating_income - $sub_item->operating_income) / $sub_item->operating_income * 100, 1) : 0;
                        $item->profit_per_share = ($sub_item->odinary_profit != 0) ? round(($item->odinary_profit - $sub_item->odinary_profit) / $sub_item->odinary_profit * 100, 1) : 0;
                        $item->diff_sales = ($sub_item->sales_amount != 0) ? round(($item->sales_amount - $sub_item->sales_amount) / $sub_item->sales_amount * 100, 1) : 0;
                        $item->diff_income = ($sub_item->operating_income != 0) ? round(($item->operating_income - $sub_item->operating_income) / $sub_item->operating_income * 100, 1) : 0;
                        $item->diff_profit = ($sub_item->odinary_profit != 0) ? round(($item->odinary_profit - $sub_item->odinary_profit) / $sub_item->odinary_profit * 100, 1) : 0;
                        $item->diff_net = ($sub_item->net_income != 0) ? round(($item->net_income - $sub_item->net_income) / $sub_item->net_income * 100, 1) : 0;
                    }
                }
            }

        }
        return view('source.detail', ['main_info' => $main_info, 'main_data' => $main_data, 'sub_data' => $sub_data]);
    }
}