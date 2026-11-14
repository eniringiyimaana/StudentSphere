<?php

require_once 'config.php'; // DB connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid post ID.");
}

$post_id = intval($_GET['id']);

// Fetch post details
$postStmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$postStmt->bind_param("i", $post_id);
$postStmt->execute();
$postResult = $postStmt->get_result();
$post = $postResult->fetch_assoc();

if (!$post) {
    die("Post not found.");
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment'])) {
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to comment.");
    }

    $comment_text = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment_text)) {
        $insertStmt = $conn->prepare("
            INSERT INTO comments (post_id, user_id, comment, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $insertStmt->bind_param("iis", $post_id, $user_id, $comment_text);
        $insertStmt->execute();
        header("Location: post.php?id=$post_id#comments");
        exit;
    }
}

// Fetch comments for this post
$commentsStmt = $conn->prepare("
    SELECT u.name AS user_name, c.comment, c.created_at
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.post_id = ?
    ORDER BY c.created_at DESC
");
$commentsStmt->bind_param("i", $post_id);
$commentsStmt->execute();
$comments = $commentsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<div class="container mt-4">
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p class="text-muted"><?php echo date("F j, Y", strtotime($post['created_at'])); ?></p>
    <?php if (!empty($post['image'])): ?>
        <!-- In your index.php file -->
<img src="uploads/posts/<?= htmlspecialchars($post['image']) ?>" class="news-img" alt="<?= htmlspecialchars($post['title']) ?>">
    <?php endif; ?>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

    <hr>
    <h4 id="comments">Comments (<?php echo count($comments); ?>)</h4>

    <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $c): ?>
            <div class="mb-3">
                <strong><?php echo htmlspecialchars($c['user_name']); ?></strong> 
                <span class="text-muted" style="font-size: 0.9em;"><?php echo date("F j, Y, g:i a", strtotime($c['created_at'])); ?></span>
                <p><?php echo nl2br(htmlspecialchars($c['comment'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No comments yet. Be the first to comment!</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="post.php?id=<?php echo $post_id; ?>#comments" method="post" class="mt-4">
            <div class="mb-3">
                <textarea name="comment" class="form-control" rows="3" placeholder="Write your comment..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Comment</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to post a comment.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
