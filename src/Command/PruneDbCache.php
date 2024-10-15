<?php
declare(strict_types=1);

namespace Jhdxr\LaravelPruneDbCache\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PruneDbCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:db-prune-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired cache from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (Config::get('cache.default') !== 'database') {
            $this->error('Default cache driver is not set to database');
            return;
        }
        $this->prune(Config::get('cache.stores.database.table'));
        $this->prune(Config::get('cache.stores.database.lock_table', 'cache_locks'));
    }

    protected function prune($table, $column_pk = 'key', $column_expires_at = 'expiration'): void
    {
        $this->info("Pruning cache table $table");
        $batch_size = 100;
        //use cursor to iterate the whole table, and remove those expired
        //avoid use DB comparison because laravel doesn't have index on expiration column by default
        $count = 0;
        $last_id = null;
        $total = DB::table($table)->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        do {
            $expired_ids = [];
            $batch = DB::table($table)
                ->orderBy($column_pk)
                ->limit($batch_size);
            if ($last_id !== null) {
                $batch->where($column_pk, '>', $last_id);
            }
            $batch = $batch->get();
            foreach ($batch as $row) {
                if ($row->{$column_expires_at} < time()) {
                    $expired_ids[] = $row->{$column_pk};
                }
                $last_id = $row->{$column_pk};
                $bar->advance();
            }
            DB::table($table)
                ->whereIn($column_pk, $expired_ids)
                ->delete();
            $count += count($expired_ids);
        } while (count($batch) > 0);

        $bar->finish();
        $this->line('');
        $this->info("Pruned $count expired cache from $table");
    }
}