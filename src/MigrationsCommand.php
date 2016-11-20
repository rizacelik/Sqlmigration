<?php namespace sqlmigration\MigrationsSeedings;

use Illuminate\Console\Command;

class MigrationsCommand extends Command {

    /**
     * The console command name.
     * php artisan sql:migration --eject="users, password_resets"
     * @var string
     */
    protected $signature = 'sql:migration {--eject=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts database sql to migrations and Seeding.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$pass = str_replace(' ', '',$this->option('eject'));
		$pass = explode(',',$pass);
		$migrate = new SqlMigrationsSeedings();
        $migrate->ignore($pass);
        $migrate->convert();
        $migrate->write();
        $this->info('Sql to Migration and Seeding Created Successfully');
    }
}
