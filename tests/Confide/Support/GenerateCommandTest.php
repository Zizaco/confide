<?php namespace Zizaco\Confide\Support;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateCommandTest extends TestCase
{
    /**
     * Calls Mockery::close
     */
    protected function tearDown(): void
    {
        $this->addToAssertionCount(m::getContainer()->mockery_getExpectationCount());
        m::close();
    }

    public function testShouldGenerateFile()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $config = m::mock('Config');
        $view = m::mock('View');
        $app = [
            'config'=>$config,
            'view'=>$view,
            'path'=>'/where/the/app/is',
        ];
        $command = m::mock('Zizaco\Confide\Support\_GenerateCommandStub[makeDir,filePutContents]', [$app]);
        $command->shouldAllowMockingProtectedMethods();
        $filename = 'path/to/file.php';
        $viewName = 'generate.my_view';
        $viewVars = [
            'someVar' => 'someValue'
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $view->shouldReceive('make')
            ->once()
            ->with('confide::'.$viewName, $viewVars)
            ->andReturn($view);

        $view->shouldReceive('render')
            ->once()
            ->andReturn('The rendered content');

        $command->shouldReceive('generateFile')
            ->passthru();

        $command->shouldReceive('makeDir')
            ->with("/where/the/app/is/path/to", 493, true)
            ->once()
            ->andReturn(true);

        $command->shouldReceive('filePutContents')
            ->with("/where/the/app/is/path/to/file.php", "The rendered content")
            ->once()
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $command->generateFile($filename, $viewName, $viewVars);
    }

    public function testShouldAppendInFile()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $config = m::mock('Config');
        $view = m::mock('View');
        $app = [
            'config'=>$config,
            'view'=>$view,
            'path'=>'/where/the/app/is',
        ];
        $command = m::mock('Zizaco\Confide\Support\_GenerateCommandStub[makeDir,filePutContents]', [$app]);
        $command->shouldAllowMockingProtectedMethods();
        $filename = 'path/to/file.php';
        $viewName = 'generate.my_view';
        $viewVars = [
            'someVar' => 'someValue'
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $view->shouldReceive('make')
            ->once()
            ->with('confide::'.$viewName, $viewVars)
            ->andReturn($view);

        $view->shouldReceive('render')
            ->once()
            ->andReturn('The rendered content');

        $command->shouldReceive('appendInFile')
            ->passthru();

        $command->shouldReceive('makeDir')
            ->with("/where/the/app/is/path/to", 493, true)
            ->once()
            ->andReturn(true);

        $command->shouldReceive('filePutContents')
            ->with("/where/the/app/is/path/to/file.php", "The rendered content", 8)
            ->once()
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $command->appendInFile($filename, $viewName, $viewVars);
    }
}

/**
 * A stub class that extends GenerateCommand
 *
 * @see \Zizaco\Confide\Support\GenerateCommand
 */
class _GenerateCommandStub extends GenerateCommand
{

}
