<?php $this->section('title'); ?>
    <?php echo t('website_title', 'Website Configuration'); ?>
<?php $this->endSection(); ?>
<div class="p-8 bg-white dark:bg-gray-700 shadow-md rounded-lg w-full">
    <h1 class="text-3xl font-bold mb-6 text-center dark:text-white">
        <?php echo t('website_title', 'Website Configuration'); ?>
    </h1>

    <form method="POST" class="w-full">
        <!-- Application Settings -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-6">
            <h2 class="text-xl font-bold mb-4 text-center dark:text-white">
                <?php echo t('website_app_section', 'Application Settings'); ?>
            </h2>
            <?php foreach ($env_vars as $var): ?>
                <div class="flex flex-wrap mb-6">
                    <label for="<?php echo htmlspecialchars($var['key']); ?>"
                        class="block text-gray-700 text-sm font-bold mb-2 dark:text-white w-full">
                        <?php echo htmlspecialchars($var['label']); ?>
                    </label>

                    <?php if (isset($var['options'])): ?>
                        <select id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            <?php foreach ($var['options'] as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>"
                                    <?php echo ($current_env[$var['key']] ?? '') === $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text"
                            id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            value="<?php echo htmlspecialchars($current_env[$var['key']] ?? ''); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <?php endif; ?>

                    <?php if (isset($var['description'])): ?>
                        <p class="text-gray-500 text-xs italic mt-1 dark:text-gray-400">
                            <?php echo htmlspecialchars($var['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Other sections follow the same pattern -->
        <!-- Logging Configuration -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-6">
            <h2 class="text-xl font-bold mb-4 text-center dark:text-white">
                <?php echo t('website_logging_section', 'Logging Configuration'); ?>
            </h2>
            <?php foreach ($log_vars as $var): ?>
                <div class="flex flex-wrap mb-6">
                    <label for="<?php echo htmlspecialchars($var['key']); ?>"
                        class="block text-gray-700 text-sm font-bold mb-2 dark:text-white w-full">
                        <?php echo htmlspecialchars($var['label']); ?>
                    </label>

                    <?php if (isset($var['options'])): ?>
                        <select id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            <?php foreach ($var['options'] as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>"
                                    <?php echo ($current_env[$var['key']] ?? '') === $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text"
                            id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            value="<?php echo htmlspecialchars($current_env[$var['key']] ?? ''); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <?php endif; ?>

                    <?php if (isset($var['description'])): ?>
                        <p class="text-gray-500 text-xs italic mt-1 dark:text-gray-400">
                            <?php echo htmlspecialchars($var['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Email Settings -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-6">
            <h2 class="text-xl font-bold mb-4 text-center dark:text-white">
                <?php echo t('website_email_section', 'Email Settings'); ?>
            </h2>
            <?php foreach ($email_vars as $var): ?>
                <div class="flex flex-wrap mb-6">
                    <label for="<?php echo htmlspecialchars($var['key']); ?>"
                        class="block text-gray-700 text-sm font-bold mb-2 dark:text-white w-full">
                        <?php echo htmlspecialchars($var['label']); ?>
                    </label>

                    <?php if (isset($var['options'])): ?>
                        <select id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            <?php foreach ($var['options'] as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>"
                                    <?php echo ($current_env[$var['key']] ?? '') === $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text"
                            id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            value="<?php echo htmlspecialchars($current_env[$var['key']] ?? ''); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <?php endif; ?>

                    <?php if (isset($var['description'])): ?>
                        <p class="text-gray-500 text-xs italic mt-1 dark:text-gray-400">
                            <?php echo htmlspecialchars($var['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- AWS Configuration -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-6">
            <h2 class="text-xl font-bold mb-4 text-center dark:text-white">
                <?php echo t('website_aws_section', 'AWS Configuration'); ?>
            </h2>
            <?php foreach ($aws_vars as $var): ?>
                <div class="flex flex-wrap mb-6">
                    <label for="<?php echo htmlspecialchars($var['key']); ?>"
                        class="block text-gray-700 text-sm font-bold mb-2 dark:text-white w-full">
                        <?php echo htmlspecialchars($var['label']); ?>
                    </label>

                    <?php if (isset($var['options'])): ?>
                        <select id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            <?php foreach ($var['options'] as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>"
                                    <?php echo ($current_env[$var['key']] ?? '') === $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text"
                            id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            value="<?php echo htmlspecialchars($current_env[$var['key']] ?? ''); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <?php endif; ?>

                    <?php if (isset($var['description'])): ?>
                        <p class="text-gray-500 text-xs italic mt-1 dark:text-gray-400">
                            <?php echo htmlspecialchars($var['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Cache & Queue Settings -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-6">
            <h2 class="text-xl font-bold mb-4 text-center dark:text-white">
                <?php echo t('website_cache_queue_section', 'Cache & Queue Settings'); ?>
            </h2>
            <?php foreach ($cache_vars as $var): ?>
                <div class="flex flex-wrap mb-6">
                    <label for="<?php echo htmlspecialchars($var['key']); ?>"
                        class="block text-gray-700 text-sm font-bold mb-2 dark:text-white w-full">
                        <?php echo htmlspecialchars($var['label']); ?>
                    </label>

                    <?php if (isset($var['options'])): ?>
                        <select id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                            <?php foreach ($var['options'] as $option): ?>
                                <option value="<?php echo htmlspecialchars($option); ?>"
                                    <?php echo ($current_env[$var['key']] ?? '') === $option ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text"
                            id="<?php echo htmlspecialchars($var['key']); ?>"
                            name="<?php echo htmlspecialchars($var['key']); ?>"
                            value="<?php echo htmlspecialchars($current_env[$var['key']] ?? ''); ?>"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
                    <?php endif; ?>

                    <?php if (isset($var['description'])): ?>
                        <p class="text-gray-500 text-xs italic mt-1 dark:text-gray-400">
                            <?php echo htmlspecialchars($var['description']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between mt-6">
            <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
                <?php echo t('previous_button', 'Previous'); ?>
            </a>
            <button type="submit"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded dark:bg-green-600 dark:hover:bg-green-800">
                <?php echo t('save_continue_button', 'Save & Continue'); ?>
            </button>
        </div>
    </form>
</div>
