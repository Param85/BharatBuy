<?php
/**
 * Admin AJAX — Chatbot FAQ CRUD
 * Handles: add_faq, edit_faq, delete_faq
 */
require_once __DIR__ . '/../includes/admin_auth.php';
requireAdmin();

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'add_faq':
        $keyword = trim($_POST['keyword'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        if (empty($keyword) || empty($answer)) {
            echo json_encode(['success' => false, 'message' => 'Keyword and answer are required.']);
            exit;
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO chatbot (keyword, answer) VALUES (?, ?)");
            $stmt->execute([$keyword, $answer]);
            $newId = $pdo->lastInsertId();

            logAdminAction($pdo, 'Added FAQ', 'chatbot', $newId, "Keyword: $keyword");
            echo json_encode(['success' => true, 'message' => 'FAQ added successfully.', 'id' => $newId]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
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
        try {
            $stmt = $pdo->prepare("UPDATE chatbot SET keyword = ?, answer = ? WHERE id = ?");
            $stmt->execute([$keyword, $answer, $faqId]);

            logAdminAction($pdo, 'Edited FAQ', 'chatbot', $faqId, "Keyword: $keyword");
            echo json_encode(['success' => true, 'message' => 'FAQ updated successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
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
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}
