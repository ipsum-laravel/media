<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMediaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('type', 20);
			$table->string('titre', 255);
            $table->string('repertoire', 40);
            $table->string('fichier', 255);
            $table->timestamps();
		});

        Schema::create('media_publication', function(Blueprint $table)
        {
            $table->integer('media_id')->unsigned();
            $table->integer('publication_id')->unsigned();
            $table->string('publication_type', 255);
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('media');
        Schema::drop('media_publication');
	}

}
