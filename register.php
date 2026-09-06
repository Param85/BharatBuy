<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    if (empty($name) || empty($email) || empty($_POST['pass'])) {
        $msg = ['error', 'All fields are required!'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = ['error', 'Please enter a valid email address.'];
    } else {
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->fetch()) {
            $msg = ['error', 'Email already exists!'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $pass]);
            $msg = ['success', 'Account created! <a href="login.php">Login here</a>'];
        }
    }
}
?>

<div class="auth-container">
    <div id="auth-title-container" style="text-align: center; margin-bottom: 1.5rem;">
        <h2 id="auth-title" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--text);">Join BharatBuy</h2>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-<?php echo $msg[0]; ?>" style="margin-bottom: 1.5rem;">
            <?php if($msg[0] === 'error'): ?>
                <i class="fa-solid fa-circle-exclamation"></i>
            <?php else: ?>
                <i class="fa-solid fa-circle-check"></i>
            <?php endif; ?>
            <span><?php echo $msg[1]; ?></span>
        </div>
    <?php endif; ?>

    <!-- 1. Account Chooser View -->
    <div id="account-chooser-view" style="display: none;">
        <!-- Header decoration with BharatBuy logo -->
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 1.5rem; position: relative;">
            <div style="background: radial-gradient(circle, #f8fafc 60%, #e2e8f0 100%); padding: 1.25rem; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; width: 90px; height: 90px; border: 1px solid #e2e8f0;">
                <img src="assets/images/logo.png?v=3" alt="BharatBuy" style="max-width: 65px; max-height: 65px; object-fit: contain;">
            </div>
        </div>

        <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.35rem; font-weight: 600; color: var(--text); margin: 0 0 6px; text-align: center;">Sign in to BharatBuy</h2>
        <p style="text-align: center; color: #5f6368; font-size: 0.9rem; margin-bottom: 1.5rem;">Which accounts will you prefer to login</p>
        
        <div id="accounts-list-container" class="accounts-chooser-list">
            <!-- Injected by JavaScript -->
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            <button id="use-other-btn" class="google-pill-btn" type="button">Use a different account</button>
            <button id="cancel-btn" class="google-pill-btn" type="button">Cancel</button>
        </div>
    </div>

    <!-- 2. Standard Register Form View -->
    <div id="standard-register-view">
        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 1rem; outline: none; background: var(--card-bg); color: var(--text);">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 1rem; outline: none; background: var(--card-bg); color: var(--text);">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="pass" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 1rem; outline: none; background: var(--card-bg); color: var(--text);">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.75rem; border-radius: 10px;">Create Account</button>
        </form>
        <div id="show-chooser-link-container" style="text-align: center; margin-top: 1.25rem; display: none;">
            <a href="#" id="show-chooser-link" style="font-size: 0.9rem; color: var(--primary); font-weight: 600; text-decoration: none;"><i class="fa-solid fa-users"></i> Choose a saved account</a>
        </div>
    </div>
</div>

<style>
.accounts-chooser-list {
    margin-left: -2.5rem;
    margin-right: -2.5rem;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    margin-bottom: 1.5rem;
}
.account-item-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 2.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--card-bg);
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}
.account-item-card:last-child {
    border-bottom: none;
}
.account-item-card:hover {
    background: var(--primary-light);
}
.account-info-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}
.account-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.25rem;
    text-transform: uppercase;
    object-fit: cover;
}
.account-details {
    display: flex;
    flex-direction: column;
}
.account-details h4 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text);
}
.account-details span {
    font-size: 0.8rem;
    color: var(--text-muted);
}
.account-remove-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1rem;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 50%;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}
.account-remove-btn:hover {
    color: var(--error);
    background: var(--error-light);
}
.google-pill-btn {
    border: 1px solid var(--border);
    background: var(--card-bg);
    color: var(--primary);
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
}
.google-pill-btn:hover {
    background: var(--primary-light);
    border-color: var(--primary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountChooserView = document.getElementById('account-chooser-view');
    const standardRegisterView = document.getElementById('standard-register-view');
    const accountsListContainer = document.getElementById('accounts-list-container');
    const showChooserLinkContainer = document.getElementById('show-chooser-link-container');
    const authTitleContainer = document.getElementById('auth-title-container');

    // Load accounts
    let accounts = [];
    try {
        accounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
    } catch(e) {
        accounts = [];
    }

    // Pre-populate if empty (same list as login page for consistency)
    if (accounts.length === 0) {
        accounts = [
            { email: 'paramshah647@gmail.com', name: 'Param Shah', image: null },
            { email: 'paramshaha41@gmail.com', name: 'PARAM SHAH', image: null }
        ];
        localStorage.setItem('saved_accounts', JSON.stringify(accounts));
    }

    // Render list
    function renderAccountsList() {
        accountsListContainer.innerHTML = '';
        if (accounts.length === 0) {
            showStandardRegister();
            showChooserLinkContainer.style.display = 'none';
            return;
        }

        accounts.forEach((acc) => {
            const initial = acc.name ? acc.name.charAt(0) : 'U';
            const avatarHtml = acc.image 
                ? `<img class="account-avatar" src="${acc.image}" alt="${acc.name}" onerror="this.onerror=null; this.src='https://placehold.co/100x100/f5f5f7/1d1d1f?text=${initial}';">`
                : `<div class="account-avatar">${initial}</div>`;

            const card = document.createElement('div');
            card.className = 'account-item-card';
            card.innerHTML = `
                <div class="account-info-container">
                    ${avatarHtml}
                    <div class="account-details">
                        <h4>${acc.name || 'User'}</h4>
                        <span>${acc.email}</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button class="account-remove-btn" title="Remove Account" type="button">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                    <i class="fa-solid fa-chevron-right" style="color: #5f6368; font-size: 0.85rem;"></i>
                </div>
            `;

            // Click card to log in via select parameter
            card.querySelector('.account-info-container').addEventListener('click', function() {
                window.location.href = `login.php?select=${encodeURIComponent(acc.email)}`;
            });

            // Click delete button
            card.querySelector('.account-remove-btn').addEventListener('click', function(e) {
                e.stopPropagation();
                removeAccount(acc.email);
            });

            accountsListContainer.appendChild(card);
        });
        
        showChooserLinkContainer.style.display = 'block';
    }

    function showStandardRegister() {
        accountChooserView.style.display = 'none';
        standardRegisterView.style.display = 'block';
        authTitleContainer.style.display = 'block';
    }

    function showAccountChooser() {
        standardRegisterView.style.display = 'none';
        accountChooserView.style.display = 'block';
        authTitleContainer.style.display = 'none';
    }

    function removeAccount(email) {
        accounts = accounts.filter(acc => acc.email !== email);
        localStorage.setItem('saved_accounts', JSON.stringify(accounts));
        renderAccountsList();
        if (accounts.length > 0) {
            showAccountChooser();
        } else {
            showStandardRegister();
        }
    }

    // Event listeners
    document.getElementById('use-other-btn').addEventListener('click', showStandardRegister);
    document.getElementById('cancel-btn').addEventListener('click', function() {
        window.location.href = 'index.php';
    });
    document.getElementById('show-chooser-link').addEventListener('click', function(e) {
        e.preventDefault();
        showAccountChooser();
    });

    // Initial load
    renderAccountsList();
    if (accounts.length > 0) {
        showAccountChooser();
    } else {
        showStandardRegister();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
