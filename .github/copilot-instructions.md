=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4+
- laravel/framework (LARAVEL) - v12
- spatie/laravel-permission (PERMISSION) - v6
- bootstrap (CSS) - v5.3
- datatables - v1.13+
- select2 - v4.1+
- dompdf/dompdf (PDF) - latest

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods in Spanish. For example, `esResultadoValido()`, not `validate()`.
- Check for existing components to reuse before writing a new one.
- **CRITICAL: This project uses Bootstrap 5 exclusively. NEVER use Tailwind CSS classes.**

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Spatie Permission, Bootstrap, Pest, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - de-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

de-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages in Spanish.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).
- This project uses Spatie Laravel Permission for roles and permissions.

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v11 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 11 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest
### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest {name}`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
de-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
de-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests which have a lot of duplicated data. This is often the case when testing validation rules, so consider going with this solution when writing tests for validation rules.

de-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>

=== bootstrap/core rules ===

## Bootstrap 5 (MANDATORY CSS Framework)

### Critical Rules
- **This project uses Bootstrap 5.3 exclusively as the CSS framework.**
- **NEVER use Tailwind CSS classes under any circumstances.**
- Forbidden Tailwind classes: `flex`, `grid`, `p-4`, `mt-2`, `text-blue-500`, `bg-gray-100`, etc.
- Always use Bootstrap 5 classes: `btn btn-primary`, `card`, `table table-hover`, `form-control`, `d-flex`, `row`, `col-md-6`.

### Common Bootstrap 5 Components
- Cards: `<div class="card"><div class="card-header"></div><div class="card-body"></div></div>`
- Tables: `<table class="table table-hover table-bordered">`
- Forms: `<input class="form-control">`, `<select class="form-select">`
- Buttons: `<button class="btn btn-primary">`
- Alerts: `<div class="alert alert-success alert-dismissible">`

### DataTables Integration
- All list/index views must use DataTables with Bootstrap 5 theme and Spanish language.

</code-snippet>
=== laboratorio/database rules ===

## Clinical Laboratory Database Model

This is a Clinical Laboratory Management System with 9 core tables:

### Module 1: Security (Spatie Permission)
- **users**: id, name, email, password, documento, telefono, status
- **roles**: Administrador, Bacteriólogo, Auxiliar, Recepcionista, Visualizador
- **permissions**: ver.examenes, crear.examenes, validar.resultados, etc.

### Module 2: Catalogs
- **categoria_examen**: id, categoria, descripcion, status, orden
- **profesionales**: id, nombre, apellido, documento, profesion, registro_profesional, especialidad, firma_digital, status

### Module 3: Exams (CORE - Parametrizable System)
- **examen**: id, categoria_id, codigo(UNIQUE), nombre, tipo_resultado(ENUM 8 types), unidad_medida, tecnica, muestra_requerida, valor_total, valor_remision, tiempo_entrega, requiere_ayuno, instrucciones_paciente, status
- **examen_parametros**: id, examen_id(FK CASCADE), nombre_parametro, codigo_parametro, tipo_dato(ENUM: DECIMAL/INTEGER/TEXT/SELECT), unidad_medida, decimales, orden, es_calculado(BOOLEAN), formula_calculo(JSON), requerido, opciones_select(JSON), status
- **examen_valores_referencia**: id, examen_id(FK), parametro_id(FK), tipo_referencia(ENUM: RANGO/CUALITATIVO/CATEGORIZADO/INFORMATIVO), genero(M/F), edad_min, edad_max, condicion_especial, valor_min, valor_max, operador, valor_cualitativo, categoria, descripcion, orden, status

### Module 4: Services and Results
- **clientes**: id, nombre, apellido, tipo_documento, documento, genero, fecha_nacimiento, edad(GENERATED), telefono, email, ciudad, eps
- **servicio**: id, cliente_id, numero_orden(GENERATED), fecha, valor_total, valor_pagado, medio_pago, estado_pago, observaciones
- **servicio_examen**: id, servicio_id, examen_id, profesional_id, estado(ENUM: PENDIENTE/EN_PROCESO/COMPLETADO/VALIDADO/ENTREGADO), fecha_toma_muestra, fecha_resultado, fecha_validacion, fecha_entrega
- **resultados_examen**: id, servicio_examen_id, parametro_id, valor_numerico, valor_texto, valor_cualitativo, unidad_medida, fuera_rango(BOOLEAN), tipo_alerta(ENUM: NORMAL/BAJO/ALTO/CRITICO), notas_tecnico, validado_por, fecha_validacion

=== laboratorio/exam-types rules ===

## 8 Exam Types (tipo_resultado ENUM)

Forms must be generated dynamically based on exam type.

1. **NUMERICO_SIMPLE**: 1 numeric input, 1 range. Example: GLICEMIA (70-100 mg/dL)
2. **NUMERICO_CATEGORIZADO**: 1 numeric input with categories. Example: COLESTEROL (<200: Óptimo, >=240: Alto)
3. **CUALITATIVO_SIMPLE**: 1-2 SELECT inputs. Example: HEMOCLASIFICACIÓN (Group: A/B/AB/O, RH: +/-)
4. **CUALITATIVO_REACTIVO**: 1 SELECT + 1 numeric. Example: VIH (Reactivo/No reactivo + índice)
5. **CUALITATIVO_MULTIPLE_OPCIONES**: 5-20 SELECT inputs. Example: FROTIS VAGINAL
6. **MULTIPLE_CALCULADO**: Mix of manual inputs + auto-calculated fields. Example: CLEARANCE CREATININA
7. **TABLA_HEMATOLOGIA**: 15-25 numeric inputs in table format. Example: HEMOGRAMA III
8. **TEXTO_DESCRIPTIVO**: 1 large textarea. Example: GOTA GRUESA

=== laboratorio/business-logic rules ===

## Critical Business Logic

### Dynamic Form Generation
- Use `@switch($examen->tipo_resultado)` in Blade to render forms dynamically.
- For SELECT fields: iterate `json_decode($parametro->opciones_select)`.
- For MULTIPLE_CALCULADO: show readonly inputs for calculated parameters.

### Services Required
1. **ResultadoCalculatorService**: Parse `formula_calculo` JSON, evaluate formulas safely (use `symfony/expression-language`, NOT raw `eval()`).
2. **AlertaService**: Detect out-of-range values by matching gender/age/conditions, auto-set `fuera_rango` and `tipo_alerta`.

### Validation Rules
- Validated results (status=VALIDADO) cannot be edited except by Administrador.
- PDF generation only if status=VALIDADO.
- If `tipo_resultado`=MULTIPLE_CALCULADO, at least 1 parameter must have `es_calculado=true`.
- If `es_calculado=true`, `formula_calculo` JSON is REQUIRED.
- If `tipo_dato=SELECT`, `opciones_select` JSON is REQUIRED.

### PDF Generation
- Use DomPDF with view `reportes/resultado-pdf.blade.php`.
- Include: lab logo, patient data, results table, reference values, professional signature.
- Highlight out-of-range values in red.
