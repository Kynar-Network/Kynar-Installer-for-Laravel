<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($selectedLanguageId); ?>" dir="<?php echo htmlspecialchars($selectedLanguageDirection); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(t('manual_title', 'Setup Guide')); ?></title>
    <script src="<?php echo asset('js/tailwind.js'); ?>"></script>
    <script src="<?php echo asset('js/alpine.js'); ?>" defer></script>

    <script src="<?php echo asset('js/tailwind.js'); ?>"></script>

    <!-- Select2 CSS -->
    <link href="<?php echo asset('css/select2.css'); ?>" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="<?php echo asset('css/theme.css'); ?>" rel="stylesheet" />

    <script src="<?php echo asset('js/alpine.js'); ?>" defer></script>

    <script>
        tailwind.config = {
            content: [
                './*.php', // Include all PHP files in the setup folder
                './steps/*.php', // Include all PHP files in the setup folder
                './assets/css/*.css', // Include CSS files in assets
                './assets/**/*.js', // Include JavaScript files
            ],
            theme: {
                extend: {
                    colors: {
                        primary: {
                            blue: {
                                light: "#00ccdd"
                            }
                        }
                    }
                }
            },
            darkMode: "class"
        };
    </script>

    <!-- Custom CSS -->
    <link href="/setup/assets/css/manualdep.css" rel="stylesheet" />
</head>

<body class="bg-gray-100 font-sans text-gray-800 dark:bg-gray-800 dark:text-gray-300" id="app">

    <header>
        <img src="/setup/assets/img/logo.png" alt="Kynar Network" style="max-height: 50px;">
    </header>
    <?php echo $content; ?>
    <footer class="bg-gray-800 text-white py-4 mt-12">
        <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center">

            <!-- Theme Toggle -->
            <div class="order-3 md:order-1">
                <button id="theme-toggle" type="button" class="flex items-center bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path>
                    </svg>
                    <?php echo htmlspecialchars(t('toggle_theme', 'Toggle Theme')); ?>
                </button>
            </div>

            <!-- Company Info -->
            <div class="order-0 md:order-1 mb-3 md:mb-0">
                <p>© 2025 <a href="https://kynar.network" target="_blank" class="text-blue-400 hover:underline">Kynar Network</a>. All rights reserved.</p>
            </div>

            <!-- Language Select -->
            <div class="mb-3 order-1 md:mb-0">
                <select id="language-select" onchange="changeLanguage(this.value)" class="bg-gray-700 text-white rounded px-2 py-1">
                    <?php foreach ($languages as $language): ?>
                        <?php
                        $isSelected = $language['id'] === $this->languageManager->getCurrentLanguage();
                        $currentSlug = $currentStep['slug'] ?? '';
                        $translatedSlug = $allTranslations[$currentSlug][$language['id']] ?? $currentSlug;
                        ?>
                        <option value="<?php echo htmlspecialchars($language['id']); ?>"
                            data-flag-image="/setup/assets/img/flags/<?php echo htmlspecialchars($language['flag_image']); ?>"
                            data-english-name="<?php echo htmlspecialchars($language['english_name']); ?>"
                            data-native-name="<?php echo htmlspecialchars($language['native_name']); ?>"
                            data-translated-slug="<?php echo htmlspecialchars($translatedSlug); ?>"
                            <?php echo $isSelected ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($language['english_name']) . ' (' . htmlspecialchars($language['native_name']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </footer>



    <script src="<?php echo asset('js/jquery.js'); ?>"></script>
    <!-- Select2 JS -->
    <script src="<?php echo asset('js/select2.js'); ?>"></script>


    <script src="<?php echo asset('js/sweetalert2.js'); ?>"></script>
    <!-- Custom JS -->
    <script src="<?php echo asset('js/manualdep.js'); ?>"></script>
    <script src="<?php echo asset('js/language.js'); ?>"></script>
    <script src="<?php echo asset('js/theme.js'); ?>"></script>

</body>

</html>
