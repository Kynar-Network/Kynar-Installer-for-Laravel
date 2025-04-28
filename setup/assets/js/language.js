"use strict";

function changeLanguage(lang) {
    const path = window.location.pathname;
    const segments = path.split('/').filter(Boolean);
    const setupIndex = segments.indexOf('setup');

    if (setupIndex === -1) {
        console.warn('Not in setup section');
        return;
    }

    // Create new URL structure
    let newSegments = ['setup', lang];

    // Check if we're in a step route
    const stepIndex = segments.indexOf('step');
    if (stepIndex !== -1 && segments[stepIndex + 1]) {
        // Get current step translations using the step ID
        const currentStepData = window.slugTranslations[window.currentStepId];
        const translatedSlug = currentStepData?.translations[lang] || currentStepData?.slug;

        console.log('Translation data:', {
            stepId: window.currentStepId,
            targetLanguage: lang,
            translatedSlug,
            availableTranslations: currentStepData?.translations
        });

        newSegments.push('step', translatedSlug);
    }

    // Preserve query parameters and hash
    const queryString = window.location.search || '';
    const hashString = window.location.hash || '';

    // Build and navigate to new URL
    const newPath = '/' + newSegments.join('/') + queryString + hashString;
    console.log('Navigating to:', newPath);
    window.location.href = newPath;
}

// Single jQuery ready handler for all initializations
$(document).ready(function() {
    // Sort options alphabetically by the English name
    let $select = $('#language-select');
    let options = $select.find('option');

    options.sort((a, b) => {
        const textA = $(a).data('english-name').toUpperCase();
        const textB = $(b).data('english-name').toUpperCase();
        return textA.localeCompare(textB);
    });

    $select.empty().append(options); // Rebuild the <select> with sorted options

    // Initialize Select2
    $('#language-select').select2({
        templateResult: formatState,
        templateSelection: formatState,
        width: 'auto'
    });

    // Add change event handler
    $select.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const lang = selectedOption.val();
        const translatedSlug = selectedOption.data('translated-slug');
        console.log(translatedSlug);
        changeLanguage(lang, translatedSlug);
    });

    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        const $state = $('<span><img class="flag-image" src="' + $(state.element).data('flag-image') + '" alt="' + $(state.element).data('english-name') + ' Flag" />' + $(state.element).data('english-name') + ' (' + $(state.element).data('native-name') + ')</span>');
        return $state;
    }
});
