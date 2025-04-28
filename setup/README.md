# Kynar Installer

## Introduction
Kynar Installer is a sophisticated web-based installation wizard designed to streamline the setup process of Laravel applications. It provides a user-friendly interface with step-by-step guidance, making the installation process accessible to both beginners and experienced developers.

## Key Features
- 🌐 Multi-language support with 15+ languages
- 🔍 Comprehensive system requirement checks
- ⚙️ Automated dependency installation
- 📝 Easy database configuration
- 🔐 Secure first account creation
- 🎯 Step-by-step guided installation
- 🌙 Dark/Light mode support
- 📱 Responsive design
- 🛠️ Environment file (.env) auto-configuration
- ✅ Real-time validation and error handling

## How to Use
#### **Step 1: Upload Files**
Upload the necessary files to the `public` directory of your Laravel application.

#### **Step 2: Modify `index.php`**
You have two options:
- **Overwrite** the existing `index.php` file.
- **Manually insert** the following code at the beginning of your `public/index.php` file:
  ```php
  //// KYNAR NETWORK LARAVEL INSTALLER START ////
  function loadEnv() {
      $envPath = __DIR__ . '/../.env';
      if (!file_exists($envPath)) {
          return [];
      }

      $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      $envVariables = [];

      foreach ($lines as $line) {
          if (strpos(trim($line), '#') === 0 || strpos(trim($line), '=') === false) continue; // Skip comments and invalid lines
          list($name, $value) = explode('=', $line, 2);
          $envVariables[trim($name)] = trim($value, '"\'');
      }

      return $envVariables;
  }

  function env($key, $default = null) {
      static $envVariables = [];
      if (empty($envVariables)) {
          $envVariables = loadEnv();
      }
      return isset($envVariables[$key]) ? $envVariables[$key] : $default;
  }

  $envPath = '../.env';

  $dbConfigured = file_exists($envPath) && env('DB_CONNECTION') !== false;
  $appKeySet = env('APP_KEY') !== null;

  if (!$dbConfigured || !$appKeySet) {
      header("Location: /setup/index.php");
      exit;
  }
  //// KYNAR NETWORK LARAVEL INSTALLER END ////
  ```
  **Note:** The `KYNAR NETWORK` comments must remain to facilitate removal at the end of the installation process.

#### **Step 3: Start Installation**
You can begin the installation in one of two ways:
- Access the `/setup/` directory directly.
- Visit your website—if the setup process is required, you will be redirected automatically.

#### **Step 4: Follow the Installation Wizard**
Proceed through the guided installation process:
1. Welcome screen
2. System requirements check
3. Dependencies installation
4. Database configuration
5. Website information setup
6. Admin account creation
7. Installation completion

#### **Step 5: Review Installation Summary**
Ensure all configurations and installations were completed successfully before finalizing the setup.

#### **Step 6: Finalize the Setup**
Click the **Complete** button on the final setup page. This action will:
- Remove the previously inserted code from `public/index.php`.
- Attempt to delete the `/setup/` directory.

If the `/setup/` directory is not automatically removed, delete it manually.

Once completed, you can now access your Laravel application.


## Donation
If you find this project helpful, please consider supporting us through:
- [Buy me a coffee](https://buymeacoffee.com/kynarnetwork)
- [Ko-fi](https://ko-fi.com/kynarnetwork)
- [Patreon](https://patreon.com/KynarNetwork)

## Contribution
We welcome contributions from the community. To contribute:
1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

For detailed guidelines, see [CONTRIBUTION_GUIDELINES.md](CONTRIBUTION_GUIDELINES.md)

## License
This project is licensed under custom terms that protect the Kynar Network brand and donation information. Key points:

- ⚖️ **Brand Protection**: Kynar Network branding must be retained
- 💝 **Donation Links**: Must be preserved
- 🔧 **Modifications**: Allowed with restrictions
- 📜 **Legal Framework**: Governed by Swedish law

See [LICENSE.md](LICENSE.md) for complete terms and conditions.

## Final Thoughts
The Kynar Installer aims to simplify the Laravel application setup process while maintaining security and reliability. We're committed to continuous improvement and welcome feedback from our users.

For bug reports and feature requests, please use the issue tracker on our repository.
