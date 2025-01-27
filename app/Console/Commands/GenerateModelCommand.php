<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class GenerateModelCommand extends Command
{
    protected $signature = 'generate:model {name} {fields*}';
    protected $description = 'Generate a model and migration with specified fields';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $fields = json_decode($this->argument('fields'), true);  // Decode the JSON input

    // Separate the fields into actual fields, required status, nullable status, and default value
    $fieldDefinitions = [];
    $requiredFields = [];
    foreach ($fields as $field) {
        [$fieldName, $fieldType, $isRequired, $isNullable, $defaultValue] = explode(':', $field);
        $fieldName = str_replace(' ', '_', strtolower($fieldName));
        $fieldDefinitions[] = compact('fieldName', 'fieldType', 'isRequired', 'isNullable', 'defaultValue');
        if ($isRequired === 'required') {
            $requiredFields[] = $fieldName;
        }
    }

        // Generate model
        $modelTemplate = str_replace(
            ['{{modelName}}', '{{fillableFields}}', '{{relations}}', '{{requiredFields}}', '{{readableColumnNames}}', '{{tableName}}'],
            [$name, $this->generateFillableFields($fieldDefinitions), '', $this->generateRequiredFields($requiredFields), $this->generateReadableColumnNames($fieldDefinitions), strtolower($name) . 's'],
            $this->getModelTemplate()
        );

        file_put_contents(app_path("/Models/{$name}.php"), $modelTemplate);

        // Generate migration
        $tableName = strtolower($name) . 's';
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        $migrationTemplate = str_replace(
            ['{{tableName}}', '{{migrationFields}}'],
            [$tableName, $this->generateMigrationFields($fieldDefinitions)],
            $this->getMigrationTemplate()
        );

        file_put_contents(database_path("/migrations/{$migrationName}"), $migrationTemplate);

        $this->info("Model and migration for {$name} created successfully.");

        // Run migrations
        Artisan::call('migrate');

        $this->info("Migration completed successfully.");
    }

    protected function getModelTemplate()
    {
        return <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {{modelName}} extends Model
{
    use HasFactory;

    protected \$table = '{{tableName}}';

    protected \$fillable = [{{fillableFields}}];

    public static function requiredFields()
    {
        return [{{requiredFields}}];
    }

    public static function readableColumnNames()
    {
        return [{{readableColumnNames}}];
    }
}
EOT;
    }

    protected function getMigrationTemplate()
    {
        return <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Create{{tableName}}Table extends Migration
{
    public function up()
    {
        Schema::create('{{tableName}}', function (Blueprint \$table) {
            \$table->id();
            {{migrationFields}}
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{{tableName}}');
    }
}
EOT;
    }

    protected function generateFillableFields($fieldDefinitions)
    {
        return implode(', ', array_map(function ($field) {
            return "'{$field['fieldName']}'";
        }, $fieldDefinitions));
    }

    protected function generateRequiredFields($requiredFields)
    {
        return implode(', ', array_map(function ($field) {
            return "'{$field}'";
        }, $requiredFields));
    }

    protected function generateReadableColumnNames($fieldDefinitions)
    {
        return implode(', ', array_map(function ($field) {
            $readableName = ucfirst(str_replace('_', ' ', $field['fieldName']));
            return "'{$field['fieldName']}' => '{$readableName}'";
        }, $fieldDefinitions));
    }

    protected function generateMigrationFields($fieldDefinitions)
    {
        return implode(";\n            ", array_map(function ($field) {
            $nullable = $field['isNullable'] === 'nullable' ? '->nullable()' : '';
            $default = !empty($field['defaultValue']) ? "->default('{$field['defaultValue']}')" : '';
            return "\$table->{$field['fieldType']}('{$field['fieldName']}'){$nullable}{$default}";
        }, $fieldDefinitions)) . ';';
    }
}
