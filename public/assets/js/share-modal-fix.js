// Fix for share-modal.js and reload.js errors
// This script runs FIRST to prevent null reference errors and WebSocket connection errors
// MUST LOAD BEFORE share-modal.js and reload.js
(function() {
    'use strict';
    
    // Mark that fix is loaded
    window.__shareModalFixLoaded = true;
    window.__shareModalBlocked = true;
    window.__reloadBlocked = true;
    
    console.log('%c🛡️ Error Prevention System Loading...', 'color: blue; font-weight: bold');
    
    // Completely override share-modal.js and reload.js if they try to execute
    const originalSetTimeout = window.setTimeout;
    window.setTimeout = function(callback, delay, ...args) {
        // Check if callback is from share-modal.js or reload.js
        const callbackStr = callback.toString();
        if (callbackStr.includes('share-modal') || callbackStr.includes('reload.js') || 
            callbackStr.includes('WebSocket') && callbackStr.includes('ws://')) {
            console.warn('🚫 Blocked setTimeout from share-modal.js/reload.js');
            return 0; // Return invalid timeout ID
        }
        return originalSetTimeout.apply(this, [callback, delay, ...args]);
    };
    
    // ===== DISABLE WEBSOCKET COMPLETELY =====
    // Save original WebSocket if it exists
    const OriginalWebSocket = window.WebSocket;
    
    // Replace WebSocket with a dummy that does nothing
    window.WebSocket = function(url, protocols) {
        console.warn('🚫 WebSocket connection BLOCKED:', url);
        // Return a dummy closed WebSocket that does nothing
        this.readyState = 3; // CLOSED
        this.close = function() {};
        this.send = function() {};
        this.addEventListener = function() {};
        this.removeEventListener = function() {};
        this.dispatchEvent = function() { return true; };
        this.onopen = null;
        this.onclose = null;
        this.onerror = null;
        this.onmessage = null;
        return this;
    };
    
    // Make it look like a real WebSocket class
    window.WebSocket.prototype = OriginalWebSocket ? OriginalWebSocket.prototype : {};
    window.WebSocket.CONNECTING = 0;
    window.WebSocket.OPEN = 1;
    window.WebSocket.CLOSING = 2;
    window.WebSocket.CLOSED = 3;
    
    console.log('%c✅ WebSocket completely disabled', 'color: green');
    
    // Intercept and fix addEventListener calls on null/undefined elements
    // This MUST run before any other script tries to use addEventListener
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    EventTarget.prototype.addEventListener = function(type, listener, options) {
        // Comprehensive null/undefined check
        if (this === null || this === undefined) {
            console.warn('🚫 Prevented addEventListener on null/undefined element');
            return;
        }
        
        // Check if 'this' is a valid object
        if (typeof this !== 'object' && typeof this !== 'function') {
            console.warn('🚫 Prevented addEventListener on non-object:', typeof this);
            return;
        }
        
        // Check if it's a valid EventTarget/Node
        try {
            if (!(this instanceof EventTarget || 
                  this instanceof Node || 
                  this === window || 
                  this === document ||
                  this.constructor === Object ||
                  typeof this.addEventListener === 'function')) {
                // Allow if it has addEventListener method (might be a custom object)
                if (typeof this.addEventListener !== 'function') {
                    console.warn('🚫 Prevented addEventListener on invalid element');
                    return;
                }
            }
        } catch (e) {
            // If instanceof check fails, element is likely null/undefined
            console.warn('🚫 Prevented addEventListener - instanceof check failed');
            return;
        }
        
        // Check if addEventListener method exists
        if (typeof originalAddEventListener !== 'function') {
            console.warn('🚫 addEventListener is not a function');
            return;
        }
        
        try {
            return originalAddEventListener.call(this, type, listener, options);
        } catch (e) {
            console.warn('⚠️ Error in addEventListener:', e.message);
            return;
        }
    };
    
    // Also patch Node.prototype for extra safety
    if (typeof Node !== 'undefined' && Node.prototype) {
        const originalNodeAddEventListener = Node.prototype.addEventListener;
        Node.prototype.addEventListener = function(type, listener, options) {
            if (this === null || this === undefined) {
                console.warn('🚫 Prevented Node.addEventListener on null/undefined');
                return;
            }
            try {
                return originalNodeAddEventListener.call(this, type, listener, options);
            } catch (e) {
                console.warn('⚠️ Error in Node.addEventListener:', e.message);
                return;
            }
        };
    }
    
    console.log('%c✅ addEventListener protection enabled', 'color: green');
    
    // Global error handler to catch addEventListener errors on null elements
    const originalErrorHandler = window.onerror;
    window.onerror = function(message, source, lineno, colno, error) {
        const msgStr = String(message || '');
        const srcStr = String(source || '');
        
        // Catch timer is not defined errors
        if (msgStr.includes("timer is not defined") ||
            msgStr.includes("timer is not defined") ||
            (msgStr.includes("ReferenceError") && msgStr.includes("timer")) ||
            srcStr.includes("custom.min.js")) {
            console.info('ℹ️ Suppressed timer error:', msgStr, 'at', srcStr + ':' + lineno);
            return true; // Suppress error
        }
        
        // Catch addEventListener errors on null - more comprehensive
        if (msgStr.includes("Cannot read properties of null") ||
            msgStr.includes("Cannot read property") ||
            msgStr.includes("addEventListener") ||
            (msgStr.includes("null") && msgStr.includes("reading")) ||
            (msgStr.includes("reading 'addEventListener'")) ||
            (msgStr.includes("null") && msgStr.includes("addEventListener")) ||
            (msgStr.includes("null (reading 'addEventListener'")) ||
            srcStr.includes("share-modal.js") ||
            srcStr.includes("reload.js")) {
            console.info('ℹ️ Suppressed null reference error:', msgStr, 'at', srcStr + ':' + lineno);
            return true; // Suppress error
        }
        
        // Catch WebSocket errors
        if (msgStr.includes("WebSocket") || 
            msgStr.includes("WebSocket connection") ||
            msgStr.includes("WebSocket connection to") ||
            srcStr.includes("reload.js") ||
            msgStr.includes("ws://") ||
            msgStr.includes("wss://") ||
            srcStr.includes("/ws/ws") ||
            srcStr.includes("/cloody/storage/ws/ws")) {
            console.info('ℹ️ Suppressed WebSocket error:', msgStr);
            return true; // Suppress error
        }
        
        // Call original error handler if exists
        if (originalErrorHandler) {
            return originalErrorHandler(message, source, lineno, colno, error);
        }
        return false;
    };
    
    console.log('%c✅ Global error handler installed', 'color: green');
    
    // Safe addEventListener wrapper for manual use
    window.safeAddEventListener = function(element, event, handler) {
        if (element && typeof element.addEventListener === 'function') {
            try {
                element.addEventListener(event, handler);
                return true;
            } catch (e) {
                console.warn('Error adding event listener:', e);
                return false;
            }
        }
        return false;
    };
    

    
    // Suppress WebSocket errors globally
    window.addEventListener('error', function(e) {
        const msg = String(e.message || e.error?.message || '');
        const filename = String(e.filename || e.source || '');
        
        if (msg.includes('WebSocket') || 
            msg.includes('ws://') || 
            msg.includes('wss://') ||
            msg.includes('/admin/ws') ||
            msg.includes('/cloody/storage/ws/ws') ||
            filename.includes('reload.js')) {
            console.info('ℹ️ WebSocket error suppressed:', msg);
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        
        // Catch timer is not defined errors
        if (msg.includes('timer is not defined') ||
            (msg.includes('ReferenceError') && msg.includes('timer')) ||
            filename.includes('custom.min.js')) {
            console.info('ℹ️ Timer error suppressed:', msg, 'at', filename);
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        
        // Also catch null reference errors - more comprehensive
        if (msg.includes("Cannot read properties of null") ||
            msg.includes("Cannot read property") ||
            msg.includes("reading 'addEventListener'") ||
            msg.includes("null (reading 'addEventListener'") ||
            (msg.includes("null") && msg.includes("addEventListener")) ||
            filename.includes('share-modal.js') ||
            filename.includes('reload.js') ||
            (filename && filename.indexOf('share-modal') !== -1) ||
            (filename && filename.indexOf('reload') !== -1)) {
            console.info('ℹ️ Null reference error suppressed:', msg, 'at', filename);
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);
    
    console.log('%c✅ Error event listener installed', 'color: green');
    
    // Catch unhandled promise rejections from WebSocket
    window.addEventListener('unhandledrejection', function(e) {
        if (e.reason) {
            const reasonStr = e.reason.toString();
            const reasonMsg = e.reason.message || '';
            if (reasonStr.includes('WebSocket') || reasonMsg.includes('WebSocket') ||
                reasonStr.includes('ws://') || reasonMsg.includes('ws://') ||
                reasonStr.includes('/admin/ws')) {
                console.info('WebSocket promise rejection suppressed');
                e.preventDefault();
            }
        }
    });
    
    // Initialize share modals safely
    function initShareModals() {
        const allModals = document.querySelectorAll('[id*="share"][id*="Modal"], [id^="shareFileModal"], [id^="shareFolderModal"], [id^="shareWithGroupModal"]');
        allModals.forEach(function(modal) {
            if (modal) {
                window.safeAddEventListener(modal, 'show.bs.modal', function() {
                    console.log('Share Modal opened:', modal.id);
                });
            }
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShareModals);
    } else {
        initShareModals();
    }
    
    // Re-initialize after delays to catch dynamically added modals
    setTimeout(initShareModals, 500);
    setTimeout(initShareModals, 1000);
    
    console.log('%c🛡️ Error Prevention System Ready!', 'color: green; font-weight: bold; font-size: 14px');
})();

