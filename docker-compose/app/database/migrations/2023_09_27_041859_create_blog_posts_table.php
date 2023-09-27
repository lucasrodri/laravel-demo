<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            /* We started adding code here*/

            $table->text('title');  // Title of our blog post          
            $table->text('body');   // Body of our blog post                  
            $table->text('user_id'); // user_id of our blog post author

            /* We stopped adding code here*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
