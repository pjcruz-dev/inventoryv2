<?php

namespace App\Services;

class KeyboardShortcutService
{
    /**
     * Get all available keyboard shortcuts
     */
    public function getShortcuts()
    {
        return [
            'global' => [
                'ctrl+k' => [
                    'action' => 'focus_search',
                    'description' => 'Focus search bar',
                    'category' => 'Navigation'
                ],
                'ctrl+/' => [
                    'action' => 'show_shortcuts',
                    'description' => 'Show keyboard shortcuts',
                    'category' => 'Help'
                ],
                'ctrl+alt+d' => [
                    'action' => 'toggle_dark_mode',
                    'description' => 'Toggle dark mode',
                    'category' => 'Interface'
                ],
                'escape' => [
                    'action' => 'close_modals',
                    'description' => 'Close modals and dropdowns',
                    'category' => 'Navigation'
                ]
            ],
            'assets' => [
                'ctrl+n' => [
                    'action' => 'create_asset',
                    'description' => 'Create new asset',
                    'category' => 'Assets',
                    'permission' => 'create_assets'
                ],
                'ctrl+f' => [
                    'action' => 'focus_asset_search',
                    'description' => 'Focus asset search',
                    'category' => 'Assets'
                ],
                'ctrl+shift+f' => [
                    'action' => 'toggle_asset_filters',
                    'description' => 'Toggle asset filters',
                    'category' => 'Assets'
                ],
                'ctrl+e' => [
                    'action' => 'export_assets',
                    'description' => 'Export assets',
                    'category' => 'Assets',
                    'permission' => 'view_assets'
                ],
                'ctrl+shift+p' => [
                    'action' => 'print_asset_labels',
                    'description' => 'Print asset labels',
                    'category' => 'Assets',
                    'permission' => 'view_assets'
                ],
                'ctrl+shift+q' => [
                    'action' => 'open_qr_scanner',
                    'description' => 'Open QR scanner',
                    'category' => 'Assets',
                    'permission' => 'view_assets'
                ]
            ],
            'navigation' => [
                'ctrl+1' => [
                    'action' => 'go_to_dashboard',
                    'description' => 'Go to Dashboard',
                    'category' => 'Navigation'
                ],
                'ctrl+2' => [
                    'action' => 'go_to_assets',
                    'description' => 'Go to Assets',
                    'category' => 'Navigation'
                ],
                'ctrl+3' => [
                    'action' => 'go_to_users',
                    'description' => 'Go to Users',
                    'category' => 'Navigation'
                ],
                'ctrl+4' => [
                    'action' => 'go_to_departments',
                    'description' => 'Go to Departments',
                    'category' => 'Navigation'
                ],
                'ctrl+5' => [
                    'action' => 'go_to_categories',
                    'description' => 'Go to Categories',
                    'category' => 'Navigation'
                ],
                'ctrl+6' => [
                    'action' => 'go_to_vendors',
                    'description' => 'Go to Vendors',
                    'category' => 'Navigation'
                ],
                'ctrl+7' => [
                    'action' => 'go_to_search',
                    'description' => 'Go to Search',
                    'category' => 'Navigation'
                ],
                'ctrl+8' => [
                    'action' => 'go_to_qr_scanner',
                    'description' => 'Go to QR Scanner',
                    'category' => 'Navigation'
                ]
            ],
            'table' => [
                'arrow_up' => [
                    'action' => 'navigate_up',
                    'description' => 'Navigate up in table',
                    'category' => 'Table'
                ],
                'arrow_down' => [
                    'action' => 'navigate_down',
                    'description' => 'Navigate down in table',
                    'category' => 'Table'
                ],
                'home' => [
                    'action' => 'go_to_first_row',
                    'description' => 'Go to first row',
                    'category' => 'Table'
                ],
                'end' => [
                    'action' => 'go_to_last_row',
                    'description' => 'Go to last row',
                    'category' => 'Table'
                ],
                'ctrl+a' => [
                    'action' => 'select_all',
                    'description' => 'Select all items',
                    'category' => 'Table'
                ],
                'ctrl+shift+a' => [
                    'action' => 'deselect_all',
                    'description' => 'Deselect all items',
                    'category' => 'Table'
                ]
            ],
            'forms' => [
                'ctrl+s' => [
                    'action' => 'save_form',
                    'description' => 'Save form',
                    'category' => 'Forms'
                ],
                'ctrl+shift+s' => [
                    'action' => 'save_and_new',
                    'description' => 'Save and create new',
                    'category' => 'Forms'
                ],
                'ctrl+enter' => [
                    'action' => 'submit_form',
                    'description' => 'Submit form',
                    'category' => 'Forms'
                ],
                'escape' => [
                    'action' => 'cancel_form',
                    'description' => 'Cancel form',
                    'category' => 'Forms'
                ]
            ],
            'modals' => [
                'ctrl+shift+n' => [
                    'action' => 'new_asset_modal',
                    'description' => 'Open new asset modal',
                    'category' => 'Modals',
                    'permission' => 'create_assets'
                ],
                'ctrl+shift+e' => [
                    'action' => 'edit_asset_modal',
                    'description' => 'Open edit asset modal',
                    'category' => 'Modals',
                    'permission' => 'edit_assets'
                ],
                'ctrl+shift+a' => [
                    'action' => 'assign_asset_modal',
                    'description' => 'Open assign asset modal',
                    'category' => 'Modals',
                    'permission' => 'edit_assets'
                ]
            ]
        ];
    }

    /**
     * Get shortcuts for a specific context
     */
    public function getShortcutsForContext($context)
    {
        $allShortcuts = $this->getShortcuts();
        $contextShortcuts = [];

        foreach ($allShortcuts as $category => $shortcuts) {
            if ($category === $context || $category === 'global') {
                $contextShortcuts = array_merge($contextShortcuts, $shortcuts);
            }
        }

        return $contextShortcuts;
    }

    /**
     * Get shortcuts grouped by category
     */
    public function getShortcutsByCategory()
    {
        $allShortcuts = $this->getShortcuts();
        $grouped = [];

        foreach ($allShortcuts as $category => $shortcuts) {
            foreach ($shortcuts as $key => $shortcut) {
                $cat = $shortcut['category'];
                if (!isset($grouped[$cat])) {
                    $grouped[$cat] = [];
                }
                $grouped[$cat][$key] = $shortcut;
            }
        }

        return $grouped;
    }

    /**
     * Check if user has permission for shortcut
     */
    public function hasPermission($shortcut, $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!isset($shortcut['permission'])) {
            return true;
        }

        return $user && $user->can($shortcut['permission']);
    }

    /**
     * Get keyboard shortcut help text
     */
    public function getHelpText()
    {
        return [
            'title' => 'Keyboard Shortcuts',
            'description' => 'Use these keyboard shortcuts to navigate and perform actions quickly.',
            'categories' => [
                'Navigation' => 'Navigate between pages and sections',
                'Assets' => 'Asset management actions',
                'Table' => 'Table navigation and selection',
                'Forms' => 'Form actions and submission',
                'Modals' => 'Open and manage modals',
                'Interface' => 'Interface customization',
                'Help' => 'Help and information'
            ]
        ];
    }

    /**
     * Get shortcut key display format
     */
    public function formatKey($key)
    {
        $key = strtolower($key);
        
        // Replace common key names
        $replacements = [
            'ctrl' => 'Ctrl',
            'shift' => 'Shift',
            'alt' => 'Alt',
            'meta' => 'Cmd',
            'arrow_up' => '↑',
            'arrow_down' => '↓',
            'arrow_left' => '←',
            'arrow_right' => '→',
            'escape' => 'Esc',
            'enter' => 'Enter',
            'space' => 'Space',
            'tab' => 'Tab',
            'backspace' => 'Backspace',
            'delete' => 'Del',
            'home' => 'Home',
            'end' => 'End',
            'pageup' => 'Page Up',
            'pagedown' => 'Page Down'
        ];

        foreach ($replacements as $search => $replace) {
            $key = str_replace($search, $replace, $key);
        }

        // Format key combinations
        $key = str_replace('+', ' + ', $key);
        
        return $key;
    }

    /**
     * Get shortcuts for current user based on permissions
     */
    public function getShortcutsForUser($user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        $allShortcuts = $this->getShortcuts();
        $userShortcuts = [];

        foreach ($allShortcuts as $category => $shortcuts) {
            foreach ($shortcuts as $key => $shortcut) {
                if ($this->hasPermission($shortcut, $user)) {
                    $userShortcuts[$key] = $shortcut;
                }
            }
        }

        return $userShortcuts;
    }
}

