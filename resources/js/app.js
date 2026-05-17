/**
 * Main Application JavaScript Entry Point
 * 
 * This file initializes the frontend framework and plugins.
 * 
 * Setup:
 * - Imports Bootstrap setup (Axios configuration)
 * - Loads Alpine.js for reactive UI components
 * - Registers Alpine globally for use in Blade templates
 */

// Import Axios configuration for API requests
import './bootstrap';

// Import Alpine.js - a lightweight JavaScript framework for reactive components
import Alpine from 'alpinejs';

// Make Alpine globally available in all Blade templates
window.Alpine = Alpine;

// Initialize Alpine.js - activates all Alpine directives and components on the page
// This scans the DOM for x-data, x-show, x-if, etc. and activates them
Alpine.start();
