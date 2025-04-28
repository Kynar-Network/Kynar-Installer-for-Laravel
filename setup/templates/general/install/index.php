
<?php $this->section('title') ?>
<?php echo t('installation_wizard'); ?>
<?php $this->endSection('title') ?>

<?php $this->section('header') ?>
<h1><?php echo t('installation_process'); ?></h1>

<?php if ($settings['show_progress_bar']): ?>
<div class="progress-bar">
    <?php
    $totalSteps = count($steps);
    $currentStepIndex = array_search($currentStep, array_column($steps, 'id'));
    $progress = ($currentStepIndex + 1) / $totalSteps * 100;
    ?>
    <div class="progress" style="width: <?php echo $progress; ?>%"></div>
</div>
<?php endif; ?>
<?php $this->endSection('header') ?>

<div class="installation-steps">
    <?php
    $currentStepData = null;
    foreach ($steps as $step) {
        if ($step['id'] === $currentStep) {
            $currentStepData = $step;
            break;
        }
    }

    if ($currentStepData):
        // Prepare data for the step template
        $stepTemplateData = [
            'stepsConfig' => ['steps' => $steps],
            'currentStep' => $currentStep,
            'isFirstStep' => $currentStepIndex === 0,
            'nextStepFile' => $currentStepIndex < count($steps) - 1 ? 'step/' . $steps[$currentStepIndex + 1]['id'] : null,
            'prevStepFile' => $currentStepIndex > 0 ? 'step/' . $steps[$currentStepIndex - 1]['id'] : null
        ];
        $this->includePath($currentStepData['template'], $stepTemplateData);
    endif;
    ?>
</div>
