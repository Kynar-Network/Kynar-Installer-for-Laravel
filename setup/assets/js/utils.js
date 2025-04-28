"use strict";


function dynamicReplace(template, data) {
    return template.replace(/:\w+/g, match => {
        const key = match.slice(1); // Remove the ":" from the match
        return key in data ? data[key] : match; // Replace if found, else keep original
    });
}

function formatTime(seconds) {
    if (seconds < 60) {
        return `${Math.round(seconds)}s`;
    }
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.round(seconds % 60);
    return `${minutes}m ${remainingSeconds}s`;
}
