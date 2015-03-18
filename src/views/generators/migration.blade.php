<?php echo "<?php\n"; ?>

use Illuminate\Database\Migrations\Migration;

class ConfideSetupUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Creates the {{ $table }} table
        Schema::create('{{ $table }}', function ($table) {
            $table->increments('id');
@if ($includeUsername)
            $table->string('username')->unique();
@endif
@if ($includeEmail)
            $table->string('email')->unique();
@endif
            $table->string('password');
            $table->string('confirmation_code');
            $table->string('remember_token')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->timestamps();
        });

        // Creates password reminders table
        Schema::create('password_reminders', function ($table) {
            $table->integer('user_id');
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at');
            $table->primary('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('password_reminders');
        Schema::drop('{{ $table }}');
    }
}
