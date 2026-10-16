<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

// Rate Limiting Configuration: max 5 failed attempts in 5 minutes
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = ['count' => 0, 'last_attempt' => time()];
}
if (time() - $_SESSION['login_attempts']['last_attempt'] > 300) {
    $_SESSION['login_attempts'] = ['count' => 0, 'last_attempt' => time()];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['login_attempts']['count'] >= 5) {
        $remaining = max(1, ceil((300 - (time() - $_SESSION['login_attempts']['last_attempt'])) / 60));
        $error = "Too many failed attempts. Please wait {$remaining} minute(s) before trying again.";
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = "Security validation failed. Please refresh and try again.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['pass'] ?? '';

        if (empty($email) || empty($pass)) {
            $error = "All fields are required!";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($pass, $user['password'])) {
                // Reset failed attempts upon successful login
                unset($_SESSION['login_attempts']);

                // Prevent session fixation
                regenerate_session_id();

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['is_admin'] = ((int)($user['is_admin'] ?? 0) === 1);
                
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['login_attempts']['count']++;
                $_SESSION['login_attempts']['last_attempt'] = time();
                $error = "Invalid credentials!";
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
    <div id="auth-title-container" style="text-align: center; margin-bottom: 1.5rem;">
        <h2 id="auth-title" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--text);">Welcome Back</h2>
    </div>

    <?php if($error): ?> 
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?php echo $error; ?></span>
        </div> 
    <?php endif; ?>

    <!-- 1. Account Chooser View -->
    <div id="account-chooser-view" style="display: none;">
        <!-- Header decoration with BharatBuy logo -->
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 1.5rem; position: relative;">
            <div style="background: radial-gradient(circle, #FAF2E3 50%, #F4EAD4 100%); padding: 1.25rem; border-radius: 50%; box-shadow: 0 4px 12px rgba(6, 67, 59, 0.08); display: flex; align-items: center; justify-content: center; width: 90px; height: 90px; border: 1.5px solid var(--champagne-border, #E8CD9E);">
                <img src="assets/images/logo.png?v=3" alt="BharatBuy" style="max-width: 65px; max-height: 65px; object-fit: contain;">
            </div>
        </div>

        <h2 style="font-family: 'Poppins', sans-serif; font-size: 1.35rem; font-weight: 600; color: var(--text); margin: 0 0 6px; text-align: center;">Sign in to BharatBuy</h2>
        <p style="text-align: center; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Which accounts will you prefer to login</p>
        
        <div id="accounts-list-container" class="accounts-chooser-list">
            <!-- Injected by JavaScript -->
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            <button id="use-other-btn" class="auth-secondary-btn" type="button">Use a different account</button>
            <button id="cancel-btn" class="auth-secondary-btn" type="button">Cancel</button>
        </div>
    </div>

    <!-- 2. Password Entry View -->
    <div id="password-view" style="display: none;">
        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <div id="selected-avatar-container"></div>
            <h3 id="selected-user-name" style="font-family: 'Poppins', sans-serif; font-weight: 700; margin: 0; color: var(--text);"></h3>
            <span id="selected-user-email" style="font-size: 0.85rem; color: var(--text-muted);"></span>
        </div>

        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <input type="hidden" name="email" id="password-view-email-input">
            <div class="form-group">
                <label for="password-view-pass">Password</label>
                <div class="password-input-wrap">
                    <input type="password" name="pass" id="password-view-pass" required style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 1rem; outline: none; background: var(--card-bg); color: var(--text);">
                    <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.75rem; border-radius: 10px;">Login</button>
        </form>

        <div style="text-align: center; margin-top: 1.25rem;">
            <a href="#" id="back-to-list-link" style="font-size: 0.9rem; color: var(--primary); font-weight: 600; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Back to accounts list</a>
        </div>
    </div>

    <!-- 3. Standard Login View (Blank Form) -->
    <div id="standard-login-view">
        <form method="POST" action="login.php" id="standard-login-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <div class="form-group">
                <label for="standard-email-input">Email</label>
                <input type="email" name="email" id="standard-email-input" required style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 1rem; outline: none; background: var(--card-bg); color: var(--text);">
            </div>
            <div class="form-group">
                <label for="standard-pass-input">Password</label>
                <div class="password-input-wrap">
                    <input type="password" name="pass" id="standard-pass-input" required style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border); border-radius: 10px; font-family: inherit; font-size: 1rem; outline: none; background: var(--card-bg); color: var(--text);">
                    <button type="button" class="password-toggle-btn" aria-label="Toggle password visibility"><i class="fa-solid fa-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; padding: 0.75rem; border-radius: 10px;">Login</button>
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
    background: rgba(248, 231, 201, 0.28);
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
    background: linear-gradient(135deg, #06433B 0%, #0A5A50 100%);
    color: #F8E7C9;
    border: 1px solid var(--champagne-border, #E8CD9E);
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
.auth-secondary-btn {
    border: 1.5px solid var(--champagne-border, #E8CD9E);
    background: var(--card-bg);
    color: #06433B;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}
.auth-secondary-btn:hover {
    background: #06433B;
    color: #F8E7C9;
    border-color: #06433B;
}
[data-theme="dark"] .auth-secondary-btn {
    color: #F8E7C9;
}
[data-theme="dark"] .auth-secondary-btn:hover {
    background: #F8E7C9;
    color: #06433B;
}

.big-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #06433B 0%, #0A5A50 100%);
    color: #F8E7C9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 2.25rem;
    text-transform: uppercase;
    border: 3px solid var(--champagne-border, #E8CD9E);
    box-shadow: var(--shadow);
    object-fit: cover;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountChooserView = document.getElementById('account-chooser-view');
    const passwordView = document.getElementById('password-view');
    const standardLoginView = document.getElementById('standard-login-view');
    const accountsListContainer = document.getElementById('accounts-list-container');
    const showChooserLinkContainer = document.getElementById('show-chooser-link-container');
    const authTitle = document.getElementById('auth-title');

    // Load accounts
    let accounts = [];
    try {
        accounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
    } catch(e) {
        accounts = [];
    }

    if (!Array.isArray(accounts)) {
        accounts = [];
    }

    // Render list
    function renderAccountsList() {
        accountsListContainer.innerHTML = '';
        if (accounts.length === 0) {
            showStandardLogin();
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

            // Click card to log in
            card.querySelector('.account-info-container').addEventListener('click', function() {
                showPasswordEntry(acc);
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

    function showPasswordEntry(acc) {
        accountChooserView.style.display = 'none';
        standardLoginView.style.display = 'none';
        passwordView.style.display = 'block';
        document.getElementById('auth-title-container').style.display = 'block';
        authTitle.innerText = 'Log In';
        
        // Populate fields
        document.getElementById('password-view-email-input').value = acc.email;
        document.getElementById('selected-user-name').innerText = acc.name || 'User';
        document.getElementById('selected-user-email').innerText = acc.email;
        
        const initial = acc.name ? acc.name.charAt(0) : 'U';
        const avatarContainer = document.getElementById('selected-avatar-container');
        avatarContainer.innerHTML = acc.image
            ? `<img class="big-avatar" src="${acc.image}" alt="${acc.name}" onerror="this.onerror=null; this.src='https://placehold.co/100x100/f5f5f7/1d1d1f?text=${initial}';">`
            : `<div class="big-avatar">${initial}</div>`;
            
        // Focus password input
        document.getElementById('password-view-pass').focus();
    }

    function showStandardLogin() {
        accountChooserView.style.display = 'none';
        passwordView.style.display = 'none';
        standardLoginView.style.display = 'block';
        document.getElementById('auth-title-container').style.display = 'block';
        authTitle.innerText = 'Welcome Back';
    }

    function showAccountChooser() {
        standardLoginView.style.display = 'none';
        passwordView.style.display = 'none';
        accountChooserView.style.display = 'block';
        document.getElementById('auth-title-container').style.display = 'none';
    }

    function removeAccount(email) {
        accounts = accounts.filter(acc => acc.email !== email);
        localStorage.setItem('saved_accounts', JSON.stringify(accounts));
        renderAccountsList();
        if (accounts.length > 0) {
            showAccountChooser();
        } else {
            showStandardLogin();
        }
    }

    // Event listeners
    document.getElementById('use-other-btn').addEventListener('click', showStandardLogin);
    document.getElementById('cancel-btn').addEventListener('click', function() {
        window.location.href = 'index.php';
    });
    document.getElementById('back-to-list-link').addEventListener('click', function(e) {
        e.preventDefault();
        showAccountChooser();
    });
    document.getElementById('show-chooser-link').addEventListener('click', function(e) {
        e.preventDefault();
        showAccountChooser();
    });

    // Initial load
    renderAccountsList();
    const urlParams = new URLSearchParams(window.location.search);
    const selectEmail = urlParams.get('select');
    if (selectEmail) {
        const matchedAcc = accounts.find(acc => acc.email === selectEmail);
        if (matchedAcc) {
            showPasswordEntry(matchedAcc);
        } else {
            if (accounts.length > 0) {
                showAccountChooser();
            } else {
                showStandardLogin();
            }
        }
    } else if (accounts.length > 0) {
        showAccountChooser();
    } else {
        showStandardLogin();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
