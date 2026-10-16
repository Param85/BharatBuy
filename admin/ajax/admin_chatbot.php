<?php
/**
 * Admin AJAX - Chatbot FAQ CRUD
 * Handles: add_faq, edit_faq, delete_faq
 */
require_once __DIR__ . '/../includes/admin_auth.php';
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

validate_csrf_or_exit();

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'add_faq':
        $keyword = trim($_POST['keyword'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        if (empty($keyword) || empty($answer)) {
            echo json_encode(['success' => false, 'message' => 'Keyword and answer are required.']);
            exit;
        }
        if (strlen($keyword) > 255 || strlen($answer) > 5000) {
            echo json_encode(['success' => false, 'message' => 'Input length exceeds allowable limits.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO chatbot (keyword, answer) VALUES (?, ?)");
            $stmt->execute([$keyword, $answer]);
            $newId = $pdo->lastInsertId();

            logAdminAction($pdo, 'Added FAQ', 'chatbot', $newId, "Keyword: $keyword");
            echo json_encode(['success' => true, 'message' => 'FAQ added successfully.', 'id' => $newId]);
        } catch (PDOException $e) {
            error_log("admin_chatbot error (add_faq): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An internal database error occurred while creating FAQ.']);
        }
        break;

    case 'edit_faq':
        $faqId = (int)($_POST['faq_id'] ?? 0);
        $keyword = trim($_POST['keyword'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        if ($faqId <= 0 || empty($keyword) || empty($answer)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }
        if (strlen($keyword) > 255 || strlen($answer) > 5000) {
            echo json_encode(['success' => false, 'message' => 'Input length exceeds allowable limits.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("UPDATE chatbot SET keyword = ?, answer = ? WHERE id = ?");
            $stmt->execute([$keyword, $answer, $faqId]);

            logAdminAction($pdo, 'Edited FAQ', 'chatbot', $faqId, "Keyword: $keyword");
            echo json_encode(['success' => true, 'message' => 'FAQ updated successfully.']);
        } catch (PDOException $e) {
            error_log("admin_chatbot error (edit_faq): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An internal database error occurred while updating FAQ.']);
        }
        break;

    case 'delete_faq':
        $faqId = (int)($_POST['faq_id'] ?? 0);
        if ($faqId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid FAQ ID.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM chatbot WHERE id = ?");
            $stmt->execute([$faqId]);

            logAdminAction($pdo, 'Deleted FAQ', 'chatbot', $faqId);
            echo json_encode(['success' => true, 'message' => 'FAQ deleted successfully.']);
        } catch (PDOException $e) {
            error_log("admin_chatbot error (delete_faq): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An internal database error occurred while deleting FAQ.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
