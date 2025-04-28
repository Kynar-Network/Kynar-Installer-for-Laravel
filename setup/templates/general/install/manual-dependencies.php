    <div class="content container mx-auto px-4 py-6 max-w-3xl">
        <div x-data="{ step: 1, os: 'linux', highestStepReached: 1 }">
            <!-- Step buttons and content here -->
            <div class="flex space-x-2 mb-6 justify-center items-center">
                <button :class="{ 'bg-blue-500 text-white': step === 1, 'bg-gray-300 text-gray-600': step !== 1 }"
                    @click.prevent="step = 1" :disabled="highestStepReached < 1" class="px-4 py-2 rounded-full">1</button>
                <button :class="{ 'bg-blue-500 text-white': step === 2, 'bg-gray-300 text-gray-600': step !== 2 }"
                    @click.prevent="step = 2" :disabled="highestStepReached < 2" class="px-4 py-2 rounded-full">2</button>
                <button :class="{ 'bg-blue-500 text-white': step === 3, 'bg-gray-300 text-gray-600': step !== 3 }"
                    @click.prevent="step = 3" :disabled="highestStepReached < 3" class="px-4 py-2 rounded-full">3</button>
                <button :class="{ 'bg-blue-500 text-white': step === 4, 'bg-gray-300 text-gray-600': step !== 4 }"
                    @click.prevent="step = 4" :disabled="highestStepReached < 4" class="px-4 py-2 rounded-full">4</button>
                <button :class="{ 'bg-blue-500 text-white': step === 5, 'bg-gray-300 text-gray-600': step !== 5 }"
                    @click.prevent="step = 5" :disabled="highestStepReached < 5" class="px-4 py-2 rounded-full">5</button>
            </div>


            <div x-show.transition.in.out="step === 1" style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                <h2 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars(t('manual_step_1_title', 'Step 1: Introduction')); ?></h2>
                <p class="mb-4"><?php echo nl2br(htmlspecialchars(t('manual_step_1_paragraph', 'In this setup guide, we\'ll assist you in preparing your Laravel environment. You\'ve been directed to this page because either Composer and NodeJS are not installed on your system, or the shell_exec/exec functions are disabled in your PHP configuration. These are essential components for running Laravel projects effectively. Follow the steps outlined here to resolve these issues and get your environment up and running.'))); ?></p>
                <!-- Other content here -->
                <button @click="step = 2; highestStepReached = Math.max(highestStepReached, 2)" class="mt-6 px-6 py-3 bg-blue-600 text-white rounded shadow-md hover:bg-blue-800">
                    <?php echo htmlspecialchars(t('manual_step_1_confirm', 'Confirm')); ?>
                </button>
            </div>

            <div x-show.transition.in.out="step === 2">
                <h2 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars(t('manual_step_2_title', 'Step 2: Installing Composer and NodeJS')); ?></h2>
                <p><?php echo htmlspecialchars(t('manual_step_2_os_selection', 'Select your operating system:')); ?></p>
                <div class="flex flex-col space-y-4">
                    <label>
                        <input type="radio" name="os" value="linux" x-model="os" class="mr-2"> <?php echo htmlspecialchars(t('manual_linux', 'Linux')); ?>
                    </label>
                    <label>
                        <input type="radio" name="os" value="windows" x-model="os" class="mr-2"> <?php echo htmlspecialchars(t('manual_windows', 'Windows')); ?>
                    </label>
                </div>

                <div x-show="os === 'linux'" class="bg-gray-100 p-4 rounded-md shadow-md mt-4">
                    <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars(t('manual_composer_installation_linux', 'Install Composer (For all Linux distributions):')); ?></h3>
                    <p class="mt-2 text-gray-700"><?php echo htmlspecialchars(t('manual_composer_forall_linux', 'Composer (For all Linux distributions):')); ?></p>
                    <pre class="relative bg-gray-900 text-white rounded-xl overflow-auto">
<code id="composer-linux-code">
$ php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ php composer-setup.php
$ sudo mv composer.phar /usr/local/bin/composer
</code>
                    <button class="copy-button" onclick="copyToClipboard('composer-linux-code', event)"><?php echo htmlspecialchars(t('copy', 'Copy')); ?></button>
                </pre>
                    <h3 class="text-xl font-semibold text-gray-800 mt-3"><?php echo htmlspecialchars(t('manual_nodejs_installation_linux', 'Follow the steps below based on your Linux distribution:')); ?></h3>
                    <p class="mt-2 text-gray-700"><?php echo htmlspecialchars(t('manual_step_2_os_selection', 'Select your operating system:')); ?>:</p>

                    <h4 class="mt-3 text-lg font-semibold text-gray-800"><?php echo htmlspecialchars(t('manual_ubuntu_debian', 'For Ubuntu/Debian:')); ?></h4>
                    <pre class="relative bg-gray-900 text-white rounded-xl overflow-auto">
<code id="ubuntu-debian-code">
$ curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
$ sudo apt-get install -y nodejs
</code>
        <button class="copy-button" onclick="copyToClipboard('ubuntu-debian-code', event)"><?php echo htmlspecialchars(t('copy', 'Copy')); ?></button>
    </pre>

                    <h4 class="mt-3 text-lg font-semibold text-gray-800"><?php echo htmlspecialchars(t('manual_fedora_centos', 'For Fedora/CentOS:')); ?></h4>
                    <pre class="relative bg-gray-900 text-white rounded-xl overflow-auto">
<code id="fedora-centos-code">
$ curl -fsSL https://rpm.nodesource.com/setup_16.x | sudo bash -
$ sudo yum install -y nodejs
</code>
        <button class="copy-button" onclick="copyToClipboard('fedora-centos-code', event)"><?php echo htmlspecialchars(t('copy', 'Copy')); ?></button>
    </pre>

                    <h4 class="mt-3 text-lg font-semibold text-gray-800"><?php echo htmlspecialchars(t('manual_arch_linux', 'For Arch Linux:')); ?></h4>
                    <pre class="relative bg-gray-900 text-white rounded-xl overflow-auto">
<code id="arch-linux-code">
$ sudo pacman -S nodejs npm
</code>
        <button class="copy-button" onclick="copyToClipboard('arch-linux-code', event)"><?php echo htmlspecialchars(t('copy', 'Copy')); ?></button>
    </pre>

                </div>

                <div x-show="os === 'windows'" class="bg-gray-100 p-4 rounded-md shadow-md mt-4">
                    <h3 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars(t('manual_windows_instructions', 'Windows Instructions')); ?></h3>
                    <p class="text-gray-800"><?php echo nl2br(t('manual_composer_windows', 'Download the latest installer for Composer from <a href="https://getcomposer.org/download/" target="_blank" class="text-blue-500 hover:text-blue-700">Composer\'s website</a>. Run it to install.')); ?></p>
                    <p class="text-gray-800"><?php echo nl2br(t('manual_nodejs_windows', 'For NodeJS, download the Windows installer from <a href="https://nodejs.org/en/download/" target="_blank" class="text-blue-500 hover:text-blue-700">NodeJS\'s website</a>.')); ?></p>
                </div>

                <div style="display: flex; justify-content: center; align-items: center;">
                    <button @click="step = 3; highestStepReached = Math.max(highestStepReached, 3)"
                        class="mt-6 px-6 py-3 bg-blue-600 text-white rounded shadow-md hover:bg-blue-800">
                        <?php echo htmlspecialchars(t('manual_step_2_confirm', 'Confirm')); ?>
                    </button>
                </div>

            </div>

            <div x-show.transition.in.out="step === 3">
                <h2 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars(t('manual_step_3_title', 'Step 3: Verifying Installation')); ?></h2>
                <p><?php echo nl2br(htmlspecialchars(t('manual_step_3_paragraph', 'Verify that Composer and NodeJS are installed correctly by running the following commands in your terminal or command prompt:'))); ?></p>
                <pre class="relative bg-gray-900 text-white rounded-xl overflow-auto">
<code id="verification-code">
$ composer --version
$ node --version
$ npm --version
</code>
                <button class="copy-button" onclick="copyToClipboard('verification-code', event)"><?php echo htmlspecialchars(t('copy', 'Copy')); ?></button>
            </pre>
            <div style="display: flex; justify-content: center; align-items: center;">
                <button @click="step = 4; highestStepReached = Math.max(highestStepReached, 4)" class="mt-6 px-6 py-3 bg-blue-600 text-white rounded shadow-md hover:bg-blue-800">
                    <?php echo htmlspecialchars(t('manual_step_3_confirm', 'Confirm')); ?>
                </button>
            </div>
            </div>

            <div x-show.transition.in.out="step === 4">
                <h2 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars(t('manual_step_4_title', 'Step 4: Setting Up Laravel')); ?></h2>
                <p><?php echo nl2br(htmlspecialchars(t('manual_step_4_paragraph', 'After installing the necessary dependencies, it\'s time to configure your Laravel project. This step involves navigating into your project directory and installing backend and frontend dependencies using Composer and npm. These commands will set up Laravel\'s framework and prepare the project for development. Once completed, your environment will be ready to run and customize.'))); ?></p>
                <pre class="relative bg-gray-900 text-white rounded-xl overflow-auto">
<code id="laravel-code">
$ cd "<?php echo htmlspecialchars($FoldersUp); ?>"
$ composer install
$ npm install
</code>
                <button class="copy-button" onclick="copyToClipboard('laravel-code', event)"><?php echo htmlspecialchars(t('copy', 'Copy')); ?></button>
            </pre>
            <div style="display: flex; justify-content: center; align-items: center;">
                <button @click="step = 5; highestStepReached = Math.max(highestStepReached, 5)" class="mt-6 px-6 py-3 bg-blue-600 text-white rounded shadow-md hover:bg-blue-800">
                    <?php echo htmlspecialchars(t('manual_step_4_confirm', 'Confirm')); ?>
                </button>
            </div>
            </div>

            <div x-show.transition.in.out="step === 5">
                <h2 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars(t('manual_step_5_title', 'Step 5: Completion')); ?></h2>
                <p class="mb-5"><?php echo nl2br(htmlspecialchars(t('manual_step_5_paragraph', 'Congratulations! You\'ve successfully followed all the steps to set up your Laravel environment. By clicking the "Finish" button, you will be redirected to complete the setup process and finalize the configuration of your project. Thank you for your effort! ðŸ˜Š'))); ?></p>
                <div style="display: flex; justify-content: center; align-items: center;">
                <a href="<?php echo generateUrl('step', ['stepid' => 'welcome'], true); ?>" class="px-6 py-3 bg-green-500 text-white rounded hover:bg-green-700">
                    <?php echo htmlspecialchars(t('manual_finish_button', 'Finish')); ?>
                </a>
                </div>
            </div>
        </div>
    </div>

