"use strict";

function copyToClipboard(elementId, event) {
    const codeElement = document.getElementById(elementId);
    const originalText = codeElement.textContent;

    // Split the text into lines
    const lines = originalText.split('\n');

    // Remove the "$ " prefix from each line
    const cleanedLines = lines.map(line => line.replace(/^\$\s*/, ''));

    // Join the cleaned lines back into a single string
    const textToCopy = cleanedLines.join('\n');

    // Use the Clipboard API to copy the text
    navigator.clipboard.writeText(textToCopy)
        .then(() => {
            const button = event.target;
            button.textContent = 'Copied';
            button.classList.add('copied');

            setTimeout(() => {
                button.textContent = 'Copy';
                button.classList.remove('copied');
            }, 5000);
        })
        .catch(err => {
            console.error('Unable to copy to clipboard', err);
        });
}


