/**
 * Searchable Dropdown Component
 * A reusable JavaScript component that adds search functionality to any select element
 * 
 * Usage:
 * 1. Include this script in your page
 * 2. Call SearchableDropdown.init(selector, options) on your select elements
 * 
 * Features:
 * - Case-insensitive search
 * - Partial matching
 * - Real-time filtering
 * - Keyboard navigation
 * - Maintains original select behavior
 * - Customizable styling
 */

class SearchableDropdown {
    constructor(selectElement, options = {}) {
        this.select = selectElement;
        this.options = {
            placeholder: options.placeholder || 'Search...',
            noResultsText: options.noResultsText || 'No results found',
            searchInputClass: options.searchInputClass || 'form-control',
            dropdownClass: options.dropdownClass || 'searchable-dropdown',
            maxHeight: options.maxHeight || '200px',
            allowClear: options.allowClear !== false,
            ...options
        };
        
        this.isOpen = false;
        this.selectedIndex = -1;
        this.filteredOptions = [];
        
        this.init();
    }
    
    init() {
        // Hide original select
        this.select.style.display = 'none';
        
        // Create wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = `${this.options.dropdownClass} position-relative`;
        this.select.parentNode.insertBefore(this.wrapper, this.select);
        this.wrapper.appendChild(this.select);
        
        // Create search input
        this.createSearchInput();
        
        // Create dropdown menu
        this.createDropdownMenu();
        
        // Populate options
        this.populateOptions();
        
        // Set initial value
        this.updateDisplayValue();
        
        // Bind events
        this.bindEvents();
    }
    
    createSearchInput() {
        this.searchInput = document.createElement('input');
        this.searchInput.type = 'text';
        this.searchInput.className = this.options.searchInputClass;
        this.searchInput.placeholder = this.options.placeholder;
        this.searchInput.autocomplete = 'off';
        this.searchInput.readOnly = false;
        
        // Copy classes from original select (except form-select)
        const selectClasses = this.select.className.split(' ').filter(cls => 
            cls !== 'form-select' && cls !== 'form-control'
        );
        if (selectClasses.length > 0) {
            this.searchInput.className += ' ' + selectClasses.join(' ');
        }
        
        // Add dropdown toggle styling
        this.searchInput.style.cursor = 'pointer';
        this.searchInput.style.backgroundImage = 'url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath fill=\'none\' stroke=\'%23343a40\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'m1 6 7 7 7-7\'/%3e%3c/svg%3e")';
        this.searchInput.style.backgroundRepeat = 'no-repeat';
        this.searchInput.style.backgroundPosition = 'right 0.75rem center';
        this.searchInput.style.backgroundSize = '16px 12px';
        this.searchInput.style.paddingRight = '2.25rem';
        
        this.wrapper.appendChild(this.searchInput);
    }
    
    createDropdownMenu() {
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'dropdown-menu w-100';
        this.dropdown.style.maxHeight = this.options.maxHeight;
        this.dropdown.style.overflowY = 'auto';
        this.dropdown.style.display = 'none';
        this.dropdown.style.position = 'absolute';
        this.dropdown.style.top = '100%';
        this.dropdown.style.left = '0';
        this.dropdown.style.right = '0';
        this.dropdown.style.zIndex = '1000';
        
        this.wrapper.appendChild(this.dropdown);
    }
    
    populateOptions() {
        this.originalOptions = Array.from(this.select.options).map(option => ({
            value: option.value,
            text: option.textContent.trim(),
            selected: option.selected,
            disabled: option.disabled,
            element: option
        }));
        
        this.filteredOptions = [...this.originalOptions];
        this.renderOptions();
    }
    
    renderOptions() {
        this.dropdown.innerHTML = '';
        
        if (this.filteredOptions.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'dropdown-item-text text-muted';
            noResults.textContent = this.options.noResultsText;
            this.dropdown.appendChild(noResults);
            return;
        }
        
        this.filteredOptions.forEach((option, index) => {
            const item = document.createElement('a');
            item.className = 'dropdown-item';
            item.href = '#';
            item.textContent = option.text;
            item.dataset.value = option.value;
            item.dataset.index = index;
            
            if (option.selected) {
                item.classList.add('active');
            }
            
            if (option.disabled) {
                item.classList.add('disabled');
            }
            
            this.dropdown.appendChild(item);
        });
    }
    
    bindEvents() {
        // Search input events
        this.searchInput.addEventListener('input', (e) => this.handleSearch(e));
        this.searchInput.addEventListener('focus', (e) => this.handleFocus(e));
        this.searchInput.addEventListener('blur', (e) => this.handleBlur(e));
        this.searchInput.addEventListener('keydown', (e) => this.handleKeydown(e));
        this.searchInput.addEventListener('click', (e) => this.handleClick(e));
        
        // Dropdown events
        this.dropdown.addEventListener('click', (e) => this.handleDropdownClick(e));
        
        // Document click to close dropdown
        document.addEventListener('click', (e) => this.handleDocumentClick(e));
        
        // Original select change event
        this.select.addEventListener('change', () => this.updateDisplayValue());
    }
    
    handleSearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        if (searchTerm === '') {
            this.filteredOptions = [...this.originalOptions];
        } else {
            this.filteredOptions = this.originalOptions.filter(option => 
                option.text.toLowerCase().includes(searchTerm)
            );
        }
        
        this.renderOptions();
        this.selectedIndex = -1;
        
        if (!this.isOpen) {
            this.openDropdown();
        }
    }
    
    handleFocus(e) {
        this.openDropdown();
    }
    
    handleBlur(e) {
        // Delay to allow dropdown click to register
        setTimeout(() => {
            if (!this.dropdown.contains(document.activeElement)) {
                this.closeDropdown();
                this.updateDisplayValue();
            }
        }, 150);
    }
    
    handleClick(e) {
        if (this.isOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    handleKeydown(e) {
        if (!this.isOpen) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
                e.preventDefault();
                this.openDropdown();
            }
            return;
        }
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.navigateDown();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.navigateUp();
                break;
            case 'Enter':
                e.preventDefault();
                this.selectCurrentOption();
                break;
            case 'Escape':
                e.preventDefault();
                this.closeDropdown();
                this.updateDisplayValue();
                break;
        }
    }
    
    handleDropdownClick(e) {
        e.preventDefault();
        
        if (e.target.classList.contains('dropdown-item') && !e.target.classList.contains('disabled')) {
            const value = e.target.dataset.value;
            this.selectOption(value);
            this.closeDropdown();
        }
    }
    
    handleDocumentClick(e) {
        if (!this.wrapper.contains(e.target)) {
            this.closeDropdown();
            this.updateDisplayValue();
        }
    }
    
    navigateDown() {
        if (this.filteredOptions.length === 0) return;
        
        this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredOptions.length - 1);
        this.updateHighlight();
    }
    
    navigateUp() {
        if (this.filteredOptions.length === 0) return;
        
        this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
        this.updateHighlight();
    }
    
    updateHighlight() {
        const items = this.dropdown.querySelectorAll('.dropdown-item');
        items.forEach((item, index) => {
            item.classList.toggle('active', index === this.selectedIndex);
        });
        
        // Scroll to highlighted item
        if (this.selectedIndex >= 0 && items[this.selectedIndex]) {
            items[this.selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    selectCurrentOption() {
        if (this.selectedIndex >= 0 && this.filteredOptions[this.selectedIndex]) {
            const option = this.filteredOptions[this.selectedIndex];
            this.selectOption(option.value);
            this.closeDropdown();
        }
    }
    
    selectOption(value) {
        // Update original select
        this.select.value = value;
        
        // Trigger change event
        const changeEvent = new Event('change', { bubbles: true });
        this.select.dispatchEvent(changeEvent);
        
        // Update display
        this.updateDisplayValue();
        
        // Update active state in dropdown
        this.dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.toggle('active', item.dataset.value === value);
        });
    }
    
    updateDisplayValue() {
        const selectedOption = this.originalOptions.find(option => option.value === this.select.value);
        this.searchInput.value = selectedOption ? selectedOption.text : '';
    }
    
    openDropdown() {
        this.isOpen = true;
        this.dropdown.style.display = 'block';
        this.dropdown.classList.add('show');
        this.searchInput.style.cursor = 'text';
        
        // Reset search if opening
        if (this.searchInput.value && this.filteredOptions.length !== this.originalOptions.length) {
            this.filteredOptions = [...this.originalOptions];
            this.renderOptions();
        }
    }
    
    closeDropdown() {
        this.isOpen = false;
        this.dropdown.style.display = 'none';
        this.dropdown.classList.remove('show');
        this.searchInput.style.cursor = 'pointer';
        this.selectedIndex = -1;
    }
    
    // Public methods
    refresh() {
        this.populateOptions();
        this.updateDisplayValue();
    }
    
    destroy() {
        this.wrapper.parentNode.insertBefore(this.select, this.wrapper);
        this.wrapper.remove();
        this.select.style.display = '';
    }
    
    // Static method to initialize multiple dropdowns
    static init(selector, options = {}) {
        const elements = typeof selector === 'string' ? 
            document.querySelectorAll(selector) : 
            [selector];
        
        const instances = [];
        
        elements.forEach(element => {
            if (element.tagName === 'SELECT' && !element.dataset.searchableDropdown) {
                element.dataset.searchableDropdown = 'true';
                instances.push(new SearchableDropdown(element, options));
            }
        });
        
        return instances.length === 1 ? instances[0] : instances;
    }
    
    // Static method to initialize all select elements with a specific class
    static initAll(className = 'searchable-select', options = {}) {
        return SearchableDropdown.init(`.${className}`, options);
    }
}

// Auto-initialize dropdowns with 'searchable-select' class when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    SearchableDropdown.initAll();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SearchableDropdown;
}

// Global access
window.SearchableDropdown = SearchableDropdown;