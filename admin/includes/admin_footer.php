        </div><!-- .admin-content -->
    </div><!-- .admin-main -->

    <script src="js/admin.js?v=<?= time(); ?>"></script>

    <!-- Confirmation Modal (shared across pages) -->
    <div class="admin-modal-overlay" id="confirmModal" style="display:none;">
        <div class="admin-modal">
            <div class="admin-modal-header">
                <h3 id="confirmModalTitle">Confirm Action</h3>
                <button class="admin-modal-close" id="confirmModalClose">&times;</button>
            </div>
            <div class="admin-modal-body">
                <p id="confirmModalMessage">Are you sure?</p>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-ghost" id="confirmModalCancel">Cancel</button>
                <button class="admin-btn admin-btn-danger" id="confirmModalConfirm">Confirm</button>
            </div>
        </div>
    </div>
</body>
</html>
