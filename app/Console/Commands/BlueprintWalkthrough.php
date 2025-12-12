<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BlueprintWalkthrough extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blueprint:walkthrough';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactive walkthrough for creating models with Blueprint';

    private array $models = [];
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Blueprint Model Generator Walkthrough');
        $this->info('This tool will help you create models, migrations, controllers, and more!');
        $this->newLine();

        // Check if Blueprint is installed
        if (!$this->checkBlueprintInstalled()) {
            if ($this->confirm('Blueprint is not installed. Would you like to install it now?')) {
                $this->installBlueprint();
            } else {
                $this->error('Blueprint is required for this command.');
                return 1;
            }
        }

        // Start the walkthrough
        $this->collectModels();
        $this->generateBlueprintFile();
        $this->generateCode();

        $this->info('✅ Blueprint walkthrough completed!');
    }

    private function checkBlueprintInstalled(): bool
    {
        $composerLock = json_decode(File::get(base_path('composer.lock')), true);
        
        foreach ($composerLock['packages'] ?? [] as $package) {
            if ($package['name'] === 'laravel-shift/blueprint') {
                return true;
            }
        }
        
        return false;
    }

    private function installBlueprint(): void
    {
        $this->info('📦 Installing Blueprint...');
        
        $result = Process::run('composer require --dev laravel-shift/blueprint');
        
        if ($result->failed()) {
            $this->error('❌ Failed to install Blueprint');
            return;
        }

        $this->info('✅ Blueprint installed successfully');
    }

    private function collectModels(): void
    {
        $this->info('📝 Let\'s create your models!');
        
        while (true) {
            $modelName = $this->ask('Enter model name (or "done" to finish):');
            
            if (strtolower($modelName) === 'done') {
                break;
            }
            
            if (empty($modelName)) {
                continue;
            }

            $model = $this->collectModelDetails($modelName);
            $this->models[Str::studly($modelName)] = $model;
        }
    }

    private function collectModelDetails(string $modelName): array
    {
        $this->info("\n🔧 Configuring {$modelName} model:");
        
        $model = [
            'fields' => [],
            'relationships' => [],
            'generate' => []
        ];

        // Collect fields
        $this->info('📋 Add fields for this model:');
        while (true) {
            $fieldName = $this->ask('Field name (or "done" to finish fields):');
            
            if (strtolower($fieldName) === 'done') {
                break;
            }
            
            if (empty($fieldName)) {
                continue;
            }

            $fieldType = $this->choice('Field type:', [
                'string', 'text', 'integer', 'bigInteger', 'boolean', 
                'date', 'datetime', 'timestamp', 'decimal', 'json'
            ]);

            $nullable = $this->confirm('Is this field nullable?');
            $unique = $this->confirm('Is this field unique?');

            $field = $fieldType;
            if ($nullable) $field .= ' nullable';
            if ($unique) $field .= ' unique';

            $model['fields'][$fieldName] = $field;
        }

        // Collect relationships
        $this->info('🔗 Add relationships for this model:');
        while (true) {
            $relationshipType = $this->choice('Add a relationship? (or skip)', [
                'skip', 'belongsTo', 'hasMany', 'hasOne', 'belongsToMany'
            ], 'skip');
            
            if ($relationshipType === 'skip') {
                break;
            }

            $relatedModel = $this->ask('Related model name:');
            $model['relationships'][] = [
                'type' => $relationshipType,
                'model' => $relatedModel
            ];
        }

        // What to generate
        $this->info('🛠️  What should we generate for this model?');
        $generateOptions = [
            'controller' => 'Controller',
            'api_controller' => 'API Controller', 
            'resource' => 'API Resource',
            'factory' => 'Factory',
            'seeder' => 'Seeder',
            'policy' => 'Policy',
            'form_request' => 'Form Request'
        ];

        foreach ($generateOptions as $key => $label) {
            if ($this->confirm("Generate {$label}?")) {
                $model['generate'][] = $key;
            }
        }

        // Livewire components
        if ($this->confirm('Generate Livewire components?')) {
            $model['generate'][] = 'livewire';
            
            $livewireTypes = $this->choice('Which Livewire components?', [
                'all', 'index', 'create', 'edit', 'show'
            ], 'all', true);
            
            $model['livewire_components'] = $livewireTypes;
        }

        // Filament resources (if Filament is installed)
        if ($this->checkFilamentInstalled() && $this->confirm('Generate Filament resource?')) {
            $model['generate'][] = 'filament';
        }

        return $model;
    }

    private function checkFilamentInstalled(): bool
    {
        return File::exists(base_path('vendor/filament/filament'));
    }

    private function generateBlueprintFile(): void
    {
        $this->info('📄 Generating Blueprint file...');

        $blueprint = "models:\n";

        foreach ($this->models as $modelName => $model) {
            $blueprint .= "  {$modelName}:\n";
            
            // Add fields
            if (!empty($model['fields'])) {
                foreach ($model['fields'] as $field => $type) {
                    $blueprint .= "    {$field}: {$type}\n";
                }
            }

            // Add relationships
            foreach ($model['relationships'] as $relationship) {
                $blueprint .= "    {$relationship['type']}: {$relationship['model']}\n";
            }

            $blueprint .= "\n";
        }

        // Add controllers section
        if ($this->hasGeneration('controller') || $this->hasGeneration('api_controller')) {
            $blueprint .= "controllers:\n";
            
            foreach ($this->models as $modelName => $model) {
                if (in_array('controller', $model['generate'])) {
                    $blueprint .= "  {$modelName}Controller:\n";
                    $blueprint .= "    resource: {$modelName}\n";
                }
                
                if (in_array('api_controller', $model['generate'])) {
                    $blueprint .= "  Api\\{$modelName}Controller:\n";
                    $blueprint .= "    resource: {$modelName}\n";
                    $blueprint .= "    api: true\n";
                }
            }
            $blueprint .= "\n";
        }

        File::put(base_path('draft.yaml'), $blueprint);
        $this->info('  • Blueprint file created: draft.yaml');
    }

    private function hasGeneration(string $type): bool
    {
        foreach ($this->models as $model) {
            if (in_array($type, $model['generate'])) {
                return true;
            }
        }
        return false;
    }

    private function generateCode(): void
    {
        $this->info('🚀 Generating code with Blueprint...');
        
        $result = Process::run('php artisan blueprint:build draft.yaml');
        
        if ($result->failed()) {
            $this->error('❌ Failed to generate code');
            $this->error($result->errorOutput());
            return;
        }

        $this->info('✅ Code generated successfully!');
        $this->info($result->output());

        // Generate additional components
        $this->generateAdditionalComponents();

        // Clean up
        if ($this->confirm('Remove draft.yaml file?')) {
            File::delete(base_path('draft.yaml'));
        }
    }

    private function generateAdditionalComponents(): void
    {
        foreach ($this->models as $modelName => $model) {
            // Generate Livewire components
            if (in_array('livewire', $model['generate'])) {
                $this->generateLivewireComponents($modelName, $model);
            }

            // Generate Filament resources
            if (in_array('filament', $model['generate'])) {
                $this->generateFilamentResource($modelName);
            }
        }
    }

    private function generateLivewireComponents(string $modelName, array $model): void
    {
        $this->info("🔗 Generating Livewire components for {$modelName}...");
        
        $components = $model['livewire_components'] ?? ['all'];
        
        if (in_array('all', $components)) {
            $components = ['index', 'create', 'edit', 'show'];
        }

        foreach ($components as $component) {
            $componentName = "{$modelName}{$component}";
            Process::run("php artisan make:livewire {$componentName}");
        }

        $this->info("  • Generated Livewire components for {$modelName}");
    }

    private function generateFilamentResource(string $modelName): void
    {
        $this->info("🎛️  Generating Filament resource for {$modelName}...");
        
        Process::run("php artisan make:filament-resource {$modelName}");
        $this->info("  • Generated Filament resource for {$modelName}");
    }
}