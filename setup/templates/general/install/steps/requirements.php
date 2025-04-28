<?php $this->section('title'); ?>
    <?php echo t('requirements_title', 'Requirements Check'); ?>
<?php $this->endSection(); ?>
<div class="col-span-12 md:col-span-7">
    <h1 class="text-3xl font-bold mb-6 text-center">
        <?php echo htmlspecialchars(t('requirements_title', 'Requirements Check')); ?>
    </h1>

    <p class="mb-4">
        <?php echo htmlspecialchars(t('requirements_paragraph', 'Ensure your server meets all necessary requirements y verifying its compatibility with the required software versions, checking for installed dependencies, ensuring proper file permissions, and optimizing performance for a seamless user experience.')); ?>
    </p>

    <!-- PHP Information -->
    <h2 class="text-lg font-bold mb-2"><?php echo t('php_information_title', 'PHP Information'); ?></h2>
    <ul class="list-disc pl-5 mb-6">
        <li>PHP Version: <?php echo $phpVersionOk ?
                                '<span class="text-green-500">' . t('passed', 'Passed') . '</span>' :
                                '<span class="text-red-500">' . t('failed', 'Failed') . ' (' . t('minimum_required_php_version', 'Minimum required is PHP') . ' ' . $minPhpVersion . ')</span>';
                            ?></li>
    </ul>

    <!-- Extensions -->
    <h2 class="text-lg font-bold mb-2"><?php echo t('extensions_title', 'Extensions'); ?></h2>
    <ul class="list-disc pl-5 mb-6">
        <?php foreach ($extensions as $extension => $info): ?>
            <?php if ($info['status'] == 'Passed'): ?>
                <li>
                    <?php echo htmlspecialchars($info['name']); ?>:
                    <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
                </li>
            <?php else: ?>
                <li>
                    <div class="relative inline-block">
                        <span class="<?php echo $info['required'] ? 'text-red-500' : 'text-yellow-500'; ?> cursor-pointer"
                            data-tooltip="<?php echo htmlspecialchars($info['description']); ?>">
                            <?php echo htmlspecialchars($info['name']); ?>:
                            <?php if ($info['required']): ?>
                                <svg class="inline w-4 h-4 text-gray-500 hover:text-blue-500"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v5h-1v-5h-1M9 9h6m-7.5 8.25h15a.75.75 0 00.75-.75V4.5A.75.75 0 0018 3.75H6a.75.75 0 00-.75.75v12a.75.75 0 00.75.75z" />
                                </svg>
                            <?php endif; ?>
                        </span>
                        <div class="tooltip hidden absolute z-10 left-0 top-full mt-1 p-2 bg-gray-800 text-white rounded shadow-lg">
                            <?php echo htmlspecialchars($info['description']); ?>
                        </div>
                    </div>
                    <span class="<?php echo $info['required'] ? 'text-red-500' : 'text-yellow-500'; ?>">
                        <?php if ($info['required']): ?>
                            <?php echo t('failed', 'Failed'); ?> (Required for
                        <?php endif; ?>
                        <?php echo htmlspecialchars($info['description']); ?>)
                    </span>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <!-- Laravel Requirements -->
    <h2 class="text-lg font-bold mb-2"><?php echo t('laravel_requirements_title', 'Laravel Requirements'); ?></h2>
    <ul class="list-disc pl-5 mb-6">
        <li>
            <?php echo t('env_file_title', '.env File'); ?>:
            <?php if ($requirements['dotenvFileOk']): ?>
                <span class="text-green-500"><?php echo t('env_file_passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('env_file_failed', 'Failed (Make sure the .env file exists and is writable)'); ?></span>
                <button onclick="createEnvFile()" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                    <?php echo t('create_env_file_button', 'Create .env'); ?>
                </button>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('storage_directory_title', 'Storage Directory'); ?>:
            <?php if ($requirements['storageDirOk']): ?>
                <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('storage_directory_failed', 'Failed (Make sure the <b>storage</b> directory exists and is writable)'); ?></span>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('framework_directory_title', 'Storage/Framework Directory'); ?>:
            <?php if ($requirements['frameworkDirOk']): ?>
                <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('framework_directory_failed', 'Failed (Make sure the <b>framework</b> directory exists in the <b>storage</b> folder and is writable)'); ?></span>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('framework_cache_directory_title', 'Storage/Framework/cache Directory'); ?>:
            <?php if ($requirements['framework_cacheDirOk']): ?>
                <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('framework_cache_directory_failed', 'Failed (Make sure the <b>cache</b> directory exists in the <b>framework</b> folder and is writable)'); ?></span>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('framework_session_directory_title', 'Storage/framework/sessions Directory'); ?>:
            <?php if ($requirements['framework_sessionDirOk']): ?>
                <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('framework_session_directory_failed', 'Failed (Make sure the <b>sessions</b> directory exists in the <b>framework</b> folder and is writable)'); ?></span>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('framework_testing_directory_title', 'Storage/framework/testing Directory'); ?>:
            <?php if ($requirements['framework_testingDirOk']): ?>
                <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('framework_testing_directory_failed', 'Failed (Make sure the <b>testing</b> directory exists in the <b>framework</b> folder and is writable)'); ?></span>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('framework_views_directory_title', 'Storage/framework/views Directory'); ?>:
            <?php if ($requirements['framework_viewsDirOk']): ?>
                <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('framework_views_directory_failed', 'Failed (Make sure the <b>views</b> directory exists in the <b>framework</b> folder and is writable)'); ?></span>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('logs_directory_title', 'Storage/logs Directory'); ?>:
            <?php if ($requirements['logsDirOk']): ?>
                <span class="text-green-500"><?php echo t('passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('logs_directory_failed', 'Failed (Make sure the <b>logs</b> directory exists in <b>storage</b> and is writable)'); ?></span>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('composer_installed_title', 'Composer Installed'); ?>:
            <?php if ($requirements['composerInstalled']): ?>
                <span class="text-green-500"><?php echo t('composer_installed_passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('composer_installed_failed', 'Failed (Install Composer to continue)'); ?></span>
                <div class="flex gap-4 mt-2">
                    <a href="<?php echo generateUrl('setup.install_manual', ['lang' => $this->languageManager->getCurrentLanguage()]); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                        <?php echo t('install_composer_link', 'Install Composer'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('npm_installed_title', 'NPM Installed'); ?>:
            <?php if ($requirements['npmInstalled']): ?>
                <span class="text-green-500"><?php echo t('npm_installed_passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('npm_installed_failed', 'Failed (Install NPM to continue)'); ?></span>
                <div class="flex gap-4 mt-2">
                    <a href="<?php echo generateUrl('setup.install_manual', ['lang' => $this->languageManager->getCurrentLanguage()]); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                        <?php echo t('install_npm_link', 'Install NPM'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </li>

        <li>
            <?php echo t('vendor_folder_title', 'Vendor Folder'); ?>:
            <?php if ($requirements['vendorFolderPassed']): ?>
                <span class="text-green-500"><?php echo t('vendor_folder_passed', 'Passed'); ?></span>
            <?php else: ?>
                <span class="text-red-500"><?php echo t('vendor_folder_failed', 'Failed (Missing or Empty)'); ?></span>
                <?php if ($requirements['failedChecks'] > 0): ?>
                    <a href="<?php echo htmlspecialchars($install_req['url']); ?>" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                        <?php echo t('install_dependencies_link', 'Install Dependencies'); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </li>
    </ul>

    <!-- Navigation Buttons -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-8">
        <div class="w-full md:w-auto">
            <?php if ($prevStep): ?>
                <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                    class="block w-full md:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 md:py-2 px-6 md:px-4 rounded">
                    <?php echo t('previous_button', 'Previous'); ?>
                </a>
            <?php endif; ?>
        </div>
        <!-- Optional Checks Warning Message (Mobile) -->
        <?php if ($requiredFailedChecks === 0 && $failedChecks > 0): ?>
            <p class="w-full text-yellow-500 text-center px-4">
                <?php echo t('optional_checks_failed_message', 'Optional checks failed. You can proceed, but some features may be limited.'); ?>
            </p>
        <?php endif; ?>
        <div class="w-full md:w-auto">
            <?php if ($nextStep && $requirements['requiredFailedChecks'] === 0): ?>
                <a href="<?php echo htmlspecialchars($nextStep['url']); ?>"
                    class="block w-full md:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 md:py-2 px-6 md:px-4 rounded">
                    <?php echo t('next_button', 'Next'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<script>
    // Move the JavaScript to a separate file
    document.addEventListener('DOMContentLoaded', function() {
        initializeTooltips();
    });

    function createEnvFile() {
        fetch('<?php echo htmlspecialchars(generateUrl('setup.env.create')) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    key: "<?php echo $key; ?>"
                }).toString()
            })
            .then(response => response.json())
            .then(handleEnvFileResponse)
            .catch(handleEnvFileError);
    }

    function initializeTooltips() {
        const tooltips = document.querySelectorAll('[data-tooltip]');
        tooltips.forEach(tooltip => {
            tooltip.addEventListener('mouseenter', showTooltip);
            tooltip.addEventListener('mouseleave', hideTooltip);
        });
    }

    function showTooltip(event) {
        const tooltipBox = document.createElement('div');
        tooltipBox.className = 'custom-tooltip';
        tooltipBox.innerText = event.target.getAttribute('data-tooltip');
        document.body.appendChild(tooltipBox);

        const rect = event.target.getBoundingClientRect();
        tooltipBox.style.left = rect.left + window.scrollX + 'px';
        tooltipBox.style.top = rect.bottom + window.scrollY + 'px';
    }

    function hideTooltip() {
        const tooltipBox = document.querySelector('.custom-tooltip');
        if (tooltipBox) {
            tooltipBox.remove();
        }
    }

    function handleEnvFileResponse(data) {
        if (data.success) {
            Swal.fire({
                title: `<?php echo htmlspecialchars(t("congratulations", "Congratulations!")); ?>`,
                text: `<?php echo htmlspecialchars(t("env_file_creation_success", ".env file created successfully!")); ?>`,
                icon: "success",
                confirmButtonText: `<?php echo htmlspecialchars(t("reload", "Reload")); ?>`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        } else {
            Swal.fire({
                title: `<?php echo htmlspecialchars(t("error_occurred", "Error occurred!")); ?>`,
                text: `<?php echo htmlspecialchars(t("env_file_creation_failure", "Failed to create .env file. More details: ")); ?>` + data.message,
                icon: "error",
                confirmButtonText: `<?php echo htmlspecialchars(t("reload", "Reload")); ?>`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }
    }

    function handleEnvFileError(error) {
        Swal.fire({
            title: `<?php echo htmlspecialchars(t("error_occurred", "Error occurred!")); ?>`,
            text: `<?php echo htmlspecialchars(t("env_file_creation_error", "An error occurred while creating the .env file.")); ?>` + data.message,
            icon: "error",
            confirmButtonText: `<?php echo htmlspecialchars(t("reload", "Reload")); ?>`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                location.reload();
            }
        });
    }
</script>
