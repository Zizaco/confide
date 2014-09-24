<?php namespace Zizaco\Confide;

use Zizaco\Confide\Support\GenerateCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 * This command renders the package view generator.migration and also
 * within the application directory in order to save some time.
 *
 * @license MIT
 * @package  Zizaco\Confide
 */
class MigrationCommand extends GenerateCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'confide:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration following the Confide especifications.';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('table', null, InputOption::VALUE_OPTIONAL, 'Table name.', 'users'),
            array('username', null, InputOption::VALUE_NONE, 'Includes an unique username column.'),
        );
    }

    /**
     * Execute the console command.
     */
    public function fire()
    {
        // Prepare variables
        $table = lcfirst($this->option('table'));
        $includeUsername = $this->option('username');

        $viewVars = compact(
            'table',
            'includeUsername'
        );

        // Prompt
        $this->line('');
        $this->info("Table name: $table");
        $this->comment(
            "A migration that creates the $table table will".
            " be created in app/database/migrations directory"
        );
        if ($includeUsername) {
            $this->comment(
                "An 'username' column will be included in the table."
            );
        }
        $this->line('');

        if ($this->confirm("Proceed with the migration creation? [Yes|no]")) {
            $this->info("Creating migration...");
            // Generate
            $filename = 'database/migrations/'.
                date('Y_m_d_His')."_confide_setup_users_table.php";
            $this->generateFile($filename, 'generators.migration', $viewVars);

            $this->info("Migration successfully created!");
        }
    }
}
