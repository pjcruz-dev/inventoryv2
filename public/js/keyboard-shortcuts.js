/**
 * Keyboard Shortcuts System
 * Provides comprehensive keyboard shortcut functionality
 */
class KeyboardShortcutManager {
    constructor() {
        this.shortcuts = new Map();
        this.context = 'global';
        this.isEnabled = true;
        this.helpVisible = false;
        this.currentElement = null;
        this.lastAction = null;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupGlobalShortcuts();
        this.loadUserPreferences();
        this.loadShortcuts(); // Load shortcuts after setting up basic functionality
    }
    
    async loadShortcuts() {
        try {
            const response = await fetch('/api/keyboard-shortcuts/user');
            const data = await response.json();
            
            if (data.success) {
                this.registerShortcuts(data.shortcuts);
            }
        } catch (error) {
            console.error('Error loading shortcuts:', error);
            // Fallback to default shortcuts if API fails
            this.loadDefaultShortcuts();
        }
    }
    
    loadDefaultShortcuts() {
        // Load default shortcuts if API is not available
        const defaultShortcuts = {
            'ctrl+k': {
                action: 'focus_search',
                description: 'Focus search bar',
                category: 'Navigation'
            },
            'ctrl+/': {
                action: 'show_shortcuts',
                description: 'Show keyboard shortcuts',
                category: 'Help'
            },
            'ctrl+alt+d': {
                action: 'toggle_dark_mode',
                description: 'Toggle dark mode',
                category: 'Interface'
            },
            'escape': {
                action: 'close_modals',
                description: 'Close modals and dropdowns',
                category: 'Navigation'
            },
            'ctrl+n': {
                action: 'create_asset',
                description: 'Create new asset',
                category: 'Assets'
            },
            'ctrl+f': {
                action: 'focus_asset_search',
                description: 'Focus asset search',
                category: 'Assets'
            },
            'ctrl+e': {
                action: 'export_assets',
                description: 'Export assets',
                category: 'Assets'
            },
            'ctrl+q': {
                action: 'open_qr_scanner',
                description: 'Open QR scanner',
                category: 'Assets'
            }
        };
        
        this.registerShortcuts(defaultShortcuts);
        console.log('Loaded default keyboard shortcuts');
    }
    
    registerShortcuts(shortcuts) {
        this.shortcuts.clear();
        
        for (const [key, shortcut] of Object.entries(shortcuts)) {
            this.shortcuts.set(key, shortcut);
        }
    }
    
    setupEventListeners() {
        // Global keyboard event listener
        document.addEventListener('keydown', (e) => {
            if (!this.isEnabled) return;
            
            // Skip if user is typing in input fields
            if (this.isTyping(e.target)) return;
            
            const key = this.getKeyString(e);
            console.log('Key detected:', key, 'Event:', e);
            const shortcut = this.shortcuts.get(key);
            
            if (shortcut) {
                console.log('Shortcut found:', shortcut);
                e.preventDefault();
                this.executeShortcut(shortcut, key);
            }
        });
        
        // Context change detection
        this.detectContextChange();
        
        // Element focus tracking
        document.addEventListener('focusin', (e) => {
            this.currentElement = e.target;
        });
        
        document.addEventListener('focusout', (e) => {
            this.currentElement = null;
        });
    }
    
    setupGlobalShortcuts() {
        // Always available shortcuts
        this.shortcuts.set('ctrl+/', {
            action: 'show_shortcuts',
            description: 'Show keyboard shortcuts',
            category: 'Help'
        });
        
        this.shortcuts.set('escape', {
            action: 'close_modals',
            description: 'Close modals and dropdowns',
            category: 'Navigation'
        });
    }
    
    getKeyString(event) {
        const keys = [];
        
        if (event.ctrlKey) keys.push('ctrl');
        if (event.shiftKey) keys.push('shift');
        if (event.altKey) keys.push('alt');
        if (event.metaKey) keys.push('meta');
        
        // Handle special keys
        const specialKeys = {
            'ArrowUp': 'arrow_up',
            'ArrowDown': 'arrow_down',
            'ArrowLeft': 'arrow_left',
            'ArrowRight': 'arrow_right',
            'Escape': 'escape',
            'Enter': 'enter',
            'Space': 'space',
            'Tab': 'tab',
            'Backspace': 'backspace',
            'Delete': 'delete',
            'Home': 'home',
            'End': 'end',
            'PageUp': 'pageup',
            'PageDown': 'pagedown'
        };
        
        if (specialKeys[event.key]) {
            keys.push(specialKeys[event.key]);
        } else if (event.key.length === 1) {
            keys.push(event.key.toLowerCase());
        }
        
        return keys.join('+');
    }
    
    isTyping(element) {
        const inputTypes = ['input', 'textarea', 'select'];
        const contentEditable = element.contentEditable === 'true';
        const isInput = inputTypes.includes(element.tagName.toLowerCase());
        
        return isInput || contentEditable;
    }
    
    async executeShortcut(shortcut, key) {
        try {
            // Show visual feedback
            this.showShortcutFeedback(key, shortcut.description);
            
            // Execute the action
            await this.performAction(shortcut.action, shortcut);
            
            // Log the action
            this.lastAction = {
                key: key,
                action: shortcut.action,
                description: shortcut.description,
                timestamp: new Date()
            };
            
        } catch (error) {
            console.error('Error executing shortcut:', error);
            this.showError('Failed to execute shortcut: ' + error.message);
        }
    }
    
    async performAction(action, shortcut) {
        switch (action) {
            case 'focus_search':
                this.focusSearch();
                break;
                
            case 'show_shortcuts':
                this.showShortcuts();
                break;
                
            case 'toggle_dark_mode':
                this.toggleDarkMode();
                break;
                
            case 'close_modals':
                this.closeModals();
                break;
                
            case 'create_asset':
                this.createAsset();
                break;
                
            case 'focus_asset_search':
                this.focusAssetSearch();
                break;
                
            case 'toggle_asset_filters':
                this.toggleAssetFilters();
                break;
                
            case 'export_assets':
                this.exportAssets();
                break;
                
            case 'print_asset_labels':
                this.printAssetLabels();
                break;
                
            case 'open_qr_scanner':
                this.openQRScanner();
                break;
                
            case 'go_to_dashboard':
                this.navigateTo('/dashboard');
                break;
                
            case 'go_to_assets':
                this.navigateTo('/assets');
                break;
                
            case 'go_to_users':
                this.navigateTo('/users');
                break;
                
            case 'go_to_departments':
                this.navigateTo('/departments');
                break;
                
            case 'go_to_categories':
                this.navigateTo('/asset-categories');
                break;
                
            case 'go_to_vendors':
                this.navigateTo('/vendors');
                break;
                
            case 'go_to_search':
                this.navigateTo('/search');
                break;
                
            case 'go_to_qr_scanner':
                this.navigateTo('/qr-scanner');
                break;
                
            case 'navigate_up':
                this.navigateTable('up');
                break;
                
            case 'navigate_down':
                this.navigateTable('down');
                break;
                
            case 'go_to_first_row':
                this.navigateTable('first');
                break;
                
            case 'go_to_last_row':
                this.navigateTable('last');
                break;
                
            case 'select_all':
                this.selectAll();
                break;
                
            case 'deselect_all':
                this.deselectAll();
                break;
                
            case 'save_form':
                this.saveForm();
                break;
                
            case 'save_and_new':
                this.saveAndNew();
                break;
                
            case 'submit_form':
                this.submitForm();
                break;
                
            case 'cancel_form':
                this.cancelForm();
                break;
                
            case 'new_asset_modal':
                this.openNewAssetModal();
                break;
                
            case 'edit_asset_modal':
                this.openEditAssetModal();
                break;
                
            case 'assign_asset_modal':
                this.openAssignAssetModal();
                break;
                
            default:
                console.warn('Unknown shortcut action:', action);
        }
    }
    
    // Action implementations
    focusSearch() {
        const searchInput = document.getElementById('globalSearchInput');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    showShortcuts() {
        window.location.href = '/keyboard-shortcuts';
    }
    
    toggleDarkMode() {
        console.log('Toggle dark mode shortcut triggered');
        // Use the existing theme toggle button
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            console.log('Using theme toggle button');
            themeToggle.click();
        } else {
            console.log('Using fallback theme toggle');
            // Fallback: manually toggle theme
            const htmlElement = document.documentElement;
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
    }
    
    closeModals() {
        // Close all Bootstrap modals
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        });
        
        // Close dropdowns
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdown.previousElementSibling);
            if (bsDropdown) {
                bsDropdown.hide();
            }
        });
    }
    
    createAsset() {
        console.log('Create asset shortcut triggered');
        if (this.hasPermission('create_assets')) {
            window.location.href = '/assets/create';
        } else {
            this.showError('You do not have permission to create assets');
        }
    }
    
    focusAssetSearch() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    toggleAssetFilters() {
        const filterCollapse = document.getElementById('filterCollapse');
        if (filterCollapse) {
            const bsCollapse = new bootstrap.Collapse(filterCollapse);
            bsCollapse.toggle();
        }
    }
    
    exportAssets() {
        if (this.hasPermission('view_assets')) {
            window.location.href = '/assets/export';
        } else {
            this.showError('You do not have permission to export assets');
        }
    }
    
    printAssetLabels() {
        if (this.hasPermission('view_assets')) {
            window.location.href = '/assets/print-all-labels';
        } else {
            this.showError('You do not have permission to print asset labels');
        }
    }
    
    openQRScanner() {
        if (this.hasPermission('view_assets')) {
            window.location.href = '/qr-scanner';
        } else {
            this.showError('You do not have permission to access QR scanner');
        }
    }
    
    navigateTo(url) {
        window.location.href = url;
    }
    
    navigateTable(direction) {
        const table = document.querySelector('table tbody');
        if (!table) return;
        
        const rows = Array.from(table.querySelectorAll('tr'));
        const currentRow = document.querySelector('tr.table-active');
        
        if (!currentRow) {
            // Select first row
            if (rows.length > 0) {
                this.selectTableRow(rows[0]);
            }
            return;
        }
        
        const currentIndex = rows.indexOf(currentRow);
        let newIndex = currentIndex;
        
        switch (direction) {
            case 'up':
                newIndex = Math.max(0, currentIndex - 1);
                break;
            case 'down':
                newIndex = Math.min(rows.length - 1, currentIndex + 1);
                break;
            case 'first':
                newIndex = 0;
                break;
            case 'last':
                newIndex = rows.length - 1;
                break;
        }
        
        if (newIndex !== currentIndex) {
            this.selectTableRow(rows[newIndex]);
        }
    }
    
    selectTableRow(row) {
        // Remove active class from all rows
        document.querySelectorAll('tr.table-active').forEach(r => {
            r.classList.remove('table-active');
        });
        
        // Add active class to selected row
        row.classList.add('table-active');
        
        // Scroll into view
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    selectAll() {
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        
        // Update select all checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        }
        
        // Show bulk actions
        const bulkActions = document.getElementById('bulkActionsToolbar');
        if (bulkActions) {
            bulkActions.classList.remove('d-none');
        }
    }
    
    deselectAll() {
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Update select all checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
        
        // Hide bulk actions
        const bulkActions = document.getElementById('bulkActionsToolbar');
        if (bulkActions) {
            bulkActions.classList.add('d-none');
        }
    }
    
    saveForm() {
        const form = document.querySelector('form');
        if (form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.click();
            }
        }
    }
    
    saveAndNew() {
        // This would need to be implemented per form
        console.log('Save and new functionality');
    }
    
    submitForm() {
        this.saveForm();
    }
    
    cancelForm() {
        if (confirm('Are you sure you want to cancel? Unsaved changes will be lost.')) {
            window.history.back();
        }
    }
    
    openNewAssetModal() {
        if (this.hasPermission('create_assets')) {
            // This would open a modal for creating assets
            console.log('Open new asset modal');
        } else {
            this.showError('You do not have permission to create assets');
        }
    }
    
    openEditAssetModal() {
        if (this.hasPermission('edit_assets')) {
            // This would open a modal for editing assets
            console.log('Open edit asset modal');
        } else {
            this.showError('You do not have permission to edit assets');
        }
    }
    
    openAssignAssetModal() {
        if (this.hasPermission('edit_assets')) {
            // This would open a modal for assigning assets
            console.log('Open assign asset modal');
        } else {
            this.showError('You do not have permission to assign assets');
        }
    }
    
    // Utility methods
    hasPermission(permission) {
        // This would check user permissions
        // For now, return true
        return true;
    }
    
    detectContextChange() {
        // Detect context based on current URL
        const path = window.location.pathname;
        
        if (path.includes('/assets')) {
            this.context = 'assets';
        } else if (path.includes('/users')) {
            this.context = 'users';
        } else if (path.includes('/search')) {
            this.context = 'search';
        } else {
            this.context = 'global';
        }
        
        // Load context-specific shortcuts
        this.loadContextShortcuts();
    }
    
    async loadContextShortcuts() {
        try {
            const response = await fetch(`/api/keyboard-shortcuts/context?context=${this.context}`);
            const data = await response.json();
            
            if (data.success) {
                this.registerShortcuts(data.shortcuts);
            }
        } catch (error) {
            console.error('Error loading context shortcuts:', error);
        }
    }
    
    showShortcutFeedback(key, description) {
        // Create feedback element
        const feedback = document.createElement('div');
        feedback.className = 'shortcut-feedback';
        feedback.innerHTML = `
            <div class="shortcut-feedback-content">
                <kbd>${key}</kbd>
                <span>${description}</span>
            </div>
        `;
        
        // Add styles
        feedback.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            animation: slideInRight 0.3s ease;
        `;
        
        // Add to document
        document.body.appendChild(feedback);
        
        // Remove after delay
        setTimeout(() => {
            feedback.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(feedback);
            }, 300);
        }, 2000);
    }
    
    showError(message) {
        // Create error element
        const error = document.createElement('div');
        error.className = 'shortcut-error';
        error.innerHTML = `
            <div class="shortcut-error-content">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        // Add styles
        error.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            animation: slideInRight 0.3s ease;
        `;
        
        // Add to document
        document.body.appendChild(error);
        
        // Remove after delay
        setTimeout(() => {
            error.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(error);
            }, 300);
        }, 3000);
    }
    
    loadUserPreferences() {
        // Load user preferences from localStorage
        const preferences = localStorage.getItem('keyboard-shortcuts-preferences');
        if (preferences) {
            const prefs = JSON.parse(preferences);
            this.isEnabled = prefs.enabled !== false;
        }
    }
    
    saveUserPreferences() {
        // Save user preferences to localStorage
        const preferences = {
            enabled: this.isEnabled,
            context: this.context
        };
        localStorage.setItem('keyboard-shortcuts-preferences', JSON.stringify(preferences));
    }
    
    // Public methods
    enable() {
        this.isEnabled = true;
        this.saveUserPreferences();
    }
    
    disable() {
        this.isEnabled = false;
        this.saveUserPreferences();
    }
    
    getLastAction() {
        return this.lastAction;
    }
    
    getShortcuts() {
        return Array.from(this.shortcuts.entries());
    }
}

// Initialize keyboard shortcuts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing keyboard shortcuts...');
    window.keyboardShortcuts = new KeyboardShortcutManager();
    console.log('Keyboard shortcuts initialized:', window.keyboardShortcuts);
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .shortcut-feedback kbd {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        margin-right: 8px;
    }
    
    .table-active {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
`;
document.head.appendChild(style);

