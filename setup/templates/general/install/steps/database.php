<?php $this->section('title'); ?>
    <?php echo t('database_configuration', 'Database Configuration'); ?>
<?php $this->endSection(); ?>
<div class="col-span-12 md:col-span-7">
    <?php if ($showMigrationSection): ?>
        <!-- Migration Section -->
        <div id="migration-section">
            <h1 class="text-3xl font-bold mb-6 text-center"><?php echo t('migration_title', 'Migration Process'); ?></h1>
            <p class="mb-4 text-center"><?php echo t('migration_description', "This migration step allows you to transfer all databases with a single click.\nNote: If the 'popen' function is disabled in your PHP configuration, you will need to manually execute the provided command."); ?></p>

            <!-- Start Migration Button -->
            <div class="text-center">
                <button id="start-migration" class="bg-green-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded dark:bg-green-600 dark:hover:bg-yellow-800 flex items-center justify-center space-x-2 w-48 mx-auto">
                    <span id="loading-icon" class="hidden">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span id="button-text"><?php echo t('start_migration_button', 'Start Migration'); ?></span>
                </button>
            </div>

            <!-- Console Output -->
            <!-- Console Output -->
            <pre id="migration-console" class="bg-gray-900 text-white p-4 rounded mb-4 mt-4 overflow-x-auto whitespace-pre-wrap break-words max-w-full" style="max-height: 400px; overflow-y: auto;"></pre>
        </div>
    <?php else: ?>
        <!-- Database Configuration Form -->
        <div id="database-section">
            <h1 class="text-3xl font-bold mb-6 text-center"><?php echo t('database_title', 'Database Configuration'); ?></h1>
            <p class="mb-4"><?php echo t('database_description', 'Please provide your database connection details below.'); ?></p>

            <?php if (!empty($message)): ?>
                <div class="<?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> p-4 rounded-lg mb-6">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <!-- Database Driver -->
                <div class="mb-4">
                    <label for="db_driver" class="block text-sm font-medium text-gray-700 dark:text-white">
                        <?php echo t('db_driver_label', 'Database Driver'); ?>
                    </label>
                    <select id="db_driver" name="db_driver" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <?php foreach ($drivers as $driver): ?>
                            <option value="<?php echo htmlspecialchars($driver); ?>" <?php echo $dbConfig['driver'] === $driver ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(t($driver, ucfirst($driver))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Database Name -->
                <div class="mb-4">
                    <label for="db_name" class="block text-sm font-medium text-gray-700 dark:text-white">
                        <?php echo t('db_name_label', 'Database Name'); ?>
                    </label>
                    <input type="text" id="db_name" name="db_name"
                        value="<?php echo htmlspecialchars($dbConfig['database']); ?>"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>

                <!-- Non-SQLite Fields -->
                <div id="non_sqlite_fields">
                    <!-- Host -->
                    <div class="mb-4">
                        <label for="db_host" class="block text-sm font-medium text-gray-700 dark:text-white">
                            <?php echo t('db_host_label', 'Database Host'); ?>
                        </label>
                        <input type="text" id="db_host" name="db_host"
                            value="<?php echo htmlspecialchars($dbConfig['host']); ?>"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <!-- Port -->
                    <div class="mb-4">
                        <label for="db_port" class="block text-sm font-medium text-gray-700 dark:text-white">
                            <?php echo t('db_port_label', 'Database Port'); ?>
                        </label>
                        <input type="text" id="db_port" name="db_port"
                            value="<?php echo htmlspecialchars($dbConfig['port']); ?>"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <!-- Username -->
                    <div class="mb-4">
                        <label for="db_username" class="block text-sm font-medium text-gray-700 dark:text-white">
                            <?php echo t('db_username_label', 'Database Username'); ?>
                        </label>
                        <input type="text" id="db_username" name="db_username"
                            value="<?php echo htmlspecialchars($dbConfig['username']); ?>"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="db_password" class="block text-sm font-medium text-gray-700 dark:text-white">
                            <?php echo t('db_password_label', 'Database Password'); ?>
                        </label>
                        <input type="password" id="db_password" name="db_password"
                            value="<?php echo htmlspecialchars($dbConfig['password']); ?>"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center mt-4">
                    <button type="submit" name="check_and_save"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
                        <?php echo t('check_and_save', 'Check and Save'); ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Navigation Buttons -->
    <div class="flex justify-center mt-8">
        <?php if ($showMigrationSection && $migrationCompleted): ?>
            <div class="flex justify-between w-full">
                <?php if ($prevStep): ?>
                    <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
                        <?php echo t('previous_button', 'Previous'); ?>
                    </a>
                <?php endif; ?>

                <?php if ($nextStep): ?>
                    <a href="<?php echo htmlspecialchars($nextStep['url']); ?>"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded dark:bg-green-600 dark:hover:bg-green-800">
                        <?php echo t('next_button', 'Next'); ?>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <?php if ($prevStep): ?>
                    <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
                        <?php echo t('previous_button', 'Previous'); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Database driver fields toggle
            const dbDriverSelect = document.getElementById("db_driver");
            const nonSqliteFields = document.getElementById("non_sqlite_fields");

            if (dbDriverSelect && nonSqliteFields) {
                function toggleNonSqliteFields() {
                    nonSqliteFields.style.display = dbDriverSelect.value === "sqlite" ? "none" : "block";
                }

                dbDriverSelect.addEventListener("change", toggleNonSqliteFields);
                toggleNonSqliteFields();
            }

            // Migration section handling
            <?php if ($showMigrationSection): ?>
                const startButton = document.getElementById("start-migration");
                const loadingIcon = document.getElementById("loading-icon");
                const buttonText = document.getElementById("button-text");
                const consoleOutput = document.getElementById("migration-console");
                const navigationDiv = document.querySelector('.flex.justify-center.mt-8');

                if (startButton && consoleOutput) {
                    startButton.addEventListener("click", function(event) {
                        event.preventDefault();

                        // Disable button and show loading state
                        startButton.disabled = true;
                        startButton.classList.add('opacity-50', 'cursor-not-allowed');
                        loadingIcon.classList.remove('hidden');
                        buttonText.textContent = '<?php echo t('migrating_button', 'Migrating...'); ?>';

                        const eventSource = new EventSource('<?php echo $migrateurl; ?>');

                        eventSource.onmessage = function(event) {
                            consoleOutput.textContent += event.data + "\n";
                            consoleOutput.scrollTop = consoleOutput.scrollHeight;

                            if (event.data.includes("<?php echo t('migrate_success', 'Migrations completed successfully'); ?>")) {
                                eventSource.close();

                                // Hide the start button completely
                                startButton.style.display = 'none';

                                // Update navigation buttons
                                navigationDiv.innerHTML = `
                            <div class="flex justify-between w-full">
                                <?php if ($prevStep): ?>
                                    <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
                                        <?php echo t('previous_button', 'Previous'); ?>
                                    </a>
                                <?php endif; ?>

                                <?php if ($nextStep): ?>
                                    <a href="<?php echo htmlspecialchars($nextStep['url']); ?>"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded dark:bg-green-600 dark:hover:bg-green-800">
                                        <?php echo t('next_button', 'Next'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        `;

                                // Add success message
                                consoleOutput.textContent += "\n<?php echo t('migrate_frontend_success', '✓ Database migration completed successfully!'); ?>\n";
                                consoleOutput.textContent += "<?php echo t('migrate_proceed_next', 'You can now proceed to the next step.'); ?>\n";
                            }

                            if (event.data.includes("<?php echo t('migrate_failed', '❌ Failed to start migration process. Please check your database connection settings.'); ?>")) {
                                eventSource.close();

                                // Reset button state on error
                                startButton.disabled = false;
                                startButton.classList.remove('opacity-50', 'cursor-not-allowed');
                                loadingIcon.classList.add('hidden');
                                buttonText.textContent = '<?php echo t('start_migration_button', 'Start Migration'); ?>';
                                consoleOutput.textContent += "\n<?php echo t('migrate_failed', '❌ Failed to start migration process. Please check your database connection settings.'); ?>\n";
                            }
                        };

                        eventSource.onerror = function(err) {
                            console.error("EventSource failed:", err);
                            eventSource.close();

                            // Reset button state on error
                            startButton.disabled = false;
                            startButton.classList.remove('opacity-50', 'cursor-not-allowed');
                            loadingIcon.classList.add('hidden');
                            buttonText.textContent = '<?php echo t('start_migration_button', 'Start Migration'); ?>';

                            consoleOutput.textContent += "\n<?php echo t('migrate_failed', '❌ Failed to start migration process. Please check your database connection settings.'); ?>\n";
                        };
                    });
                }
            <?php endif; ?>
        });
    </script>
