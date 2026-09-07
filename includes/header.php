<?php 
require_once __DIR__ . '/session.php'; 
require_once __DIR__ . '/db.php'; 

$dbAlertsEnabled = 1;
$pendingAlerts = [];
$userNotifications = [];
if (isLoggedIn()) {
    try {
        if (!isset($_SESSION['user_email']) || !isset($_SESSION['profile_image']) || !isset($_SESSION['is_admin'])) {
            $stmtUser = $pdo->prepare("SELECT email, full_name, profile_image, is_admin FROM users WHERE id = ?");
            $stmtUser->execute([$_SESSION['user_id']]);
            $userData = $stmtUser->fetch();
            if ($userData) {
                $_SESSION['user_email'] = $userData['email'];
                $_SESSION['user_name'] = $userData['full_name'];
                $_SESSION['profile_image'] = $userData['profile_image'];
                $_SESSION['is_admin'] = ((int)($userData['is_admin'] ?? 0) === 1);
            }
        }
        
        // Fetch notifications list
        $stmtNotifList = $pdo->prepare("SELECT * FROM user_notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 15");
        $stmtNotifList->execute([$_SESSION['user_id']]);
        $userNotifications = $stmtNotifList->fetchAll();

        $stmt = $pdo->prepare("SELECT alerts_enabled FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $val = $stmt->fetchColumn();
        if ($val !== false) {
            $dbAlertsEnabled = (int)$val;
        }

        if ($dbAlertsEnabled === 1) {
            $stmt = $pdo->prepare("
                SELECT t.id as tracker_id, p.id as product_id, t.target_price, p.product_name, p.current_price, p.image_url 
                FROM product_trackers t
                JOIN product_prices p ON t.product_id = p.id
                WHERE t.user_id = ? AND t.is_active = 1 AND p.current_price <= t.target_price
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $pendingAlerts = $stmt->fetchAll();
        }
    } catch (PDOException $e) {
        // Fallback silently
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <?php if (isLoggedIn()): ?>
    <script>
        (function () {
            const userEmail = <?php echo json_encode($_SESSION['user_email'] ?? ''); ?>;
            const userName = <?php echo json_encode($_SESSION['user_name'] ?? ''); ?>;
            const userImg = <?php echo json_encode($_SESSION['profile_image'] ?? ''); ?>;
            if (userEmail) {
                let accounts = JSON.parse(localStorage.getItem('saved_accounts') || '[]');
                accounts = accounts.filter(acc => acc.email !== userEmail);
                accounts.unshift({
                    email: userEmail,
                    name: userName,
                    image: userImg
                });
                localStorage.setItem('saved_accounts', JSON.stringify(accounts.slice(0, 5)));
            }
        })();
    </script>
    <?php endif; ?>
    <style>
    .notif-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--error);
        color: white;
        border-radius: 50%;
        font-size: 0.65rem;
        font-weight: 800;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--card-bg);
    }
    .notif-dropdown {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        width: 320px;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        z-index: 1000;
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        overflow: visible;
        animation: slideDown 0.2s ease-out;
    }
    .notif-dropdown::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 8px solid transparent;
        border-bottom-color: var(--primary-light);
    }
    .notif-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        background: var(--primary-light);
    }
    .notif-header h3 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--primary);
    }
    .notif-clear-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: color 0.15s;
    }
    .notif-clear-btn:hover {
        color: var(--error);
    }
    .notif-body {
        max-height: 280px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    .notif-item {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 4px;
        cursor: pointer;
        transition: background 0.15s;
        text-align: left;
    }
    .notif-item:last-child {
        border-bottom: none;
    }
    .notif-item:hover {
        background: var(--primary-light);
    }
    .notif-item-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--primary);
    }
    .notif-item-message {
        font-size: 0.8rem;
        color: var(--text);
        line-height: 1.4;
    }
    .notif-item-time {
        font-size: 0.7rem;
        color: var(--text-muted);
    }
    .notif-empty {
        padding: 2rem 1rem;
        text-align: center;
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }
    @keyframes bellShake {
        0%, 100% { transform: rotate(0); }
        15%, 45%, 75% { transform: rotate(15deg); }
        30%, 60%, 90% { transform: rotate(-15deg); }
    }
    .bell-wiggle {
        animation: bellShake 0.6s ease-in-out;
        color: var(--primary) !important;
        display: inline-block;
    }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="index.php" class="nav-logo"><img src="assets/images/logo.png?v=3" alt="BharatBuy"></a>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <?php if(isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="wishlist.php">Wishlist</a></li>
                    <?php endif; ?>
                    <?php if(isLoggedIn()): ?>
                    <li style="position: relative;">
                        <button id="notif-bell-toggle" class="nav-icon-btn" title="Notifications" aria-label="Notifications">
                            <i class="fa-solid fa-bell"></i>
                            <span id="notif-badge" class="notif-badge" style="display: none;">0</span>
                        </button>
                        <div id="notif-dropdown" class="notif-dropdown" style="display: none;">
                            <div class="notif-header">
                                <h3>Notifications</h3>
                                <button id="notif-clear-all" class="notif-clear-btn">Clear All</button>
                            </div>
                            <div id="notif-items-container" class="notif-body">
                                <div class="notif-empty">No new notifications</div>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>

                    <li style="position: relative;">
                        <button id="settings-toggle" class="nav-icon-btn" title="Settings" aria-label="Settings">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                        <div id="settings-dropdown" class="settings-dropdown">
                            <div class="settings-header">
                                <h3>Settings</h3>
                            </div>
                            <div class="settings-body" style="display: flex; flex-direction: column; gap: 0.85rem;">
                                <div class="settings-item">
                                    <span data-translate="themeLabel">Appearance</span>
                                    <button id="theme-toggle" class="theme-toggle-dropdown-btn">
                                        <i class="fa-solid fa-moon"></i> <span id="theme-btn-text">Dark Mode</span>
                                    </button>
                                </div>
                                <div class="settings-item">
                                    <span data-translate="langLabel">Language</span>
                                    <select id="lang-select" class="settings-select">
                                        <option value="en">English</option>
                                        <option value="hi">हिंदी (Hindi)</option>
                                        <option value="es">Español (Spanish)</option>
                                        <option value="fr">Français (French)</option>
                                        <option value="de">Deutsch (German)</option>
                                    </select>
                                </div>
                                <div class="settings-item">
                                    <span data-translate="currencyLabel">Currency</span>
                                    <select id="currency-select" class="settings-select">
                                        <option value="INR">INR (₹)</option>
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="GBP">GBP (£)</option>
                                    </select>
                                </div>
                                <div class="settings-item">
                                    <span data-translate="notifLabel">Price Alerts</span>
                                    <button id="notif-toggle" class="theme-toggle-dropdown-btn">
                                        <i class="fa-solid fa-bell"></i> <span id="notif-btn-text">Enabled</span>
                                    </button>
                                </div>
                                <div class="settings-item">
                                    <span data-translate="strictLabel">Search Mode</span>
                                    <select id="strict-select" class="settings-select">
                                        <option value="strict">Strict Match</option>
                                        <option value="broad">Broad Search</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php if(isLoggedIn()): ?>
                        <?php if(!empty($_SESSION['is_admin'])): ?>
                            <li><a href="admin/" class="btn" style="background: rgba(99, 102, 241, 0.15); color: #6366f1; border: 1px solid rgba(99, 102, 241, 0.35); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 0.45rem 0.9rem; border-radius: 8px; text-decoration: none;"><i class="fa-solid fa-shield-halved"></i> Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="btn btn-primary">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php" class="btn btn-primary">Join</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <script>
        const PENDING_ALERTS = <?php echo json_encode($pendingAlerts); ?>;
        const TRANSLATIONS = {
            en: {
                home: "Home",
                dashboard: "Dashboard",
                wishlist: "Wishlist",
                logout: "Logout",
                login: "Login",
                join: "Join",
                themeLabel: "Appearance",
                langLabel: "Language",
                darkModeBtn: "Dark Mode",
                lightModeBtn: "Light Mode",
                settingsTitle: "Settings",
                currencyLabel: "Currency",
                notifLabel: "Price Alerts",
                strictLabel: "Search Mode",
                enabledText: "Enabled",
                disabledText: "Disabled",
                strictOption: "Strict Match",
                broadOption: "Broad Search",
                heroTitle: "Compare. Analyze.<br><span style='background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>Shop Smarter.</span>",
                heroDesc: "The intelligent shopping assistant that searches multiple online platforms simultaneously and recommends the absolute best deal using a smart scoring algorithm.",
                getStarted: "Get Started Now",
                goToDashboard: "Go to Dashboard",
                smartScoringTitle: "Smart Value Scoring",
                smartScoringDesc: "Our weighted engine processes price, merchant ratings, and customer reviews to calculate the absolute best product value.",
                multiSiteTitle: "Multi-Site Integration",
                multiSiteDesc: "Aggregate product results from major e-commerce platforms like Amazon and eBay dynamically in a single interface.",
                searchTrackingTitle: "Search Tracking",
                searchTrackingDesc: "Review your search logs and access previous product comparisons instantly without re-typing search keywords."
            },
            hi: {
                home: "मुख्य पृष्ठ",
                dashboard: "डैशबोर्ड",
                wishlist: "इच्छा सूची",
                logout: "लॉगआउट",
                login: "लॉगिन",
                join: "शामिल हों",
                themeLabel: "प्रकटन",
                langLabel: "भाषा",
                darkModeBtn: "डार्क मोड",
                lightModeBtn: "लाइट मोड",
                settingsTitle: "सेटिंग्स",
                currencyLabel: "मुद्रा",
                notifLabel: "मूल्य अलर्ट",
                strictLabel: "खोज मोड",
                enabledText: "सक्षम",
                disabledText: "अक्षम",
                strictOption: "सख्त मिलान",
                broadOption: "व्यापक खोज",
                heroTitle: "तुलना करें। विश्लेषण करें।<br><span style='background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>होशियारी से खरीदें।</span>",
                heroDesc: "बुद्धिमान शॉपिंग सहायक जो एक साथ कई ऑनलाइन प्लेटफ़ॉर्म पर खोज करता है और स्मार्ट स्कोरिंग एल्गोरिदम का उपयोग करके सर्वोत्तम सौदे की सिफारिश करता है।",
                getStarted: "अभी शुरू करें",
                goToDashboard: "डैशबोर्ड पर जाएं",
                smartScoringTitle: "स्मार्ट वैल्यू स्कोरिंग",
                smartScoringDesc: "हमारा भारित इंजन सर्वोत्तम उत्पाद मूल्य की गणना करने के लिए मूल्य, विक्रेता रेटिंग और ग्राहक समीक्षाओं को संसाधित करता है।",
                multiSiteTitle: "मल्टी-साइट एकीकरण",
                multiSiteDesc: "एक ही इंटरफ़ेस में अमेज़न और ईबे जैसे प्रमुख ई-कॉमर्स प्लेटफ़ॉर्म के उत्पाद परिणामों को गतिशील रूप से एकत्रित करें।",
                searchTrackingTitle: "सर्च ट्रैकिंग",
                searchTrackingDesc: "अपने खोज लॉग की समीक्षा करें और खोज कीवर्ड को पुन: टाइप किए बिना पिछली उत्पाद तुलनाओं को तुरंत एक्सेस करें।"
            },
            es: {
                home: "Inicio",
                dashboard: "Panel de control",
                wishlist: "Lista de deseos",
                logout: "Cerrar sesión",
                login: "Iniciar sesión",
                join: "Unirse",
                themeLabel: "Apariencia",
                langLabel: "Idioma",
                darkModeBtn: "Modo oscuro",
                lightModeBtn: "Modo claro",
                settingsTitle: "Ajustes",
                currencyLabel: "Moneda",
                notifLabel: "Alertas de precio",
                strictLabel: "Modo de búsqueda",
                enabledText: "Activado",
                disabledText: "Desactivado",
                strictOption: "Coincidencia estricta",
                broadOption: "Búsqueda amplia",
                heroTitle: "Comparar. Analizar.<br><span style='background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>Compra inteligente.</span>",
                heroDesc: "El asistente de compras inteligente que busca en múltiples plataformas en línea simultáneamente y recomienda la mejor oferta absoluta mediante un algoritmo de puntuación inteligente.",
                getStarted: "Empezar ahora",
                goToDashboard: "Ir al panel",
                smartScoringTitle: "Puntuación de valor inteligente",
                smartScoringDesc: "Nuestro motor procesa el precio, las calificaciones de los vendedores y las opiniones de los clientes para calcular el valor absoluto del producto.",
                multiSiteTitle: "Integración multi-sitio",
                multiSiteDesc: "Agregue resultados de productos de las principales plataformas de comercio electrónico como Amazon y eBay en una sola interfaz.",
                searchTrackingTitle: "Seguimiento de búsqueda",
                searchTrackingDesc: "Revise sus registros de búsqueda anteriores al instante sin tener que volver a escribir los términos."
            },
            fr: {
                home: "Accueil",
                dashboard: "Tableau de bord",
                wishlist: "Liste de souhaits",
                logout: "Se déconnecter",
                login: "Se connecter",
                join: "Rejoindre",
                themeLabel: "Apparence",
                langLabel: "Langue",
                darkModeBtn: "Mode sombre",
                lightModeBtn: "Mode clair",
                settingsTitle: "Paramètres",
                currencyLabel: "Devise",
                notifLabel: "Alertes de prix",
                strictLabel: "Mode de recherche",
                enabledText: "Activé",
                disabledText: "Désactivé",
                strictOption: "Correspondance stricte",
                broadOption: "Recherche large",
                heroTitle: "Comparer. Analyser.<br><span style='background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>Acheter plus intelligemment.</span>",
                heroDesc: "L'assistant d'achat intelligent qui effectue des recherches sur plusieurs plateformes en ligne simultanément et recommande la meilleure offre absolue à l'aide d'un algorithme de notation intelligent.",
                getStarted: "Commencer maintenant",
                goToDashboard: "Aller au tableau de bord",
                smartScoringTitle: "Évaluation intelligente de la valeur",
                smartScoringDesc: "Notre motor pondéré traite les prix, les évaluations des marchands et les avis des clients pour calculer la valeur absolue du produit.",
                multiSiteTitle: "Intégration multi-sites",
                multiSiteDesc: "Regroubez les résultats des produits des principales plateformes de commerce électronique comme Amazon et eBay dans une seule interface.",
                searchTrackingTitle: "Suivi des recherches",
                searchTrackingDesc: "Consultez vos historiques de recherche et accédez instantanément aux comparaisons précédentes sans retaper les mots-clés."
            },
            de: {
                home: "Startseite",
                dashboard: "Dashboard",
                wishlist: "Wunschliste",
                logout: "Abmelden",
                login: "Anmelden",
                join: "Beitreten",
                themeLabel: "Aussehen",
                langLabel: "Sprache",
                darkModeBtn: "Dunkelmodus",
                lightModeBtn: "Lichtmodus",
                settingsTitle: "Einstellungen",
                currencyLabel: "Währung",
                notifLabel: "Preisbenachrichtigungen",
                strictLabel: "Suchmodus",
                enabledText: "Aktiviert",
                disabledText: "Deaktiviert",
                strictOption: "Strikte Übereinstimmung",
                broadOption: "Breite Suche",
                heroTitle: "Vergleichen. Analysieren.<br><span style='background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;'>Intelligenter einkaufen.</span>",
                heroDesc: "Der intelligente Einkaufsassistent, der gleichzeitig mehrere Online-Plattformen durchsucht und mithilfe eines intelligenten Bewertungsalgorithmus das absolut beste Angebot empfiehlt.",
                getStarted: "Jetzt starten",
                goToDashboard: "Zum Dashboard gehen",
                smartScoringTitle: "Intelligente Wertbewertung",
                smartScoringDesc: "Unsere gewichtete Engine verarbeitet Preise, Händlerbewertungen und Kundenrezensionen, um den absoluten Produktwert zu berechnen.",
                multiSiteTitle: "Multi-Site-Integration",
                multiSiteDesc: "Aggregieren Sie Produktergebnisse von großen E-Commerce-Plattformen wie Amazon und eBay dynamisch in einer einzigen Benutzeroberfläche.",
                searchTrackingTitle: "Suchverlauf",
                searchTrackingDesc: "Überprüfen Sie Ihre Suchprotokolle und greifen Sie sofort auf frühere Produktvergleiche zu, ohne Suchbegriffe erneut einzugeben."
            }
        };

        const CURRENCIES = {
            INR: { symbol: "₹", rate: 1 },
            USD: { symbol: "$", rate: 0.0105 },
            EUR: { symbol: "€", rate: 0.0097 },
            GBP: { symbol: "£", rate: 0.0082 }
        };

        function formatCurrency(priceInr) {
            const savedCurrency = localStorage.getItem('currency') || 'INR';
            const cur = CURRENCIES[savedCurrency] || CURRENCIES['INR'];
            const converted = (priceInr * cur.rate).toFixed(2);
            return cur.symbol + parseFloat(converted).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function updateAllPrices() {
            document.querySelectorAll('[data-price-inr]').forEach(el => {
                const priceInr = parseFloat(el.getAttribute('data-price-inr'));
                if (!isNaN(priceInr)) {
                    el.textContent = formatCurrency(priceInr);
                }
            });
            // Update input group currency prefix on product-details page
            const alertPrefix = document.getElementById('alert-currency-prefix');
            if (alertPrefix) {
                const savedCurrency = localStorage.getItem('currency') || 'INR';
                const cur = CURRENCIES[savedCurrency] || CURRENCIES['INR'];
                alertPrefix.textContent = cur.symbol;
            }
        }

        function applyLanguage(lang) {
            const dict = TRANSLATIONS[lang] || TRANSLATIONS['en'];
            
            // Translate navigation menu links
            const navLinks = document.querySelectorAll('.nav-links a');
            navLinks.forEach(link => {
                const text = link.textContent.trim().toLowerCase();
                // Match links and replace
                if (text.includes('home') || text.includes('inicio') || text.includes('accueil') || text.includes('startseite') || text.includes('मुख्य')) {
                    link.textContent = dict.home;
                } else if (text.includes('dashboard') || text.includes('tableau') || text.includes('panel') || text.includes('डैशबोर्ड')) {
                    link.textContent = dict.dashboard;
                } else if (text.includes('wishlist') || text.includes('liste') || text.includes('lista') || text.includes('इच्छा')) {
                    link.textContent = dict.wishlist;
                } else if (text.includes('logout') || text.includes('déconnecter') || text.includes('cerrar') || text.includes('abmelden') || text.includes('लॉगआउट')) {
                    link.textContent = dict.logout;
                } else if (text.includes('login') || text.includes('se connecter') || text.includes('iniciar') || text.includes('anmelden') || text.includes('लॉगिन')) {
                    link.textContent = dict.login;
                } else if (text.includes('join') || text.includes('rejoindre') || text.includes('unirse') || text.includes('beitreten') || text.includes('शामिल')) {
                    link.textContent = dict.join;
                }
            });

            // Translate static dropdown labels using data-translate
            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.getAttribute('data-translate');
                if (dict[key]) {
                    el.textContent = dict[key];
                }
            });

            // Translate settings dropdown title
            const dropdownHeader = document.querySelector('.settings-header h3');
            if (dropdownHeader) {
                dropdownHeader.textContent = dict.settingsTitle;
            }

            // Sync theme button text translation
            const htmlEl = document.documentElement;
            const currentTheme = htmlEl.getAttribute('data-theme') || 'light';
            const themeBtnText = document.getElementById('theme-btn-text');
            if (themeBtnText) {
                themeBtnText.textContent = currentTheme === 'dark' ? dict.lightModeBtn : dict.darkModeBtn;
            }

            // Translate selector option texts
            const strictSelect = document.getElementById('strict-select');
            if (strictSelect) {
                strictSelect.options[0].text = dict.strictOption;
                strictSelect.options[1].text = dict.broadOption;
            }

            // Sync notifications text translation
            const notifBtnText = document.getElementById('notif-btn-text');
            if (notifBtnText) {
                const isEnabled = localStorage.getItem('notif_alerts') !== 'disabled';
                notifBtnText.textContent = isEnabled ? dict.enabledText : dict.disabledText;
            }

            // Homepage Hero Translations
            const heroH1 = document.getElementById('hero-title');
            if (heroH1) {
                heroH1.innerHTML = dict.heroTitle;
            }

            const heroP = document.getElementById('hero-desc');
            if (heroP) {
                heroP.textContent = dict.heroDesc;
            }

            const heroBtn = document.getElementById('hero-btn');
            if (heroBtn) {
                const isStarted = heroBtn.getAttribute('href').includes('register.php');
                if (isStarted) {
                    heroBtn.innerHTML = dict.getStarted + ' <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>';
                } else {
                    heroBtn.innerHTML = dict.goToDashboard + ' <i class="fa-solid fa-arrow-right" style="margin-left: 0.5rem;"></i>';
                }
            }

            // Homepage Feature Cards Translations
            const ft1 = document.getElementById('feature-title-1');
            const fd1 = document.getElementById('feature-desc-1');
            if (ft1 && fd1) {
                ft1.textContent = dict.smartScoringTitle;
                fd1.textContent = dict.smartScoringDesc;
            }

            const ft2 = document.getElementById('feature-title-2');
            const fd2 = document.getElementById('feature-desc-2');
            if (ft2 && fd2) {
                ft2.textContent = dict.multiSiteTitle;
                fd2.textContent = dict.multiSiteDesc;
            }

            const ft3 = document.getElementById('feature-title-3');
            const fd3 = document.getElementById('feature-desc-3');
            if (ft3 && fd3) {
                ft3.textContent = dict.searchTrackingTitle;
                fd3.textContent = dict.searchTrackingDesc;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('theme-toggle');
            const toggleIcon = toggleBtn ? toggleBtn.querySelector('i') : null;
            const themeBtnText = document.getElementById('theme-btn-text');
            const htmlEl = document.documentElement;

            const settingsToggle = document.getElementById('settings-toggle');
            const settingsDropdown = document.getElementById('settings-dropdown');
            const langSelect = document.getElementById('lang-select');
            const currencySelect = document.getElementById('currency-select');
            const notifToggle = document.getElementById('notif-toggle');
            const notifIcon = notifToggle ? notifToggle.querySelector('i') : null;
            const notifBtnText = document.getElementById('notif-btn-text');
            const strictSelect = document.getElementById('strict-select');

            // Sync initial theme
            const currentTheme = htmlEl.getAttribute('data-theme') || 'light';
            if (currentTheme === 'dark') {
                if (toggleIcon) toggleIcon.className = 'fa-solid fa-sun';
                if (themeBtnText) themeBtnText.textContent = TRANSLATIONS[localStorage.getItem('lang') || 'en'].lightModeBtn;
            } else {
                if (toggleIcon) toggleIcon.className = 'fa-solid fa-moon';
                if (themeBtnText) themeBtnText.textContent = TRANSLATIONS[localStorage.getItem('lang') || 'en'].darkModeBtn;
            }

            // Sync initial currency
            if (currencySelect) {
                const savedCurrency = localStorage.getItem('currency') || 'INR';
                currencySelect.value = savedCurrency;
            }

            // Sync initial notifications state (prioritize database setting for logged-in users)
            const isNotifEnabled = <?php echo isLoggedIn() ? $dbAlertsEnabled : "(localStorage.getItem('notif_alerts') !== 'disabled' ? 1 : 0)"; ?> === 1;
            localStorage.setItem('notif_alerts', isNotifEnabled ? 'enabled' : 'disabled');
            if (notifBtnText) {
                const currentLang = localStorage.getItem('lang') || 'en';
                notifBtnText.textContent = isNotifEnabled ? TRANSLATIONS[currentLang].enabledText : TRANSLATIONS[currentLang].disabledText;
            }
            if (notifIcon) {
                notifIcon.className = isNotifEnabled ? 'fa-solid fa-bell' : 'fa-solid fa-bell-slash';
            }

            // Sync initial search strictness
            if (strictSelect) {
                const savedStrictness = localStorage.getItem('search_strictness') || 'strict';
                strictSelect.value = savedStrictness;
            }

            // Settings dropdown open/close toggle
            if (settingsToggle && settingsDropdown) {
                settingsToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (notifDropdown) notifDropdown.style.display = 'none';
                    settingsDropdown.classList.toggle('show');
                });

                document.addEventListener('click', (e) => {
                    if (!settingsDropdown.contains(e.target) && e.target !== settingsToggle) {
                        settingsDropdown.classList.remove('show');
                    }
                });
            }

            // Theme toggle listener inside settings
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    const theme = htmlEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    htmlEl.setAttribute('data-theme', theme);
                    localStorage.setItem('theme', theme);

                    const currentLang = localStorage.getItem('lang') || 'en';

                    if (theme === 'dark') {
                        if (toggleIcon) toggleIcon.className = 'fa-solid fa-sun';
                        if (themeBtnText) themeBtnText.textContent = TRANSLATIONS[currentLang].lightModeBtn;
                    } else {
                        if (toggleIcon) toggleIcon.className = 'fa-solid fa-moon';
                        if (themeBtnText) themeBtnText.textContent = TRANSLATIONS[currentLang].darkModeBtn;
                    }
                });
            }

            // Language selector listener
            if (langSelect) {
                const savedLang = localStorage.getItem('lang') || 'en';
                langSelect.value = savedLang;
                applyLanguage(savedLang);

                langSelect.addEventListener('change', (e) => {
                    const lang = e.target.value;
                    localStorage.setItem('lang', lang);
                    applyLanguage(lang);
                });
            }

            // Currency listener
            if (currencySelect) {
                currencySelect.addEventListener('change', (e) => {
                    localStorage.setItem('currency', e.target.value);
                    updateAllPrices();
                });
            }

            // Notifications toggle listener
            if (notifToggle) {
                notifToggle.addEventListener('click', () => {
                    const currentLang = localStorage.getItem('lang') || 'en';
                    const isEnabled = localStorage.getItem('notif_alerts') !== 'disabled';
                    const newState = isEnabled ? 'disabled' : 'enabled';
                    localStorage.setItem('notif_alerts', newState);

                    if (notifIcon) {
                        notifIcon.className = newState === 'enabled' ? 'fa-solid fa-bell' : 'fa-solid fa-bell-slash';
                    }
                    if (notifBtnText) {
                        notifBtnText.textContent = newState === 'enabled' ? TRANSLATIONS[currentLang].enabledText : TRANSLATIONS[currentLang].disabledText;
                    }

                    // Sync setting with backend DB
                    const formData = new FormData();
                    formData.append('enabled', newState === 'enabled' ? '1' : '0');
                    fetch('ajax/update_alert_setting.php', {
                        method: 'POST',
                        body: formData
                    }).catch(err => console.error("Error syncing alert preference:", err));
                });
            }

            // Search strictness listener
            if (strictSelect) {
                strictSelect.addEventListener('change', (e) => {
                    localStorage.setItem('search_strictness', e.target.value);
                });
            }

            // Notifications Dropdown toggle and clear handlers
            const notifBellToggle = document.getElementById('notif-bell-toggle');
            const notifDropdown = document.getElementById('notif-dropdown');
            const notifBadge = document.getElementById('notif-badge');
            const notifItemsContainer = document.getElementById('notif-items-container');
            const notifClearAll = document.getElementById('notif-clear-all');

            const notifications = <?php echo json_encode($userNotifications); ?> || [];

            function renderNotifications() {
                if (!notifItemsContainer) return;
                notifItemsContainer.innerHTML = '';
                
                if (notifications.length === 0) {
                    notifItemsContainer.innerHTML = '<div class="notif-empty">No new notifications</div>';
                    if (notifBadge) notifBadge.style.display = 'none';
                    return;
                }

                // Show badge count
                if (notifBadge) {
                    notifBadge.textContent = notifications.length;
                    notifBadge.style.display = 'flex';
                }

                notifications.forEach(notif => {
                    const item = document.createElement('div');
                    item.className = 'notif-item';
                    
                    // Format time
                    const notifTime = new Date(notif.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    item.innerHTML = `
                        <div class="notif-item-title">${notif.title}</div>
                        <div class="notif-item-message">${notif.message}</div>
                        <div class="notif-item-time">${notifTime}</div>
                    `;

                    item.addEventListener('click', () => {
                        if (notif.product_id) {
                            window.location.href = `product-details.php?id=${notif.product_id}`;
                        }
                    });

                    notifItemsContainer.appendChild(item);
                });
            }

            function triggerAlertNotification(alert) {
                // 1. Add alert to dynamic list
                notifications.unshift({
                    id: alert.tracker_id,
                    title: '🔥 Price Drop Alert!',
                    message: `${alert.product_name} is now down to ₹${parseFloat(alert.current_price).toLocaleString()}!`,
                    product_id: alert.product_id,
                    created_at: new Date().toISOString()
                });

                // 2. Re-render the dropdown list & badge
                renderNotifications();

                // 3. Shake/wiggle the bell button
                const bellIcon = document.querySelector('#notif-bell-toggle i');
                if (bellIcon) {
                    bellIcon.classList.add('bell-wiggle');
                    setTimeout(() => {
                        bellIcon.classList.remove('bell-wiggle');
                    }, 650);
                }

                // 4. Drop down the dropdown directly under the button!
                if (notifDropdown) {
                    notifDropdown.style.display = 'flex';
                }



                // 6. Deactivate tracker
                const formData = new FormData();
                formData.append('tracker_id', alert.tracker_id);
                fetch('ajax/deactivate_tracker.php', {
                    method: 'POST',
                    body: formData
                }).catch(err => console.error("Error deactivating tracker:", err));
            }

            if (notifBellToggle && notifDropdown) {
                notifBellToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isVisible = notifDropdown.style.display === 'flex';
                    notifDropdown.style.display = isVisible ? 'none' : 'flex';
                    
                    // Hide settings dropdown if open
                    const settingsDropdown = document.getElementById('settings-dropdown');
                    if (settingsDropdown) {
                        settingsDropdown.classList.remove('show');
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', () => {
                    notifDropdown.style.display = 'none';
                });

                notifDropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            }

            if (notifClearAll) {
                notifClearAll.addEventListener('click', () => {
                    fetch('ajax/clear_notifications.php', { method: 'POST' })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                notifications.length = 0;
                                renderNotifications();
                            }
                        })
                        .catch(err => console.error("Error clearing notifications:", err));
                });
            }

            // Initial render of notifications list
            renderNotifications();

            // Run active pending alerts
            if (typeof PENDING_ALERTS !== 'undefined' && PENDING_ALERTS.length > 0 && localStorage.getItem('notif_alerts') !== 'disabled') {
                PENDING_ALERTS.forEach(triggerAlertNotification);
            }
        });
    </script>
    <main>
