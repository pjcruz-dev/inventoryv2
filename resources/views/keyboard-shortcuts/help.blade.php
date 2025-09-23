@extends('layouts.app')

@section('title', 'Keyboard Shortcuts')
@section('page-title', 'Keyboard Shortcuts')

@section('page-actions')
    <button class="btn btn-outline-secondary btn-sm" onclick="window.history.back()">
        <i class="fas fa-arrow-left me-2"></i>Back
    </button>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Introduction -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-keyboard me-2"></i>
                        {{ $helpText['title'] }}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">{{ $helpText['description'] }}</p>
                    
                    <!-- Quick Reference -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-search text-primary me-3 fa-2x"></i>
                                <div>
                                    <h6 class="mb-1">Quick Search</h6>
                                    <small class="text-muted">Press <kbd>Ctrl</kbd> + <kbd>K</kbd> to focus search</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-moon text-warning me-3 fa-2x"></i>
                                <div>
                                    <h6 class="mb-1">Dark Mode</h6>
                                    <small class="text-muted">Press <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>D</kbd> to toggle</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-plus text-success me-3 fa-2x"></i>
                                <div>
                                    <h6 class="mb-1">New Asset</h6>
                                    <small class="text-muted">Press <kbd>Ctrl</kbd> + <kbd>N</kbd> to create</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fas fa-question-circle text-info me-3 fa-2x"></i>
                                <div>
                                    <h6 class="mb-1">This Help</h6>
                                    <small class="text-muted">Press <kbd>Ctrl</kbd> + <kbd>/</kbd> to show</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shortcuts by Category -->
            @foreach($shortcuts as $category => $categoryShortcuts)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-{{ $category === 'Navigation' ? 'compass' : ($category === 'Assets' ? 'box' : ($category === 'Table' ? 'table' : ($category === 'Forms' ? 'edit' : ($category === 'Modals' ? 'window-maximize' : ($category === 'Help' ? 'question-circle' : 'keyboard'))))) }} me-2"></i>
                        {{ $category }}
                        <small class="text-muted ms-2">{{ $helpText['categories'][$category] ?? '' }}</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30%">Shortcut</th>
                                    <th width="50%">Description</th>
                                    <th width="20%">Permission</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryShortcuts as $key => $shortcut)
                                <tr>
                                    <td>
                                        <kbd class="shortcut-key">{{ str_replace(['ctrl+', 'alt+', 'shift+'], ['Ctrl+', 'Alt+', 'Shift+'], ucwords(str_replace('+', ' + ', $key))) }}</kbd>
                                    </td>
                                    <td>{{ $shortcut['description'] }}</td>
                                    <td>
                                        @if(isset($shortcut['permission']))
                                            <span class="badge bg-info">{{ $shortcut['permission'] }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="col-lg-4">
            <!-- Tips -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Global shortcuts</strong> work from any page
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Context shortcuts</strong> only work on specific pages
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Permission-based</strong> shortcuts require proper access
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Escape key</strong> closes modals and dropdowns
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Tab navigation</strong> works in forms and tables
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Practice Area -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-dumbbell me-2"></i>Practice Area
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Try these shortcuts to get familiar:</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Focus Search</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Press Ctrl+K to focus" id="practiceSearch">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Test Form</label>
                        <input type="text" class="form-control" placeholder="Press Ctrl+S to save" id="practiceForm">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="testShortcut('create_asset')">
                            <i class="fas fa-plus me-1"></i>Test New Asset (Ctrl+N)
                        </button>
                        <button class="btn btn-outline-secondary" onclick="testShortcut('toggle_dark_mode')">
                            <i class="fas fa-moon me-1"></i>Test Dark Mode (Ctrl+Alt+D)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Keyboard Layout -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-keyboard me-2"></i>Keyboard Layout
                    </h6>
                </div>
                <div class="card-body">
                    <div class="keyboard-layout">
                        <div class="keyboard-row">
                            <kbd>Ctrl</kbd>
                            <kbd>Alt</kbd>
                            <kbd>Shift</kbd>
                            <kbd>Space</kbd>
                        </div>
                        <div class="keyboard-row">
                            <kbd>1</kbd>
                            <kbd>2</kbd>
                            <kbd>3</kbd>
                            <kbd>4</kbd>
                            <kbd>5</kbd>
                        </div>
                        <div class="keyboard-row">
                            <kbd>Q</kbd>
                            <kbd>W</kbd>
                            <kbd>E</kbd>
                            <kbd>R</kbd>
                            <kbd>T</kbd>
                        </div>
                        <div class="keyboard-row">
                            <kbd>A</kbd>
                            <kbd>S</kbd>
                            <kbd>D</kbd>
                            <kbd>F</kbd>
                            <kbd>G</kbd>
                        </div>
                        <div class="keyboard-row">
                            <kbd>Z</kbd>
                            <kbd>X</kbd>
                            <kbd>C</kbd>
                            <kbd>V</kbd>
                            <kbd>B</kbd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.shortcut-key {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.keyboard-layout {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: center;
}

.keyboard-row {
    display: flex;
    gap: 4px;
}

.keyboard-row kbd {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 0.875rem;
    font-weight: 500;
    min-width: 32px;
    text-align: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.keyboard-row kbd:hover {
    background: #e9ecef;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}

/* Dark mode styles */
[data-theme="dark"] .shortcut-key {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: #e9ecef;
}

[data-theme="dark"] .keyboard-row kbd {
    background: #343a40;
    border-color: #404040;
    color: #e9ecef;
}

[data-theme="dark"] .keyboard-row kbd:hover {
    background: #404040;
}
</style>
@endpush

@push('scripts')
<script>
// Helper functions for the view
function getCategoryIcon(category) {
    const icons = {
        'Navigation': 'compass',
        'Assets': 'desktop',
        'Table': 'table',
        'Forms': 'edit',
        'Modals': 'window-maximize',
        'Interface': 'cog',
        'Help': 'question-circle'
    };
    return icons[category] || 'keyboard';
}

function formatKey(key) {
    const replacements = {
        'ctrl': 'Ctrl',
        'shift': 'Shift',
        'alt': 'Alt',
        'meta': 'Cmd',
        'arrow_up': '↑',
        'arrow_down': '↓',
        'arrow_left': '←',
        'arrow_right': '→',
        'escape': 'Esc',
        'enter': 'Enter',
        'space': 'Space',
        'tab': 'Tab',
        'backspace': 'Backspace',
        'delete': 'Del',
        'home': 'Home',
        'end': 'End',
        'pageup': 'Page Up',
        'pagedown': 'Page Down'
    };

    let formattedKey = key.toLowerCase();
    
    for (const [search, replace] of Object.entries(replacements)) {
        formattedKey = formattedKey.replace(search, replace);
    }
    
    return formattedKey.replace('+', ' + ');
}

function testShortcut(action) {
    // Simulate shortcut execution
    const event = new CustomEvent('shortcut-executed', {
        detail: { action: action }
    });
    document.dispatchEvent(event);
    
    // Show feedback
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-1"></i>Executed!';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-primary', 'btn-outline-secondary');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');
    }, 2000);
}

// Make functions available globally
window.getCategoryIcon = getCategoryIcon;
window.formatKey = formatKey;
window.testShortcut = testShortcut;
</script>
@endpush

