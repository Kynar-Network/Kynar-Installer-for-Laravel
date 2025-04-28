<?php $this->section('title'); ?>
    <?php echo t('create_account_title', 'Create First Account'); ?>
<?php $this->endSection(); ?>
<div class="col-span-12 md:col-span-7">
    <h1 class="text-3xl font-bold mb-6 text-center dark:text-white">
        <?php echo t('create_account_title', 'Create First Account'); ?>
    </h1>

    <p class="mb-4 dark:text-gray-300">
        <?php echo t('create_account_description', 'Please create an account to manage your application.'); ?>
    </p>
    <!-- Display errors here -->
    <?php if (!empty($errors)): ?>
        <div class="bg-red-500 text-white p-4 rounded mb-4 dark:bg-red-800">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (!$usersTableExists): ?>
        <div class="bg-red-500 text-white p-4 rounded mb-4 dark:bg-red-800">
            <?php echo t('create_account_users_table_missing', 'The users table does not exist. Please create it first.'); ?>
        </div>
        <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
            <?php echo t('create_account_go_back', 'Go Back'); ?>
        </a>
    <?php else: ?>
        <form method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 dark:bg-gray-700">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2 dark:text-white">
                    <?php echo t('create_account_username_label', 'Username'); ?>
                </label>
                <input id="username" type="text" name="username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2 dark:text-white">
                    <?php echo t('create_account_email_label', 'Email'); ?>
                </label>
                <input id="email" type="email" name="email"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2 dark:text-white">
                    <?php echo t('create_account_password_label', 'Password'); ?>
                </label>
                <input id="password" type="password" name="password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2 dark:text-white">
                    <?php echo t('create_account_confirm_password_label', 'Confirm Password'); ?>
                </label>
                <input id="confirm_password" type="password" name="confirm_password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-800 dark:text-white dark:border-gray-600">
            </div>

            <div class="flex justify-between mt-6">
                <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded dark:bg-blue-600 dark:hover:bg-blue-800">
                    <?php echo t('create_account_previous_button', 'Previous'); ?>
                </a>
                <button type="submit"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-green-600 dark:hover:bg-green-800">
                    <?php echo t('create_account_register_button', 'Register'); ?>
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>
