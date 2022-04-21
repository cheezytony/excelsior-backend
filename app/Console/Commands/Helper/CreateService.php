<?php

namespace App\Console\Commands\Helper;

use Exception;
use Illuminate\Console\Command;

class CreateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service} {model? : The model class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

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
     * @return int
     */
    public function handle(): int
    {
        $parameters = $this->getParameters();

        $className = $parameters[0];
        $namespace = $parameters[1];

        $filePath = $this->getFilePath();
        $directory = $this->getDirectory($namespace);

        if ($this->serviceExists($filePath)) {
            throw new Exception('Service already exists');
        }

        if (!$this->directoryExists($directory)) {
            $this->createDirectory($directory);
        }

        $this->createFile($filePath, $directory, $className, $namespace);

        $this->info("Service class created successfully.");

        return 0;
    }

    public function getParameters(): array
    {
        $array = explode('/', $this->argument('name'));
        $className = array_pop($array);
        $namespace = implode('\\', $array);
        return [$className, $namespace];
    }

    public function getFilePath(): string
    {
        return base_path('app/Services/' . $this->argument('name') . '.php');
    }

    public function getDirectory(string $namespace): string
    {
        return 'app/Services/' . preg_replace('/\\\/', '/', $namespace);
    }

    public function serviceExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    public function directoryExists(string $namespace): bool
    {
        return is_dir($namespace);
    }

    public function createDirectory(string $directory): void
    {
        $path = explode('/', $directory);
        $depth = base_path("");
        foreach ($path as $directory) {
            $currentDirectory = "{$depth}/{$directory}";
            if (!is_dir($currentDirectory)) {
                mkdir($currentDirectory);
            }
            $depth = $currentDirectory;
        }
    }

    public function getStub(): string
    {
        $model = $this->argument('model');
        return file_get_contents(
            base_path($model ? 'stubs/service.model.stub' : 'stubs/service.stub')
        )
            ?: throw new Exception(
                __('exceptions.stub.service-file-missing'),
            );
    }

    public function getModel(): string
    {
        return 'App\\Models\\' . $this->argument('model');
    }

    public function createFile(
        string $filePath,
        string $directory,
        string $class,
        string $namespace
    ): void {
        $model = $this->getModel();
        $stub = $this->getStub();

        $namespace = "App\\Services" . (strlen($namespace) ? ("\\" . $namespace) : "");

        $replacements = compact('class', 'namespace', 'model');
        foreach ($replacements as $key => $value) {
            $stub = preg_replace("/{{ $key }}/", $value, $stub);
        }

        file_put_contents($filePath, $stub);
    }
}
