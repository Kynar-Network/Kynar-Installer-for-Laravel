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

/**
 * Perform fetch request with SweetAlert handling
 * @param {string} url - The endpoint URL
 * @param {string} method - HTTP method (GET, POST, etc.)
 * @param {object} headers - Request headers
 * @param {object} params - URL parameters
 * @returns {Promise<boolean>} Returns true if successful, false otherwise
 */
window.performFetch = async function performFetch(url, method = 'POST', headers = {}, params = {}) {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                ...headers
            },
            body: new URLSearchParams(params).toString()
        });

        // Check if response is OK
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        const responseText = await response.text();

        // Handle empty response
        if (!responseText.trim()) {
            console.warn('Empty response received from server');
            return {
                success: false,
                data: {
                    message: 'Server returned empty response',
                    error: 'No content'
                }
            };
        }

        // Try to parse JSON
        if (contentType && contentType.includes('application/json')) {
            try {
                const data = JSON.parse(responseText);
                return { success: true, data };
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                return {
                    success: false,
                    data: {
                        message: 'Invalid JSON response',
                        error: responseText
                    }
                };
            }
        }

        // Handle non-JSON response
        console.error('Non-JSON response:', responseText);
        return {
            success: false,
            data: {
                message: 'Server returned non-JSON response',
                error: responseText
            }
        };
    } catch (error) {
        console.error("Fetch error:", error.message);
        return {
            success: false,
            data: {
                message: 'Request failed',
                error: error.message
            }
        };
    }
}
