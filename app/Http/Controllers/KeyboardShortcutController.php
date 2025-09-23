<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KeyboardShortcutService;

class KeyboardShortcutController extends Controller
{
    protected $shortcutService;

    public function __construct(KeyboardShortcutService $shortcutService)
    {
        $this->shortcutService = $shortcutService;
    }

    /**
     * Get all keyboard shortcuts
     */
    public function index()
    {
        $shortcuts = $this->shortcutService->getShortcutsByCategory();
        $helpText = $this->shortcutService->getHelpText();
        
        return response()->json([
            'success' => true,
            'shortcuts' => $shortcuts,
            'help' => $helpText
        ]);
    }

    /**
     * Get shortcuts for current user
     */
    public function userShortcuts()
    {
        $shortcuts = $this->shortcutService->getShortcutsForUser();
        
        return response()->json([
            'success' => true,
            'shortcuts' => $shortcuts
        ]);
    }

    /**
     * Get shortcuts for specific context
     */
    public function contextShortcuts(Request $request)
    {
        $context = $request->input('context', 'global');
        $shortcuts = $this->shortcutService->getShortcutsForContext($context);
        
        return response()->json([
            'success' => true,
            'shortcuts' => $shortcuts,
            'context' => $context
        ]);
    }

    /**
     * Execute shortcut action
     */
    public function execute(Request $request)
    {
        $action = $request->input('action');
        $context = $request->input('context', 'global');
        
        $shortcuts = $this->shortcutService->getShortcutsForContext($context);
        $shortcut = collect($shortcuts)->firstWhere('action', $action);
        
        if (!$shortcut) {
            return response()->json([
                'success' => false,
                'message' => 'Shortcut action not found'
            ], 404);
        }

        if (!$this->shortcutService->hasPermission($shortcut)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action'
            ], 403);
        }

        // Return the action to be executed on the frontend
        return response()->json([
            'success' => true,
            'action' => $action,
            'shortcut' => $shortcut
        ]);
    }

    /**
     * Get shortcut help page
     */
    public function help()
    {
        $shortcuts = $this->shortcutService->getShortcutsByCategory();
        $helpText = $this->shortcutService->getHelpText();
        
        return view('keyboard-shortcuts.help', compact('shortcuts', 'helpText'));
    }
}

