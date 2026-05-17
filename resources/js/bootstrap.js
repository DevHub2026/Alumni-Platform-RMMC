/**
 * Bootstrap JavaScript Setup
 * 
 * This file sets up Axios for making HTTP requests from the frontend.
 * Axios is a JavaScript HTTP client that simplifies API calls.
 * 
 * Configuration:
 * - Sets up global Axios instance available as window.axios
 * - Adds 'X-Requested-With' header to identify AJAX requests
 * - Used by Alpine.js components and Blade templates for API communication
 */

import axios from 'axios';

// Make axios globally available in all templates and components
window.axios = axios;

// Add header to identify this as an AJAX request
// Laravel uses this to return JSON instead of HTML for API responses
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';