<?php $this->section('title'); ?>
    <?php echo t('install_dependencies_title', 'Install Dependencies'); ?>
<?php $this->endSection(); ?>

<div class="p-8 bg-white dark:bg-gray-700 shadow-lg rounded-lg w-full">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">
        <?php echo t('install_dependencies_title', 'Install Dependencies'); ?>
    </h1>

    <p class="mb-4 text-gray-700 dark:text-gray-200 text-center">
        <?php echo t('install_dependencies_description', 'This step will install the necessary Composer packages for your Laravel application.'); ?>
    </p>

    <!-- Progress Bar -->
    <div id="progress" class="hidden">
        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-4 mb-2">
            <div id="progressBar" class="bg-blue-500 h-4 rounded-full w-0"></div>
        </div>
        <p id="progressPercentage" class="text-center text-gray-700 dark:text-gray-200 mb-4">0%</p>
    </div>
    <!-- Button Section -->
    <div class="flex justify-center items-center mb-4">
        <button id="toggleConsole" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded dark:bg-yellow-700 dark:hover:bg-yellow-800">
            <?php echo t('toggle_console_button', 'Toggle Console'); ?>
        </button>
    </div>

    <!-- Console Output -->
    <div id="console" class="mt-4 hidden">
        <pre id="consoleOutput" class="bg-black text-green-500 p-4 rounded-lg overflow-y-auto h-64"></pre>
    </div>

    <!-- Status Message -->
    <div id="statusContainer">
        <p id="statusMessage" class="text-gray-700 dark:text-gray-200 mt-4 text-center">
            <?php echo t('click_start_message', 'Click "Start Installation" to begin.'); ?>
        </p>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex flex-col md:flex-row justify-center items-center gap-4 mt-8">
        <div class="w-full md:w-auto">
            <?php if ($prevStep): ?>
                <a id="prevButton" href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                    class="hidden w-full md:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 md:py-2 px-6 md:px-4 rounded">
                    <?php echo t('check_requirements_button', 'Check Requirements'); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="w-full md:w-auto">
            <button id="startInstall" class="block w-full md:w-auto text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 md:py-2 px-6 md:px-4 rounded">
                <?php echo t('start_installation_button', 'Start Installation'); ?>
            </button>
        </div>
    </div>
</div>

<script>
    let isConsoleVisible = false;
    const toggleConsoleButton = document.getElementById('toggleConsole');
    const startInstallButton = document.getElementById('startInstall');
    const consoleDiv = document.getElementById('console');
    const consoleOutput = document.getElementById('consoleOutput');
    const progressBar = document.getElementById('progressBar');
    const progress = document.getElementById('progress');
    const progressPercentage = document.getElementById('progressPercentage');
    const statusMessage = document.getElementById('statusMessage');
    const prevButton = document.getElementById('prevButton');

    // Define the SVG spinner
    const spinnerSVG = `
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200' width="30" height="30" style="vertical-align: middle; margin-right: 5px; margin-top: 2px;">
  <path fill='yellow' stroke='yellow' stroke-width='15' transform-origin='center' d='m148 84.7 13.8-8-10-17.3-13.8 8a50 50 0 0 0-27.4-15.9v-16h-20v16A50 50 0 0 0 63 67.4l-13.8-8-10 17.3 13.8 8a50 50 0 0 0 0 31.7l-13.8 8 10 17.3 13.8-8a50 50 0 0 0 27.5 15.9v16h20v-16a50 50 0 0 0 27.4-15.9l13.8 8 10-17.3-13.8-8a50 50 0 0 0 0-31.7Zm-47.5 50.8a35 35 0 1 1 0-70 35 35 0 0 1 0 70Z'>
    <animateTransform type='rotate' attributeName='transform' calcMode='spline' dur='2' values='0;120' keyTimes='0;1' keySplines='0 0 1 1' repeatCount='indefinite'></animateTransform>
  </path>
</svg>
`;

    const checkMarkSVG = `
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 50 50' width="50" height="50" style="vertical-align: middle; margin-right: 1px; margin-bottom: 5px;">
  <path fill='green' d='M20.292 38.292l-8.292-8.292 2.828-2.828 5.464 5.464 13.292-13.292 2.828 2.828-16.12 16.12z'/>
</svg>
`;

    progress.classList.add('hidden');

    // Toggle Console Button
    toggleConsoleButton.addEventListener('click', () => {
        isConsoleVisible = !isConsoleVisible;
        consoleDiv.classList.toggle('hidden', !isConsoleVisible);
    });

    // Function to disable the Start Installation button
    function disableStartInstallButton() {
        startInstallButton.disabled = true;
        startInstallButton.classList.add('bg-gray-400', 'cursor-not-allowed');
        startInstallButton.classList.remove('bg-blue-500', 'hover:bg-blue-600', 'dark:bg-blue-700', 'dark:hover:bg-blue-800');
    }

    // Function to start installation
    function startInstallation() {
        // Show spinner and change text
        startInstallButton.innerHTML = `
        <div class="flex items-center justify-center">
            ${spinnerSVG}
            <span><?php echo t('installing_button', 'Installing...'); ?></span>
        </div>`;

        const eventSource = new EventSource('<?php echo $installDependenciesUrl; ?>');
        disableStartInstallButton();
        statusMessage.innerHTML = spinnerSVG + "<?php echo t('running_dependencies_install', 'Running dependencies install...'); ?>";
        // Reset progress and console
        progress.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressPercentage.textContent = '0%';

        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);

                switch (data.type) {
                    case 'progress':
                        // Update progress bar and percentage
                        const progress = data.progress;
                        progressBar.style.width = progress + '%';
                        progressPercentage.textContent = progress + '%';

                        var elapsedTime = formatTime(data.time.elapsed);
                        var remainingTime = formatTime(data.time.remaining);
                        const totalTime = formatTime(elapsedTime + remainingTime);
                        // Update time estimation
                        if (data.time) {
                            let timeDisplay = '';
                            if (progress < 100) {
                                const template = "<?php echo t('elapsed_remaining_time', 'Elapsed: :elapsed | Remaining: :remaining'); ?>";
                                const data = {
                                    elapsed: elapsedTime,
                                    remaining: remainingTime
                                };

                                timeDisplay = dynamicReplace(template, data);

                            } else {
                                const template = "<?php echo t('total_time', 'Total time: :elapsed'); ?>";
                                const data = {
                                    elapsed: totalTime
                                };

                                timeDisplay = dynamicReplace(template, data);
                            }

                            statusMessage.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                ${spinnerSVG}
                <span class="text-gray-600 dark:text-gray-300">${timeDisplay}</span>
            </div>`;
                        }

                        // Show previous button when installation completes
                        if (progress === 100) {
                            prevButton.classList.remove('hidden');
                            startInstallButton.classList.add('hidden'); // Hide start button
                            const template = "<?php echo t('installation_complete', 'Installation complete! Total time: :elapsed'); ?>";
                            const data = {
                                elapsed: elapsedTime
                            };

                            statusMessage.innerHTML = `${checkMarkSVG}<span class="flex items-center justify-center gap-1">
            <span>${dynamicReplace(template, data)}</span></span>`;
                        }
                        break;

                    case 'message':
                        // Add message to console
                        const line = document.createElement('div');
                        line.textContent = data.message;
                        consoleOutput.appendChild(line);
                        consoleOutput.scrollTop = consoleOutput.scrollHeight;

                        // Check for completion
                        if (data.message.includes("<?php echo t('install_dependencies_completed', '✓ All dependencies installed successfully'); ?>")) {
                            eventSource.close();
                            //startButton.classList.add('hidden');
                           // showNextButton();
                        }

                        // Check for invalid key
                        if (data.message.includes("<?php echo t('invalid_key_message', '❌ Invalid completion key.'); ?>")) {
                            eventSource.close();
                            startInstallButton.disabled = false;
                            startInstallButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                            startInstallButton.classList.add('bg-blue-500', 'hover:bg-blue-600', 'dark:bg-blue-700', 'dark:hover:bg-blue-800');
                            statusMessage.innerHTML = "<?php echo t('click_start_message', 'Click <b>Start Installation</b> to begin.'); ?>";
                            startInstallButton.textContent = "<?php echo t('start_installation_button', 'Start Installation'); ?>";
                        }

                        // Check for errors
                        if (data.message.includes("<?php echo t('error', '❌ Error: '); ?>")) {
                            eventSource.close();
                            startInstallButton.disabled = false;
                            startInstallButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                            startInstallButton.classList.add('bg-blue-500', 'hover:bg-blue-600', 'dark:bg-blue-700', 'dark:hover:bg-blue-800');
                            statusMessage.innerHTML = "<?php echo t('click_start_message', 'Click "Start Installation" to begin.'); ?>";
                            startInstallButton.textContent = "<?php echo t('start_installation_button', 'Start Installation'); ?>";
                        }
                        break;
                }
            } catch (error) {
                console.error("Failed to parse JSON:", event.data, error);
            }
        };

        eventSource.onerror = function(err) {
            eventSource.close();
            startInstallButton.disabled = false;
            startInstallButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            startInstallButton.textContent = "<?php echo t('start_installation_button', 'Start Installation'); ?>";
            consoleOutput.textContent += "\n<?php echo t('installation_failed', '\n❌ Installation failed. Please check the logs.'); ?>\n";
        };
    }

    // Add event listener to the "Start Installation" button
    startInstallButton.addEventListener('click', startInstallation);
</script>
