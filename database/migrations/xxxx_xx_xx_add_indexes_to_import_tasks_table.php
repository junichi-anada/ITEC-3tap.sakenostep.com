use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToImportTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('import_tasks', function (Blueprint $table) {
            $table->index(['site_id', 'status']);
            $table->index(['site_id', 'file_hash', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('import_tasks', function (Blueprint $table) {
            $table->dropIndex(['site_id', 'status']);
            $table->dropIndex(['site_id', 'file_hash', 'created_at']);
        });
    }
} 