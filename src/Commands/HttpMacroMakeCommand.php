<?php

namespace KSuzuki2016\HttpClient\Commands;

use Illuminate\Console\GeneratorCommand;

/**
 * Class HttpMacroMakeCommand
 * @package KSuzuki2016\HttpClient\Commands
 */
class HttpMacroMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:http:macro';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new http macro class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'HttpMacro';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/http-macro.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub): string
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
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\HttpMacros';
    }
}
