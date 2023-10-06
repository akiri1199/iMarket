<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('source_data', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code'); //証券コード
            $table->string('company'); //証券コード
            $table->string('account_criteria'); //会計基準
            $table->string('aggency_individual'); //連結個別
            $table->string('fiscal'); //決算期
            $table->string('fiscal_term'); //決算期間
            $table->date("from"); //決算期間
            $table->date("to"); //期末
            $table->string('account_item')->nullable(); //名寄前勘定科目（売上高欄に掲載）
            $table->bigInteger('sales_amount')->nullable(); //売上高
            $table->bigInteger('operating_income')->nullable(); //営業利益
            $table->bigInteger('odinary_profit')->nullable(); //経常利益
            $table->bigInteger("net_income")->nullable(); //純利益
            $table->string("net_income_per_share")->nullable(); //一株当り純利益
            $table->string("after_profit")->nullable(); //希薄化後一株当り純利益
            $table->bigInteger("net_assets")->nullable(); //純資産又は株主資本
            $table->bigInteger("total_assets")->nullable(); //総資産
            $table->float("total_assets_per_share")->nullable(); //一株当り純資産
            $table->bigInteger("operating_cash_flow")->nullable(); //営業キャッシュフロー
            $table->bigInteger("investment_cash_flow")->nullable(); //投資キャッシュフロー
            $table->bigInteger("financial_cash_flow")->nullable(); //財務キャッシュフロー
            $table->date("update_date"); //情報公開又は更新日
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_data');
    }
};