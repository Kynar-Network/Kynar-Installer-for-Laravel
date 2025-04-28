<?php $this->section('title'); ?>
    <?php echo t('summary_title', 'Installation Summary'); ?>
<?php $this->endSection(); ?>
<div class="container mx-auto p-8 shadow-lg rounded-lg w-full bg-gray-100 dark:bg-gray-800">
    <h1 class="text-4xl font-bold mb-6 text-center dark:text-white">
        <?php echo t('summary_title', 'Installation Summary'); ?>
    </h1>

    <p class="mb-8 text-lg text-center dark:text-gray-300">
        <?php echo t('summary_description', 'Here is a summary of your installation.'); ?>
    </p>

    <!-- Requirements Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-4 dark:bg-gray-700">
        <h2 class="text-xl font-bold mb-4 dark:text-white">
            <?php echo t('summary_requirements_title', 'System Requirements'); ?>
        </h2>

        <!-- PHP Version -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">
                <?php echo t('php_version', 'PHP Version'); ?>
            </h3>
            <div class="flex items-center">
                <span class="<?php echo $requirements['php_version']['status'] ? 'text-green-500' : 'text-red-500'; ?> mr-2">
                    <?php if ($requirements['php_version']['status']): ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    <?php else: ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    <?php endif; ?>
                </span>
                <span class="dark:text-white">
                    <?php echo t('php_version_current', 'Current Version:'); ?>
                    <?php echo htmlspecialchars($requirements['php_version']['version']); ?>
                </span>
            </div>
        </div>

        <!-- Extensions -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-2 dark:text-white">
                <?php echo t('extensions_title', 'Extensions'); ?>
            </h3>
            <div class="space-y-2">
                <?php foreach ($requirements['extensions'] as $id => $extension): ?>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="dark:text-white"><?php echo htmlspecialchars($extension['name']); ?></span>
                            <?php if (!$extension['required']): ?>
                                <span class="text-gray-500 text-sm ml-2">(<?php echo t('optional', 'Optional'); ?>)</span>
                            <?php endif; ?>
                        </div>
                        <span class="<?php echo $extension['status'] === 'Passed' ? 'text-green-500' : ($extension['required'] ? 'text-red-500' : 'text-yellow-500'); ?>">
                            <?php if ($extension['status'] === 'Passed'): ?>
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            <?php else: ?>
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($extension['status']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Database Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-4 dark:bg-gray-700">
        <h2 class="text-xl font-bold mb-4 dark:text-white">
            <?php echo t('summary_databases_title', 'Databases'); ?>
        </h2>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
            <?php echo t('database_driver_label', 'Database Driver'); ?>:
            <?php echo htmlspecialchars($database['driver']); ?>
        </p>
        <p class="mb-2 text-gray-700 dark:text-gray-300">
            <?php echo t('database_name_label', 'Database Name'); ?>:
            <?php echo htmlspecialchars($database['name']); ?>
        </p>

        <!-- Tables -->
        <div class="mt-2">
            <?php if (isset($database) && !empty($database['tables'])): ?>
                <h3 class="text-lg font-bold mb-1 dark:text-white">Tables</h3>
                <ul class="list-disc list-inside">
                    <?php foreach ($database['tables'] as $table): ?>
                        <li class="mb-2 text-gray-700 dark:text-gray-300">
                            <?php echo htmlspecialchars(is_object($table) ? reset((array)$table) : $table); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="flex justify-center items-center p-4 bg-white rounded shadow dark:bg-gray-800">
                    <a href="<?php echo generateUrl('step', ['stepid' => 'database'], true); ?>" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:text-white dark:bg-blue-600 dark:hover:bg-blue-800">
                        <?php echo t('setup_database', 'Setup Database'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Created User Section -->
    <?php if (!empty($user['username'])): ?>
        <div class="bg-white p-6 rounded-lg shadow-md mb-4 dark:bg-gray-700">
            <h2 class="text-xl font-bold mb-4 dark:text-white">
                <?php echo t('summary_user_title', 'Created User'); ?>
            </h2>
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-700 dark:text-gray-300">Username:</span>
                <span class="text-gray-800 font-bold dark:text-white">
                    <?php echo htmlspecialchars($user['username']); ?>
                </span>
            </div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-700 dark:text-gray-300">Email:</span>
                <span class="text-gray-800 font-bold dark:text-white">
                    <?php echo htmlspecialchars($user['email']); ?>
                </span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Support Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-4 text-center dark:bg-gray-700">
        <h2 class="text-xl font-bold mb-4 dark:text-white">Support the Project</h2>
        <p class="mb-4 text-gray-700 dark:text-gray-300">
            If you find this project helpful, please consider supporting us via:
        </p>
        <div class="flex justify-center space-x-4">
            <a href="https://buymeacoffee.com/kynarnetwork" target="_blank" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-orange-600 dark:hover:bg-orange-700">
                Buy me a coffee
            </a>
            <a href="https://ko-fi.com/kynarnetwork" target="_blank" class="bg-pink-500 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-pink-600 dark:hover:bg-pink-800">
                Ko-fi
            </a>
            <a href="https://patreon.com/KynarNetwork" target="_blank" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-purple-600 dark:hover:bg-purple-800">
                Patreon
            </a>
        </div>
    </div>

    <!-- Complete Button -->
    <?php if (isset($database) && !empty($database['tables'])): ?>
        <div class="text-center mt-8">
            <button id="completeButton"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-green-600 dark:hover:bg-green-800">
                <?php echo t('summary_complete_button', 'Complete'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript for handling the Complete button click -->
<script>
    document.getElementById("completeButton").addEventListener("click", function(event) {
        event.preventDefault();
        fetch("<?php echo generateUrl('setup.complete'); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    key: "<?php echo $key; ?>"
                }).toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    Swal.fire({
                        title: `<?php echo htmlspecialchars(t("congratulations", "Congratulations!")); ?>`,
                        html: `<p>${data.message}</p>`,
                        icon: "success",
                        confirmButtonText: `<?php echo htmlspecialchars(t("finalize", "Finalize")); ?>`,
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            window.location.href = data.url;
                        }
                    });
                } else {
                    console.log(data.message);
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error:", error);
            });
    });
</script>
