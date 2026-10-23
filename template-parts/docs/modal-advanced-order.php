<?php
/**
 * Advanced Order Settings Modal Component
 * Allows administrators and editors to rearrange article and subcategory orders.
 * Supports: First Come First Serve (FCFS), First Come Last Serve (FCLS), and Custom Order.
 *
 * @package HelloElementor
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('edit_posts')) {
    return;
}
?>

<!-- Advanced Order Settings Modal -->
<div id="cmg-advanced-order-modal" class="cmg-order-modal-backdrop" style="display: none;" aria-hidden="true" role="dialog">
    <div class="cmg-order-modal-container">
        
        <!-- Modal Header -->
        <div class="cmg-order-modal-header">
            <div class="cmg-order-header-info">
                <div class="cmg-order-icon-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="cmg-order-title">Advanced Order Settings</h3>
                    <p class="cmg-order-subtitle">Rearrange article sequence across modern sidebar, category page & article lists</p>
                </div>
            </div>
            <button type="button" class="cmg-order-close-btn" id="cmg-order-modal-close" aria-label="Close modal">&times;</button>
        </div>

        <!-- Scope Tabs: Articles vs Subcategories -->
        <div class="cmg-order-scope-tabs" id="cmg-order-tabs-wrap">
            <button type="button" class="cmg-order-tab active" data-tab="articles">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>Articles Order</span>
            </button>
            <button type="button" class="cmg-order-tab" data-tab="subcategories" id="cmg-subcats-tab" style="display: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                <span>Subcategories Order</span>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="cmg-order-modal-body">
            
            <!-- Category / Subcategory Selector -->
            <div class="cmg-order-field-group">
                <label for="cmg-order-cat-select" class="cmg-order-label">
                    <span>Category / Subcategory:</span>
                </label>
                <div class="cmg-order-select-wrap">
                    <select id="cmg-order-cat-select" class="cmg-order-select">
                        <option value="">Loading categories...</option>
                    </select>
                </div>
            </div>

            <!-- Ordering Method Cards -->
            <div class="cmg-order-field-group">
                <label class="cmg-order-label">
                    <span>Choose Ordering Method:</span>
                    <span class="cmg-order-method-badge" id="cmg-active-method-label">Current: First Come First Serve</span>
                </label>
                
                <div class="cmg-method-cards-grid">
                    <!-- Method 1: First Come First Serve -->
                    <div class="cmg-method-card" data-method="fcfs" tabindex="0">
                        <div class="cmg-method-radio-box">
                            <span class="cmg-radio-circle"></span>
                        </div>
                        <div class="cmg-method-details">
                            <div class="cmg-method-title-row">
                                <span class="cmg-method-name">First Come First Serve</span>
                                <span class="cmg-tag cmg-tag-blue">Oldest First</span>
                            </div>
                            <p class="cmg-method-desc">Articles created first are shown first (Chronological sequence: Date ASC).</p>
                        </div>
                    </div>

                    <!-- Method 2: First Come Last Serve -->
                    <div class="cmg-method-card" data-method="fcls" tabindex="0">
                        <div class="cmg-method-radio-box">
                            <span class="cmg-radio-circle"></span>
                        </div>
                        <div class="cmg-method-details">
                            <div class="cmg-method-title-row">
                                <span class="cmg-method-name">First Come Last Serve</span>
                                <span class="cmg-tag cmg-tag-amber">Newest First</span>
                            </div>
                            <p class="cmg-method-desc">Latest published articles appear at the top. First created appear last (Date DESC).</p>
                        </div>
                    </div>

                    <!-- Method 3: Custom Order -->
                    <div class="cmg-method-card" data-method="custom" tabindex="0">
                        <div class="cmg-method-radio-box">
                            <span class="cmg-radio-circle"></span>
                        </div>
                        <div class="cmg-method-details">
                            <div class="cmg-method-title-row">
                                <span class="cmg-method-name">Custom Order</span>
                                <span class="cmg-tag cmg-tag-purple">Drag & Drop</span>
                            </div>
                            <p class="cmg-method-desc">Freely drag and drop items or use arrows to define your exact custom sequence.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Sortable Preview List -->
            <div class="cmg-order-field-group cmg-list-group">
                <div class="cmg-list-header">
                    <span class="cmg-order-label mb-0" id="cmg-list-heading">
                        Sequence Preview (<span id="cmg-items-count">0</span> items)
                    </span>
                    <span class="cmg-drag-tip" id="cmg-drag-instruction">
                        Drag items to reorder or click arrows
                    </span>
                </div>

                <!-- Loading State -->
                <div id="cmg-order-loading" class="cmg-order-loading">
                    <div class="cmg-order-spinner"></div>
                    <span>Loading items...</span>
                </div>

                <!-- Empty State -->
                <div id="cmg-order-empty" class="cmg-order-empty" style="display: none;">
                    No articles found in this category.
                </div>

                <!-- Sortable Container -->
                <div id="cmg-sortable-items-wrap" class="cmg-sortable-wrap" style="display: none;">
                    <!-- Draggable items rendered dynamically -->
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="cmg-order-modal-footer">
            <div id="cmg-order-feedback" class="cmg-order-feedback" style="display: none;"></div>
            
            <div class="cmg-order-footer-buttons">
                <button type="button" class="cmg-btn-outline" id="cmg-order-modal-cancel">Cancel</button>
                <button type="button" class="cmg-btn-primary" id="cmg-save-order-btn">
                    <span class="cmg-btn-label">💾 Save & Apply Order</span>
                    <span class="cmg-btn-spinner" style="display: none;"></span>
                </button>
            </div>
        </div>

    </div>
</div>

<style>
/* Advanced Order Modal Styles */
.cmg-order-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 9999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    box-sizing: border-box;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.25s ease, visibility 0.25s ease;
}
.cmg-order-modal-backdrop.active {
    opacity: 1;
    visibility: visible;
}
.cmg-order-modal-container {
    background: #ffffff;
    width: 100%;
    max-width: 740px;
    max-height: 90vh;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transform: translateY(20px) scale(0.98);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.cmg-order-modal-backdrop.active .cmg-order-modal-container {
    transform: translateY(0) scale(1);
}
.cmg-order-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: #ffffff;
}
.cmg-order-header-info {
    display: flex;
    align-items: center;
    gap: 14px;
}
.cmg-order-icon-badge {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cmg-order-title {
    margin: 0;
    font-size: 19px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
}
.cmg-order-subtitle {
    margin: 3px 0 0 0;
    font-size: 13px;
    color: #64748b;
    line-height: 1.4;
}
.cmg-order-close-btn {
    background: transparent;
    border: none;
    font-size: 26px;
    line-height: 1;
    color: #94a3b8;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 8px;
    transition: all 0.15s ease;
}
.cmg-order-close-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.cmg-order-scope-tabs {
    display: flex;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 0 24px;
    gap: 8px;
}
.cmg-order-tab {
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 12px 16px;
    font-size: 13.5px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all 0.2s ease;
}
.cmg-order-tab:hover {
    color: #0f172a;
}
.cmg-order-tab.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
    background: #ffffff;
}
.cmg-order-modal-body {
    padding: 22px 24px;
    overflow-y: auto;
    flex: 1;
    background: #ffffff;
}
.cmg-order-field-group {
    margin-bottom: 20px;
}
.cmg-order-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}
.cmg-order-method-badge {
    font-size: 11.5px;
    font-weight: 600;
    color: #2563eb;
    background: #eff6ff;
    padding: 2px 8px;
    border-radius: 12px;
}
.cmg-order-select-wrap {
    position: relative;
}
.cmg-order-select {
    width: 100%;
    padding: 10px 14px;
    font-size: 14px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    background-color: #ffffff;
    color: #0f172a;
    font-weight: 500;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
}
.cmg-order-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}
.cmg-method-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
@media (max-width: 640px) {
    .cmg-method-cards-grid {
        grid-template-columns: 1fr;
    }
}
.cmg-method-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    user-select: none;
    outline: none;
}
.cmg-method-card:hover {
    border-color: #93c5fd;
    background: #f8fafc;
}
.cmg-method-card.active {
    border-color: #2563eb;
    background: #f0f7ff;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.1);
}
.cmg-method-radio-box {
    margin-top: 2px;
}
.cmg-radio-circle {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #cbd5e1;
    border-radius: 50%;
    box-sizing: border-box;
    position: relative;
    transition: all 0.2s ease;
}
.cmg-method-card.active .cmg-radio-circle {
    border-color: #2563eb;
}
.cmg-method-card.active .cmg-radio-circle::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 6px;
    height: 6px;
    background: #2563eb;
    border-radius: 50%;
}
.cmg-method-details {
    flex: 1;
}
.cmg-method-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 4px;
}
.cmg-method-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.cmg-tag {
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 6px;
    white-space: nowrap;
}
.cmg-tag-blue { background: #e0f2fe; color: #0369a1; }
.cmg-tag-amber { background: #fef3c7; color: #b45309; }
.cmg-tag-purple { background: #f3e8ff; color: #7e22ce; }
.cmg-method-desc {
    font-size: 11.5px;
    color: #64748b;
    margin: 0;
    line-height: 1.35;
}
.cmg-list-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.cmg-drag-tip {
    font-size: 12px;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 6px;
}
.cmg-sortable-wrap {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    max-height: 320px;
    overflow-y: auto;
    background: #f8fafc;
}
.cmg-order-item-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease, transform 0.15s ease;
    cursor: grab;
    user-select: none;
}
.cmg-order-item-row:last-child {
    border-bottom: none;
}
.cmg-order-item-row:hover {
    background: #f8fafc;
}
.cmg-order-item-row.dragging {
    opacity: 0.4;
    background: #eff6ff;
}
.cmg-order-item-row.drag-over {
    border-top: 2px solid #2563eb !important;
}
.cmg-item-handle {
    color: #94a3b8;
    cursor: grab;
    display: flex;
    align-items: center;
}
.cmg-item-handle svg {
    display: block;
}
.cmg-item-badge {
    width: 26px;
    height: 26px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cmg-item-title-col {
    flex: 1;
    min-width: 0;
}
.cmg-item-title {
    font-size: 13.5px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cmg-item-date {
    font-size: 11.5px;
    color: #94a3b8;
    margin-top: 1px;
}
.cmg-item-controls {
    display: flex;
    gap: 4px;
    flex-shrink: 0;
}
.cmg-arrow-btn {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cmg-arrow-btn:hover {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.cmg-order-loading,
.cmg-order-empty {
    padding: 36px 20px;
    text-align: center;
    color: #64748b;
    font-size: 14px;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
}
.cmg-order-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.cmg-order-spinner {
    width: 24px;
    height: 24px;
    border: 2px solid #e2e8f0;
    border-top-color: #2563eb;
    border-radius: 50%;
    animation: cmg-spin 0.7s linear infinite;
}
@keyframes cmg-spin {
    to { transform: rotate(360deg); }
}
.cmg-order-modal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    border-top: 1px solid #f1f5f9;
    background: #ffffff;
    gap: 16px;
    flex-wrap: wrap;
}
.cmg-order-feedback {
    font-size: 13px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 8px;
    flex: 1;
}
.cmg-order-feedback.success {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.cmg-order-feedback.error {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
.cmg-order-footer-buttons {
    display: flex;
    gap: 10px;
    margin-left: auto;
}
.cmg-btn-outline {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.cmg-btn-outline:hover {
    background: #f8fafc;
    color: #0f172a;
}
.cmg-btn-primary {
    background: #2563eb;
    border: 1px solid #2563eb;
    color: #ffffff;
    padding: 9px 22px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.cmg-btn-primary:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}
.cmg-btn-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Open Button Styles (Front-End & Headers) */
.cmg-open-advanced-order-modal-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #334155;
    padding: 6px 13px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    text-decoration: none !important;
}
.cmg-open-advanced-order-modal-btn:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #2563eb;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15);
}
.cmg-open-advanced-order-modal-btn svg {
    color: #64748b;
    transition: transform 0.3s ease, color 0.2s ease;
}
.cmg-open-advanced-order-modal-btn:hover svg {
    color: #2563eb;
    transform: rotate(45deg);
}
.cmg-subcat-order-btn {
    padding: 4px 9px;
    font-size: 12px;
    margin-left: auto;
    border-radius: 6px;
}
</style>

<script>
(function($) {
    'use strict';

    var currentCatId = 0;
    var currentScope = 'articles'; // 'articles' or 'subcategories'
    var currentMethod = 'fcfs';   // 'fcfs', 'fcls', 'custom'
    var rawItemsData = [];        // Cached items for current category
    var isSaving = false;

    // Open Modal Handlers
    $(document).on('click', '.cmg-open-advanced-order-modal-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var targetCatId = $(this).data('cat-id') || 0;
        openAdvancedOrderModal(targetCatId);
    });

    // Close Modal Handlers
    $('#cmg-order-modal-close, #cmg-order-modal-cancel').on('click', function(e) {
        e.preventDefault();
        closeAdvancedOrderModal();
    });

    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#cmg-advanced-order-modal').hasClass('active')) {
            closeAdvancedOrderModal();
        }
    });

    // Switch Category Dropdown
    $('#cmg-order-cat-select').on('change', function() {
        currentCatId = parseInt($(this).val(), 10) || 0;
        loadOrderData(currentCatId);
    });

    // Switch Scope Tabs (Articles vs Subcategories)
    $('.cmg-order-tab').on('click', function() {
        var tab = $(this).data('tab');
        if (tab === currentScope) return;
        
        $('.cmg-order-tab').removeClass('active');
        $(this).addClass('active');
        currentScope = tab;
        
        applyCurrentMethodToList();
    });

    // Select Ordering Method Card
    $('.cmg-method-card').on('click', function() {
        var method = $(this).data('method');
        if (!method) return;
        
        setOrderingMethod(method);
    });

    function openAdvancedOrderModal(initialCatId) {
        var $modal = $('#cmg-advanced-order-modal');
        $modal.show();
        setTimeout(function() {
            $modal.addClass('active');
        }, 10);

        // Load Categories Hierarchy and Data
        loadCategoriesList(initialCatId);
    }

    function closeAdvancedOrderModal() {
        var $modal = $('#cmg-advanced-order-modal');
        $modal.removeClass('active');
        setTimeout(function() {
            $modal.hide();
        }, 250);
    }

    function setOrderingMethod(method) {
        currentMethod = method;
        $('.cmg-method-card').removeClass('active');
        $('.cmg-method-card[data-method="' + method + '"]').addClass('active');

        var labelText = 'Current: ';
        if (method === 'fcfs') labelText += 'First Come First Serve (Oldest First)';
        else if (method === 'fcls') labelText += 'First Come Last Serve (Newest First)';
        else labelText += 'Custom Order (Drag & Drop)';
        
        $('#cmg-active-method-label').text(labelText);

        if (method === 'custom') {
            $('#cmg-drag-instruction').text('Drag items or use ▲ ▼ arrows to reorder').css('color', '#7e22ce');
        } else {
            $('#cmg-drag-instruction').text('Automatic sorting active based on publication date').css('color', '#64748b');
        }

        applyCurrentMethodToList();
    }

    function loadCategoriesList(selectedId) {
        $('#cmg-order-loading').show();
        $('#cmg-sortable-items-wrap').hide();
        $('#cmg-order-empty').hide();

        $.ajax({
            url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
            type: 'POST',
            data: {
                action: 'cmg_get_advanced_order_categories',
                selected_cat_id: selectedId,
                _ajax_nonce: '<?php echo wp_create_nonce("cmg_adv_order_nonce"); ?>'
            },
            success: function(res) {
                if (res.success && res.data) {
                    var categories = res.data.categories || [];
                    var $select = $('#cmg-order-cat-select');
                    $select.empty();

                    var foundSelected = false;
                    categories.forEach(function(cat) {
                        var isSel = (cat.id == res.data.active_id);
                        if (isSel) foundSelected = true;
                        var prefix = '';
                        if (cat.depth === 1) prefix = '— ';
                        if (cat.depth === 2) prefix = '—— ';

                        $select.append(
                            $('<option></option>')
                                .val(cat.id)
                                .text(prefix + cat.name + ' (' + cat.article_count + ' articles)')
                                .prop('selected', isSel)
                        );
                    });

                    currentCatId = res.data.active_id || (categories.length ? categories[0].id : 0);
                    loadOrderData(currentCatId);
                }
            },
            error: function() {
                $('#cmg-order-loading').hide();
                $('#cmg-order-empty').text('Failed to load categories. Please check permissions.').show();
            }
        });
    }

    function loadOrderData(catId) {
        if (!catId) return;

        $('#cmg-order-loading').show();
        $('#cmg-sortable-items-wrap').hide();
        $('#cmg-order-empty').hide();
        $('#cmg-order-feedback').hide();

        $.ajax({
            url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
            type: 'POST',
            data: {
                action: 'cmg_get_advanced_order_data',
                cat_id: catId,
                _ajax_nonce: '<?php echo wp_create_nonce("cmg_adv_order_nonce"); ?>'
            },
            success: function(res) {
                $('#cmg-order-loading').hide();
                if (res.success && res.data) {
                    rawItemsData = res.data;
                    
                    // Show or hide subcategories tab
                    if (rawItemsData.subcategories && rawItemsData.subcategories.length > 0) {
                        $('#cmg-subcats-tab').show();
                    } else {
                        $('#cmg-subcats-tab').hide();
                        if (currentScope === 'subcategories') {
                            currentScope = 'articles';
                            $('.cmg-order-tab').removeClass('active');
                            $('.cmg-order-tab[data-tab="articles"]').addClass('active');
                        }
                    }

                    // Set initial method (saved or default fcfs)
                    var savedMethod = rawItemsData.order_method || 'fcfs';
                    setOrderingMethod(savedMethod);
                } else {
                    $('#cmg-order-empty').text('No data available.').show();
                }
            },
            error: function() {
                $('#cmg-order-loading').hide();
                $('#cmg-order-empty').text('Error fetching category data.').show();
            }
        });
    }

    function applyCurrentMethodToList() {
        var items = [];

        if (currentScope === 'articles') {
            $('#cmg-list-heading').html('Articles Sequence (<span id="cmg-items-count">0</span> items)');
            items = (rawItemsData.articles || []).slice(0); // clone
        } else {
            $('#cmg-list-heading').html('Subcategories Sequence (<span id="cmg-items-count">0</span> items)');
            items = (rawItemsData.subcategories || []).slice(0); // clone
        }

        if (!items || items.length === 0) {
            $('#cmg-items-count').text(0);
            $('#cmg-sortable-items-wrap').hide();
            $('#cmg-order-empty').text('No ' + currentScope + ' found in this selection.').show();
            return;
        }

        $('#cmg-items-count').text(items.length);
        $('#cmg-order-empty').hide();

        // Sort items according to active method
        if (currentMethod === 'fcfs') {
            // First Come First Serve: Oldest date / lowest ID first
            items.sort(function(a, b) {
                if (a.date_timestamp && b.date_timestamp) {
                    return a.date_timestamp - b.date_timestamp;
                }
                return a.id - b.id;
            });
        } else if (currentMethod === 'fcls') {
            // First Come Last Serve: Newest date / highest ID first
            items.sort(function(a, b) {
                if (a.date_timestamp && b.date_timestamp) {
                    return b.date_timestamp - a.date_timestamp;
                }
                return b.id - a.id;
            });
        } else {
            // Custom Order: preserve current menu_order or manual sequence
            items.sort(function(a, b) {
                var ordA = typeof a.order !== 'undefined' ? parseInt(a.order, 10) : 0;
                var ordB = typeof b.order !== 'undefined' ? parseInt(b.order, 10) : 0;
                if (ordA !== ordB) return ordA - ordB;
                return (a.date_timestamp || 0) - (b.date_timestamp || 0);
            });
        }

        renderSortableList(items);
    }

    function renderSortableList(items) {
        var $wrap = $('#cmg-sortable-items-wrap');
        $wrap.empty().show();

        items.forEach(function(item, idx) {
            var dateInfo = item.date_formatted ? item.date_formatted : '';
            var $row = $(
                '<div class="cmg-order-item-row" draggable="true" data-id="' + item.id + '">' +
                    '<div class="cmg-item-handle" title="Drag to reorder">' +
                        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="5" r="1"></circle><circle cx="9" cy="12" r="1"></circle><circle cx="9" cy="19" r="1"></circle><circle cx="15" cy="5" r="1"></circle><circle cx="15" cy="12" r="1"></circle><circle cx="15" cy="19" r="1"></circle></svg>' +
                    '</div>' +
                    '<div class="cmg-item-badge">' + (idx + 1) + '</div>' +
                    '<div class="cmg-item-title-col">' +
                        '<div class="cmg-item-title">' + $('<div>').text(item.title).html() + '</div>' +
                        (dateInfo ? '<div class="cmg-item-date">' + dateInfo + '</div>' : '') +
                    '</div>' +
                    '<div class="cmg-item-controls">' +
                        '<button type="button" class="cmg-arrow-btn cmg-arrow-up" title="Move Up">▲</button>' +
                        '<button type="button" class="cmg-arrow-btn cmg-arrow-down" title="Move Down">▼</button>' +
                    '</div>' +
                '</div>'
            );
            $wrap.append($row);
        });

        initDragAndDrop();
    }

    function renumberBadges() {
        $('#cmg-sortable-items-wrap .cmg-order-item-row').each(function(index) {
            $(this).find('.cmg-item-badge').text(index + 1);
        });
    }

    // Up / Down Arrow Click Handlers
    $(document).on('click', '.cmg-arrow-up', function(e) {
        e.preventDefault();
        var $row = $(this).closest('.cmg-order-item-row');
        var $prev = $row.prev('.cmg-order-item-row');
        if ($prev.length) {
            $row.insertBefore($prev);
            renumberBadges();
            switchToCustomMethodIfManual();
        }
    });

    $(document).on('click', '.cmg-arrow-down', function(e) {
        e.preventDefault();
        var $row = $(this).closest('.cmg-order-item-row');
        var $next = $row.next('.cmg-order-item-row');
        if ($next.length) {
            $row.insertAfter($next);
            renumberBadges();
            switchToCustomMethodIfManual();
        }
    });

    function switchToCustomMethodIfManual() {
        if (currentMethod !== 'custom') {
            setOrderingMethod('custom');
        }
    }

    // HTML5 Drag and Drop Implementation
    var draggedElement = null;

    function initDragAndDrop() {
        var container = document.getElementById('cmg-sortable-items-wrap');
        if (!container) return;

        var rows = container.querySelectorAll('.cmg-order-item-row');
        rows.forEach(function(row) {
            row.addEventListener('dragstart', function(e) {
                draggedElement = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.getAttribute('data-id'));
            });

            row.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                rows.forEach(function(r) { r.classList.remove('drag-over'); });
                renumberBadges();
                switchToCustomMethodIfManual();
            });

            row.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                if (draggedElement !== this) {
                    this.classList.add('drag-over');
                }
            });

            row.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            row.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                if (draggedElement && draggedElement !== this) {
                    var all = Array.from(container.querySelectorAll('.cmg-order-item-row'));
                    var draggedIdx = all.indexOf(draggedElement);
                    var droppedIdx = all.indexOf(this);

                    if (draggedIdx < droppedIdx) {
                        this.parentNode.insertBefore(draggedElement, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedElement, this);
                    }
                    renumberBadges();
                    switchToCustomMethodIfManual();
                }
            });
        });
    }

    // Save Orders via AJAX
    $('#cmg-save-order-btn').on('click', function(e) {
        e.preventDefault();
        if (isSaving || !currentCatId) return;

        var $btn = $(this);
        var $toast = $('#cmg-order-feedback');
        
        var orderedIds = [];
        $('#cmg-sortable-items-wrap .cmg-order-item-row').each(function() {
            var id = $(this).data('id');
            if (id) orderedIds.push(id);
        });

        if (orderedIds.length === 0) {
            $toast.removeClass('success').addClass('error').text('No items to save.').show();
            return;
        }

        isSaving = true;
        $btn.prop('disabled', true);
        $btn.find('.cmg-btn-label').text('Applying everywhere...');
        $toast.hide();

        $.ajax({
            url: '<?php echo esc_url(admin_url("admin-ajax.php")); ?>',
            type: 'POST',
            data: {
                action: 'cmg_save_advanced_order',
                cat_id: currentCatId,
                scope: currentScope,
                order_method: currentMethod,
                ordered_ids: orderedIds,
                _ajax_nonce: '<?php echo wp_create_nonce("cmg_adv_order_nonce"); ?>'
            },
            success: function(res) {
                isSaving = false;
                $btn.prop('disabled', false);
                $btn.find('.cmg-btn-label').text('💾 Save & Apply Order');

                if (res.success) {
                    $toast.removeClass('error').addClass('success').text(res.data.message || 'Order applied successfully! Reloading...').show();
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    $toast.removeClass('success').addClass('error').text(res.data.message || 'Error saving order.').show();
                }
            },
            error: function() {
                isSaving = false;
                $btn.prop('disabled', false);
                $btn.find('.cmg-btn-label').text('💾 Save & Apply Order');
                $toast.removeClass('success').addClass('error').text('Connection failed while saving.').show();
            }
        });
    });

})(jQuery);
</script>
