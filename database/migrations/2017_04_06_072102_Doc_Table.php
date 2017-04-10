<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('DOCUMENTS')) {
            Schema::connection('mysql_migrate')->create('DOCUMENTS', function (Blueprint $table) {
                $table->bigIncrements('id')->unique();
                $table->string('productId');
                $table->string('userId');
                $table->string('profileName');
                $table->string('helpfulness');
                $table->float('score');
                $table->integer('time');
                $table->string('summary');
                $table->string('text', 100000);
                $table->timestamps();
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mysql_migrate')->dropIfExists('DOCUMENTS');
        \App\Models\TokenDocMappingModel::deleteAllIngestedKeysFromRedis();
    }
}
