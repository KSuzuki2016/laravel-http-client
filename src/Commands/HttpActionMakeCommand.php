<?php

namespace KSuzuki2016\HttpClient\Commands;

use Illuminate\Console\GeneratorCommand;

class HttpActionMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:http-action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new http action class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'HttpAction';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/http-action.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\HttpActions';
    }
}
