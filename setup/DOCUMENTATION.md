# Configuration Documentation

## Configs Directory Structure
The `configs` directory contains essential configuration files:

```filetree
configs/
├── extensions.json    # PHP extensions configuration
├── general.json      # General installer settings
├── routes.php        # Route definitions
└── steps.json        # Installation steps configuration
```

## Configuration Files

### extensions.json
This file defines required and optional PHP extensions:

```json
{
            "extension_name": "PCRE",
            "extension_id": "pcre",
            "extension_description": "The PCRE (Perl Compatible Regular Expressions) PHP Extension is required for regular expression operations.",
            "required": true
        },
        {
            "extension_name": "PDO",
            "extension_id": "pdo",
            "extension_description": "The PDO (PHP Data Objects) PHP Extension is required for database connectivity.",
            "required": true
        },
        {
            "extension_name": "Session",
            "extension_id": "session",
            "extension_description": "The Session PHP Extension is required for managing user sessions.",
            "required": true
        },
        {
            "extension_name": "Tokenizer",
            "extension_id": "tokenizer",
            "extension_description": "The Tokenizer PHP Extension is required for tokenizing PHP code.",
            "required": true
        }
```

#### Adding Extensions
1. Add new extension to `extensions.json`
2. Add translations for the extension

#### Translation Structure
Extensions descriptions are stored in language files:

```json
// filepath: languages/en.json for example
"ext_desc.pcre": "The PCRE PHP Extension is required for regular expression operations.",
"ext_desc.pdo": "The PDO PHP Extension is required for database connectivity.",
"ext_desc.session": "The Session PHP Extension is required for managing user sessions.",
"ext_desc.tokenizer": "The Tokenizer PHP Extension is required for tokenizing PHP code.",
```

#### Translation Keys
- Format: `ext_desc.{extension_id}`
- Example: `ext_desc.pdo` for PDO extension
- Must be added to all language files

#### Best Practices
1. Keep extension IDs lowercase
2. Provide clear descriptions
3. Test extension detection
4. Update all language files
5. Verify translations display correctly

--------------

### general.json
Controls installer settings and behavior:

```json
{
  "default_language": "en",
  "fallback_language": "en",
  "installer": {
    "name": "Kynar Installer",
    "version": "1.0.0"
  },
  "requirements": {
    "php_version": "8.2.0"
  },
  "encryption_key": "aaaaaaaaa",
  "setup_key": "bbbbbbbb",
  "original_setup_key": "ccccccccc",
  "default_template": "general"
}
```

#### Key Descriptions

- **Language Settings**
   - `default_language`: Primary language for the installer interface
   - `fallback_language`: Used when a translation is missing in the selected language

- **Installer Information**
   - `installer.name`: Branding name shown throughout the interface
   - `installer.version`: Version tracking for updates and compatibility

- **System Requirements**
   - `requirements.php_version`: Minimum PHP version needed to run the application

- **Security Keys**
   - `encryption_key`: Used for encrypting sensitive configuration data
   - `setup_key`: Current session's installation key
   - `original_setup_key`: Initial key for validating installation integrity

- **Template**
   - `default_template`: Defines the default UI theme template

#### Important Notes
- Never commit real encryption keys to version control
- Keep the setup and original setup keys secure
- Version numbers should follow semantic versioning
- Template name must match an existing template directory

--------------
### steps.json
Defines installation wizard steps:

```json
{
    "steps": [
        {
            "id": "requirements",
            "order": 1,
            "title": "step_2_name",
            "description": "step_2_description",
            "controller": "App\\Controllers\\Steps\\RequirementsController",
            "template": "install/steps/requirements",
            "required": true,
            "slug": "requirements",
            "slug_default": "requirements",
            "methods": ["GET"]
        }
    ]
}
```

#### Steps Configuration Fields
- `id`: Unique identifier for the step
- `order`: Numerical order in the installation process
- `title`: Translation key for step name
- `description`: Translation key for step description
- `controller`: Fully qualified controller class name
- `template`: Path to the view template
- `required`: Whether the step is mandatory
- `slug`: URL-friendly identifier
- `slug_default`: Default slug if not localized
- `methods`: Allowed HTTP methods for this step

--------------

### routes.php
The router configuration supports various HTTP methods and route patterns:

```php
$router->addRoute(METHOD, PATH, CONTROLLER_ACTION, ROUTE_NAME);
```

#### Parameters
- `METHOD`: HTTP method ('GET', 'POST', etc.)
- `PATH`: URL path pattern
- `CONTROLLER_ACTION`: Controller and method in format 'ControllerName@methodName'
- `ROUTE_NAME`: Unique identifier for the route

#### Examples

```php
// Create environment file
$router->addRoute('POST', '/setup/create-env', 'UtilsController@create_env', 'setup.env.create');
```
This route:
- Handles POST requests to `/setup/create-env`
- Uses `UtilsController->create_env()` method
- Named `setup.env.create` for reference
- Used for generating the .env file during installation

### Dynamic and Stream Routes

The router supports dynamic parameters and streaming responses:

```php
$router->addRoute(METHOD, PATH_WITH_PARAMS, CONTROLLER_ACTION, ROUTE_NAME);
```

#### Stream Routes Example
```php
$router->addRoute('GET', '/setup/{lang}/migrate-stream/{key}', 'MigrateStreamController@handle', 'setup.migrate.stream');
```

This route configuration:
- **Method**: Handles GET requests
- **Dynamic Parameters**:
  - `{lang}`: Language code (e.g., 'en', 'es')
  - `{key}`: Security key for the stream
- **Controller**: Uses `MigrateStreamController@handle`
- **Purpose**: Streams migration progress in real-time
- **Usage**: Used during database migration process


#### Important Notes
- Controllers must be in the `App\Controllers` namespace
- Method names in routes are camelCase
- Route names should be lowercase with dots as separators
- Always validate POST data in controllers

## Testing Process

1. Add new extension configuration
2. Add translations for all languages
3. Verify extension detection:
   ```php
   php -m | grep new_ext
   ```
4. Test through web interface
5. Verify translations display correctly
6. Test required/optional behavior

## Troubleshooting

### Common Issues
1. Extension not detected
   - Verify PHP extension is installed
   - Check extension_id spelling
   - Restart web server

2. Translation not showing
   - Verify key format
   - Check language file exists
   - Clear translation cache

3. Route not working
   - Check controller exists
   - Verify method exists
   - Check route definition

## Need Help?
- Review [CONTRIBUTION_GUIDELINES.md](CONTRIBUTION_GUIDELINES.md)
- Check [LICENSE.md](LICENSE.md)
- Submit issues on GitHub
- Contact: support@kynarnetwork.com
