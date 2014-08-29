<?php namespace Zizaco\Confide\Support;

use Illuminate\Console\Command;

/**
 * This is a support class that abstracts some of the file generation
 * behaviors for the confide commands that generates files.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
abstract class GenerateCommand extends Command
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Foundation\Application $app Laravel application object
     *
     * @return void
     */
    public function __construct($app = null)
    {
        if (!is_array($app))
            parent::__construct();

        $this->app = $app ?: app();
    }

    /**
     * Generates the given file with the rendered view.
     *
     * @param string $filename Path to the file within the app directory.
     * @param string $view     View file.
     * @param array  $viewVars Variables that are going to be passed to the view.
     *
     * @return bool Success.
     */
    protected function generateFile($filename, $view, $viewVars)
    {
        $output = $this->app['view']->make('confide::'.$view, $viewVars)
            ->render();

        $filename = $this->app['path'].'/'.trim($filename,'/');
        $directory = dirname($filename);

        $this->makeDir($directory, 0755, true);
        $this->filePutContents($filename, $output);

        return true;
    }

    /**
     * Append the rendered view to the given file. Same as generateFile but
     * the 'file_put_contents' is called with the FILE_APPEND flag.
     *
     * @param string $filename Path to the file within the app directory.
     * @param string $view     View file.
     * @param array  $viewVars Variables that are going to be passed to the view.
     *
     * @return bool Success.
     */
    protected function appendInFile($filename, $view, $viewVars)
    {
        $output = $this->app['view']->make('confide::'.$view, $viewVars)
            ->render();

        $filename = $this->app['path'].'/'.trim($filename,'/');
        $directory = dirname($filename);

        $this->makeDir($directory, 0755, true);
        $this->filePutContents($filename, $output, FILE_APPEND);

        return true;
    }

    /**
     * Encapsulates mkdir function.
     *
     * @codeCoverageIgnore
     *
     * @param string $directory
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return void
     */
    protected function makeDir($directory, $mode, $recursive)
    {
        if (!is_dir($directory)) {
            @mkdir($directory, $mode, $recursive);
        }
    }

    /**
     * Encapsulates file_put_contents function.
     *
     * @codeCoverageIgnore
     *
     * @param string $filename
     * @param string $data
     * @param int    $flags
     *
     * @return void
     */
    protected function filePutContents($filename, $data, $flags = 0)
    {
        @file_put_contents($filename, $data, $flags);
    }
}
