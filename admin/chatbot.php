<?php
/**
 * Admin — Chatbot FAQ Manager
 * Full CRUD for the chatbot table.
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

try {
    $faqs = $pdo->query("SELECT * FROM chatbot ORDER BY id ASC")->fetchAll();
} catch (PDOException $e) {
    $faqs = [];
}
?>

<div class="admin-page-header">
    <h1>Chatbot FAQ Manager</h1>
    <p>Add, edit, or remove keyword-based FAQ responses for the chatbot fallback engine</p>
</div>

<div class="admin-table-card">
    <div class="admin-table-header">
        <h3><i class="fa-solid fa-robot" style="color: var(--admin-primary-hover); margin-right: 0.5rem;"></i>FAQ Entries (<?= count($faqs); ?>)</h3>
        <div class="admin-table-actions">
            <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="openFaqModal()">
                <i class="fa-solid fa-plus"></i> Add FAQ
            </button>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table" id="faqTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Keyword</th>
                    <th>Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($faqs)): ?>
                    <tr><td colspan="4"><div class="admin-empty-state"><i class="fa-solid fa-robot"></i><p>No FAQ entries found.</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($faqs as $faq): ?>
                        <tr id="faq-row-<?= $faq['id']; ?>">
                            <td class="fw-bold">#<?= $faq['id']; ?></td>
                            <td><span class="admin-badge primary"><?= htmlspecialchars($faq['keyword']); ?></span></td>
                            <td><span class="truncate" style="display:inline-block; max-width: 400px;" title="<?= htmlspecialchars($faq['answer']); ?>"><?= htmlspecialchars($faq['answer']); ?></span></td>
                            <td style="white-space: nowrap;">
                                <button class="admin-btn-icon" title="Edit" onclick="openFaqModal(<?= $faq['id']; ?>, '<?= htmlspecialchars(addslashes($faq['keyword'])); ?>', `<?= htmlspecialchars(addslashes($faq['answer'])); ?>`)">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="admin-btn-icon danger" title="Delete" onclick="deleteFaq(<?= $faq['id']; ?>, '<?= htmlspecialchars(addslashes($faq['keyword'])); ?>')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- FAQ Modal (Add/Edit) -->
<div class="admin-modal-overlay" id="faqModal" style="display:none;">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="faqModalTitle">Add FAQ</h3>
            <button class="admin-modal-close" onclick="closeFaqModal()">&times;</button>
        </div>
        <div class="admin-modal-body">
            <input type="hidden" id="faqEditId" value="">
            <div class="admin-form-group">
                <label for="faqKeyword">Keyword / Trigger</label>
                <input type="text" id="faqKeyword" class="admin-form-input" placeholder="e.g., how it works">
            </div>
            <div class="admin-form-group">
                <label for="faqAnswer">Answer</label>
                <textarea id="faqAnswer" class="admin-form-textarea" placeholder="Enter the chatbot response..."></textarea>
            </div>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-ghost" onclick="closeFaqModal()">Cancel</button>
            <button class="admin-btn admin-btn-primary" id="faqSaveBtn" onclick="saveFaq()">
                <i class="fa-solid fa-check"></i> Save
            </button>
        </div>
    </div>
</div>

<script>
function openFaqModal(id = null, keyword = '', answer = '') {
    document.getElementById('faqEditId').value = id || '';
    document.getElementById('faqKeyword').value = keyword;
    document.getElementById('faqAnswer').value = answer;
    document.getElementById('faqModalTitle').textContent = id ? 'Edit FAQ' : 'Add FAQ';
    document.getElementById('faqModal').style.display = 'flex';
    document.getElementById('faqKeyword').focus();
}

function closeFaqModal() {
    document.getElementById('faqModal').style.display = 'none';
}

async function saveFaq() {
    const editId = document.getElementById('faqEditId').value;
    const keyword = document.getElementById('faqKeyword').value.trim();
    const answer = document.getElementById('faqAnswer').value.trim();

    if (!keyword || !answer) {
        showAdminToast('Keyword and answer are required.', 'error');
        return;
    }

    const data = { keyword, answer };
    if (editId) {
        data.action = 'edit_faq';
        data.faq_id = editId;
    } else {
        data.action = 'add_faq';
    }

    const result = await adminFetch('ajax/admin_chatbot.php', data);
    if (result.success) {
        showAdminToast(result.message, 'success');
        closeFaqModal();
        setTimeout(() => location.reload(), 600);
    } else {
        showAdminToast(result.message, 'error');
    }
}

function deleteFaq(faqId, keyword) {
    showConfirmModal(
        'Delete FAQ',
        `Delete the FAQ entry for keyword "${keyword}"?`,
        async function() {
            const result = await adminFetch('ajax/admin_chatbot.php', { action: 'delete_faq', faq_id: faqId });
            if (result.success) {
                const row = document.getElementById('faq-row-' + faqId);
                if (row) {
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => row.remove(), 300);
                }
                showAdminToast(result.message, 'success');
            } else {
                showAdminToast(result.message, 'error');
            }
        }
    );
}
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
