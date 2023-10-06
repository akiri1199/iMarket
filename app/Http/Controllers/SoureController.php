<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SourceData;
use App\Imports\SourceImport;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use Illuminate\Support\Facades\DB;

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
    public function load(Request $request)
    {

        //$data = SourceData::where('stock_code', '1301')->orderBy('company', 'ASC')->get();
                $data = DB::select('SELECT current_year.id AS id,
	current_year.stock_code AS stock_code, 
	current_year.company AS company,	
	current_year.aggency_individual AS aggency_individual, 
	REPLACE(REPLACE(current_year.fiscal,"年","/"),"月期","") AS fiscal, 
	REPLACE(REPLACE(current_year.fiscal_term,"第",""),"四半期","Q") AS fiscal_term,
    current_year.net_income_per_share AS net_income_per_share,
	current_year.from AS "from",
	current_year.to AS "to",current_year.sales_amount,
	current_year.operating_income, 
	current_year.odinary_profit,
	current_year.net_income,
    (current_year.sales_amount - last_year.sales_amount) / last_year.sales_amount * 100 AS sales_rate,
    (current_year.operating_income - last_year.operating_income) / last_year.operating_income * 100 AS operating_rate,
    (current_year.odinary_profit - last_year.odinary_profit) / last_year.odinary_profit * 100 AS odinary_rate,
    (current_year.net_income - last_year.net_income) / last_year.net_income * 100 AS net_rate
FROM 
    (SELECT *
FROM source_data 
WHERE SUBSTRING(fiscal_term, 2, 1) = (SELECT MAX(SUBSTRING(fiscal_term, 2, 1))
                                                  FROM source_data
                                                  WHERE fiscal_term != "通期")      

        LIMIT 10) AS current_year  
  JOIN 
    source_data AS last_year 
     ON  last_year.stock_code=current_year.stock_code AND current_year.fiscal_term = last_year.fiscal_term AND SUBSTRING(current_year.fiscal, 1, 4) = SUBSTRING(last_year.fiscal, 1, 4) + 1 ORDER BY current_year.company and current_year.to');
        return DataTables::of($data)->toJson();
    }

    public function search(Request $request)
    {

        $keyword = $request->input('keyword');
        $data = SourceData::where('stock_code', 'LIKE', "%$keyword%")
            ->orWhere('company', 'LIKE', "%$keyword%")
            ->get();
        //         $data = DB::select('SELECT current_year.id AS id,
// 	current_year.stock_code AS stock_code, 
// 	current_year.company AS company,	
// 	current_year.aggency_individual AS aggency_individual, 
// 	REPLACE(REPLACE(current_year.fiscal,"年","/"),"月期","") AS fiscal, 
// 	REPLACE(REPLACE(current_year.fiscal_term,"第",""),"四半期","Q") AS fiscal_term,
//     current_year.net_income_per_share AS net_income_per_share,
// 	current_year.from AS "from",
// 	current_year.to AS "to",current_year.sales_amount,
// 	current_year.operating_income, 
// 	current_year.odinary_profit,
// 	current_year.net_income,
//     (current_year.sales_amount - last_year.sales_amount) / last_year.sales_amount * 100 AS sales_rate,
//     (current_year.operating_income - last_year.operating_income) / last_year.operating_income * 100 AS operating_rate,
//     (current_year.odinary_profit - last_year.odinary_profit) / last_year.odinary_profit * 100 AS odinary_rate,
//     (current_year.net_income - last_year.net_income) / last_year.net_income * 100 AS net_rate
// FROM 
//     (SELECT *
// FROM source_data 
// WHERE SUBSTRING(fiscal_term, 2, 1) = (SELECT MAX(SUBSTRING(fiscal_term, 2, 1))
//                                                   FROM source_data
//                                                   WHERE fiscal_term != "通期")      

        // LIMIT 40) AS current_year  
//   JOIN 
//     source_data AS last_year 
//      ON  last_year.stock_code=current_year.stock_code AND current_year.fiscal_term = last_year.fiscal_term AND SUBSTRING(current_year.fiscal, 1, 4) = SUBSTRING(last_year.fiscal, 1, 4) + 1 ORDER BY current_year.company and current_year.to');
        return DataTables::of($data)->toJson();
    }
}