<?php $this->section('title'); ?>
     <?php echo t('welcome_title', 'Welcome to Laravel Installer'); ?>
<?php $this->endSection(); ?>
<div class="col-span-12 md:col-span-7">
    <h1 class="text-3xl font-bold mb-6 text-center">
        <?php
        // Use step-specific title translation or fall back to welcome title
        $titleKey = $currentStep['title'] ?? 'welcome_title';
        echo htmlspecialchars(t($titleKey, 'Welcome to Installer'));
        ?>
    </h1>

    <p class="mb-4">
        <?php
        // Use step-specific description translation
        $descriptionKey = $currentStep['description'] ?? 'welcome_paragraph';
        echo htmlspecialchars(t($descriptionKey, 'This installer will guide you through the setup process.'));
        ?>
    </p>

    <ul class="list-decimal pl-5 mb-6">
        <?php foreach ($stepsConfig['steps'] as $step): ?>
            <?php
            // Get translated title for each step
            $stepTitle = t($step['title']) ?: $step['title'];
            $activeClass = $step['id'] === $currentStep['id'] ? 'font-bold text-blue-500' : 'text-gray-700 dark:text-white';
            ?>
            <li class="<?php echo $activeClass; ?>">
                <?php if ($step['id'] === $currentStep['id']): ?>
                    <strong><?php echo htmlspecialchars($stepTitle); ?></strong>
                <?php else: ?>
                    <span><?php echo htmlspecialchars($stepTitle); ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="flex justify-center mt-8">
        <?php if ($isFirstStep): ?>
            <a href="<?php echo htmlspecialchars($nextStep['url']); ?>"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                <?php echo htmlspecialchars(t('start_installation_button', 'Start Installation')); ?>
            </a>
        <?php else: ?>
            <div class="flex justify-between w-full">
                <?php if ($prevStep): ?>
                    <a href="<?php echo htmlspecialchars($prevStep['url']); ?>"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <?php echo htmlspecialchars(t('previous_button', 'Previous')); ?>
                    </a>
                <?php endif; ?>
                <?php if ($nextStep): ?>
                    <a href="<?php echo htmlspecialchars($nextStep['url']); ?>"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <?php echo htmlspecialchars(t('next_button', 'Next')); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
