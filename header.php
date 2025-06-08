<?php  
// Start the session only if not already started  
if (session_status() == PHP_SESSION_NONE) {  
    session_start();  
}  
include_once('db.php');
include_once('includes/language_functions.php');

$userId = $_SESSION['UserID'] ?? null;
$notifCount = 0;
if ($userId && isset($dbh)) {
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM bookings WHERE UserID = ? AND is_seen_by_user = 0");
    $stmt->execute([$userId]);
    $notifCount = $stmt->fetchColumn();
}

// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Get current language from session or default to English
$current_lang = $_SESSION['selected_lang'] ?? 'en';
?>
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<header class="main-header">  
  <div class="header-left">  
    <!-- Hamburger Menu -->  
    <div class="hamburger-menu" onclick="toggleMenu()">☰</div>  
    <!-- Logo -->  
    <img src="images/logo.png" alt="Logo" class="main-logo">  
  </div>  

  <!-- Top Navigation Bar -->  
  <nav class="top-nav">  
    <ul>  
      <li style="position:relative;">
        <!-- Manage Bookings Link -->
        <?php if (isset($_SESSION['login']) && !empty($_SESSION['login'])): ?>
          <a href="manage_booking.php" data-translate="Manage Bookings"><i class="fa fa-car"></i> <span data-translate="Manage Bookings">Manage Bookings</span></a>
        <?php else: ?>
          <a href="javascript:void(0);" onclick="toggleLoginForm()" data-translate="Manage Bookings"><i class="fa fa-car"></i> <span data-translate="Manage Bookings">Manage Bookings</span></a>
        <?php endif; ?>
      </li>  
      <li style="position:relative;">
        <a href="#" id="lang-switch" data-lang="en">🌐 EN <i class="fa fa-caret-down"></i></a>
        <ul id="lang-dropdown" style="display:none;position:absolute;top:100%;left:0;background:#fff;box-shadow:0 2px 8px #0002;padding:0;margin:0;list-style:none;z-index:999;min-width:180px;max-height:300px;overflow-y:auto;">
          <li><a href="#" data-langcode="en">English</a></li>
          <li><a href="#" data-langcode="af">Afrikaans</a></li>
          <li><a href="#" data-langcode="sq">Albanian</a></li>
          <li><a href="#" data-langcode="am">Amharic</a></li>
          <li><a href="#" data-langcode="ar">Arabic</a></li>
          <li><a href="#" data-langcode="hy">Armenian</a></li>
          <li><a href="#" data-langcode="eu">Basque</a></li>
          <li><a href="#" data-langcode="be">Belarusian</a></li>
          <li><a href="#" data-langcode="bn">Bengali</a></li>
          <li><a href="#" data-langcode="bs">Bosnian</a></li>
          <li><a href="#" data-langcode="bg">Bulgarian</a></li>
          <li><a href="#" data-langcode="ca">Catalan</a></li>
          <li><a href="#" data-langcode="zh">Chinese (Simplified)</a></li>
          <li><a href="#" data-langcode="zh-TW">Chinese (Traditional)</a></li>
          <li><a href="#" data-langcode="hr">Croatian</a></li>
          <li><a href="#" data-langcode="cs">Czech</a></li>
          <li><a href="#" data-langcode="da">Danish</a></li>
          <li><a href="#" data-langcode="nl">Dutch</a></li>
          <li><a href="#" data-langcode="eo">Esperanto</a></li>
          <li><a href="#" data-langcode="et">Estonian</a></li>
          <li><a href="#" data-langcode="fi">Finnish</a></li>
          <li><a href="#" data-langcode="fr">French</a></li>
          <li><a href="#" data-langcode="gl">Galician</a></li>
          <li><a href="#" data-langcode="ka">Georgian</a></li>
          <li><a href="#" data-langcode="de">German</a></li>
          <li><a href="#" data-langcode="el">Greek</a></li>
          <li><a href="#" data-langcode="gu">Gujarati</a></li>
          <li><a href="#" data-langcode="ht">Haitian Creole</a></li>
          <li><a href="#" data-langcode="ha">Hausa</a></li>
          <li><a href="#" data-langcode="he">Hebrew</a></li>
          <li><a href="#" data-langcode="hi">Hindi</a></li>
          <li><a href="#" data-langcode="hu">Hungarian</a></li>
          <li><a href="#" data-langcode="is">Icelandic</a></li>
          <li><a href="#" data-langcode="id">Indonesian</a></li>
          <li><a href="#" data-langcode="ga">Irish</a></li>
          <li><a href="#" data-langcode="it">Italian</a></li>
          <li><a href="#" data-langcode="ja">Japanese</a></li>
          <li><a href="#" data-langcode="kk">Kazakh</a></li>
          <li><a href="#" data-langcode="ko">Korean</a></li>
          <li><a href="#" data-langcode="ku">Kurdish</a></li>
          <li><a href="#" data-langcode="lv">Latvian</a></li>
          <li><a href="#" data-langcode="lt">Lithuanian</a></li>
          <li><a href="#" data-langcode="mk">Macedonian</a></li>
          <li><a href="#" data-langcode="mg">Malagasy</a></li>
          <li><a href="#" data-langcode="ms">Malay</a></li>
          <li><a href="#" data-langcode="mt">Maltese</a></li>
          <li><a href="#" data-langcode="no">Norwegian</a></li>
          <li><a href="#" data-langcode="ps">Pashto</a></li>
          <li><a href="#" data-langcode="fa">Persian</a></li>
          <li><a href="#" data-langcode="pl">Polish</a></li>
          <li><a href="#" data-langcode="pt">Portuguese (Portugal)</a></li>
          <li><a href="#" data-langcode="pa">Punjabi</a></li>
          <li><a href="#" data-langcode="ro">Romanian</a></li>
          <li><a href="#" data-langcode="ru">Russian</a></li>
          <li><a href="#" data-langcode="sr">Serbian</a></li>
          <li><a href="#" data-langcode="sk">Slovak</a></li>
          <li><a href="#" data-langcode="sl">Slovene</a></li>
          <li><a href="#" data-langcode="es">Spanish</a></li>
          <li><a href="#" data-langcode="sw">Swahili</a></li>
          <li><a href="#" data-langcode="sv">Swedish</a></li>
          <li><a href="#" data-langcode="ta">Tamil</a></li>
          <li><a href="#" data-langcode="te">Telugu</a></li>
          <li><a href="#" data-langcode="th">Thai</a></li>
          <li><a href="#" data-langcode="tr">Turkish</a></li>
          <li><a href="#" data-langcode="uk">Ukrainian</a></li>
          <li><a href="#" data-langcode="ur">Urdu</a></li>
          <li><a href="#" data-langcode="vi">Vietnamese</a></li>
          <li><a href="#" data-langcode="cy">Welsh</a></li>
          <li><a href="#" data-langcode="xh">Xhosa</a></li>
          <li><a href="#" data-langcode="yi">Yiddish</a></li>
          <li><a href="#" data-langcode="yo">Yoruba</a></li>
          <li><a href="#" data-langcode="zu">Zulu</a></li>
        </ul>
      </li>  

      <?php  
      // Check if the user is logged in  
      if (isset($_SESSION['login']) && !empty($_SESSION['login'])): ?>  
        <!-- Dropdown Menu for Dashboard -->  
        <li class="dropdown">  
          <a href="javascript:void(0);" class="dropbtn">
            <i class="fas fa-user"></i> 
            <span><?php echo htmlspecialchars($_SESSION['fname']); ?>'s</span>
            <span data-translate="Dashboard">Dashboard</span> ▼
          </a>  
          <div class="dropdown-content">  
            <a href="profile.php" data-translate="Profile Settings">Profile Settings</a>  
            <a href="activity_log.php" data-translate="Activity Log">Activity Log</a>  
            <a href="logout.php" data-translate="Logout"><i class="fas fa-sign-out-alt"></i> <span data-translate="Logout">Logout</span></a>  
          </div>  
        </li>  
      <?php else: ?>  
        <!-- Show different navigation based on current page -->  
        <li>
          <a href="javascript:void(0);" onclick="toggleLoginForm()" data-translate="Log in">
            <i class="fas fa-sign-in-alt"></i> <span data-translate="Log in">Log in</span>
          </a>
        </li>
        <?php if ($current_page === 'register.php'): ?>
        <li>
          <a href="index.php" data-translate="Homepage">
            <i class="fas fa-home"></i> <span data-translate="Homepage">Homepage</span>
          </a>
        </li>
        <?php else: ?>
        <li>
          <a href="register.php" data-translate="Register">
            <i class="fas fa-user-plus"></i> <span data-translate="Register">Register</span>
          </a>
        </li>
        <?php endif; ?>
      <?php endif; ?>  
    </ul>  
  </nav> 
</header>  

<header class="top-header">
  <div class="container" data-translate="Welcome to Ormoc Car Rental - Call us at (956) 783-3665">
    Welcome to Ormoc Car Rental - Call us at (956) 783-3665
  </div>
</header>

<!-- Sidebar Hamburger Menu -->  
<div id="mobileMenu" class="sidebar">  
  <div class="sidebar-header">  
    <span class="close-btn" onclick="toggleMenu()">✕</span>  
    <img src="images/logo.png" alt="Logo" class="sidebar-logo">  
  </div>  
  <ul class="menu-items">  
    <li><a href="index.php" onclick="toggleMenu()" data-translate="Home">Home</a></li>  
    <li><a href="explore.php" onclick="toggleMenu()" data-translate="Explore Us">Explore Us</a></li>  
    <li><a href="about.php" onclick="toggleMenu()" data-translate="About">About</a></li>  
    <li><a href="contact.php" onclick="toggleMenu()" data-translate="Contact Us">Contact Us</a></li>  
    <li>
      <a href="rrj.php" onclick="toggleMenu()">
        <span data-translate="RRJ">RRJ</span><br>
        <small data-translate="Car Subscription">Car Subscription</small>
      </a>
    </li>  
    <li><a href="business.php" onclick="toggleMenu()" data-translate="Business">Business</a></li>  
    <?php if (!isset($_SESSION['login']) || empty($_SESSION['login'])): ?>
    <li>
      <a href="javascript:void(0);" onclick="toggleLoginForm(); toggleMenu();" data-translate="Log in">
        <i class="fas fa-sign-in-alt"></i> <span data-translate="Log in">Log in</span>
      </a>
    </li>
    <li>
      <a href="javascript:void(0);" onclick="toggleRegisterForm(); toggleMenu();" data-translate="Register">
        <i class="fas fa-user-plus"></i> <span data-translate="Register">Register</span>
      </a>
    </li>
    <?php endif; ?>
  </ul>  
</div>  

<!-- The Login Form (initially hidden) -->  
<div id="login-form-container" class="login-form-container" style="display: none;">  
  <?php include('login.php'); ?>  
</div>

<script>
async function translatePage(targetLang) {
    try {
        console.log('Starting translation to:', targetLang);
        const elements = document.querySelectorAll('[data-translate]');
        console.log('Found elements to translate:', elements.length);
        
        if (elements.length === 0) {
            console.warn('No translatable elements found. Make sure elements have data-translate attribute.');
            return;
        }

        const textsToTranslate = [];
        
        elements.forEach(el => {
            const original = el.getAttribute('data-translate');
            if (original) {
                textsToTranslate.push({
                    element: el,
                    text: original
                });
            }
        });

        console.log('Texts to translate:', textsToTranslate.length);

        // Batch translate in groups of 10 to avoid overloading
        const batchSize = 10;
        for (let i = 0; i < textsToTranslate.length; i += batchSize) {
            const batch = textsToTranslate.slice(i, i + batchSize);
            console.log(`Processing batch ${i/batchSize + 1}:`, batch);
            
            try {
                const response = await fetch('translate_batch.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        texts: batch.map(item => item.text),
                        target_lang: targetLang
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! status: ${response.status}, body: ${errorText}`);
                }

                const translations = await response.json();
                console.log('Received translations:', translations);
                
                // Apply translations
                translations.forEach((translation, index) => {
                    if (translation.success) {
                        batch[index].element.innerHTML = translation.text;
                        console.log(`Translated "${batch[index].text}" to "${translation.text}"`);
                    } else {
                        console.error('Translation failed:', translation.error);
                    }
                });
            } catch (error) {
                console.error('Batch translation error:', error);
            }
        }
    } catch (error) {
        console.error('Translation error:', error);
    }
}

// Language switcher functionality
document.addEventListener('DOMContentLoaded', function() {
    const langSwitch = document.getElementById('lang-switch');
    const langDropdown = document.getElementById('lang-dropdown');

    if (!langSwitch || !langDropdown) {
        console.error('Language switcher elements not found!');
        return;
    }

    // Toggle dropdown
    langSwitch.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        langDropdown.style.display = langDropdown.style.display === 'block' ? 'none' : 'block';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!langSwitch.contains(e.target) && !langDropdown.contains(e.target)) {
            langDropdown.style.display = 'none';
        }
    });

    // Handle language selection
    langDropdown.querySelectorAll('a[data-langcode]').forEach(function(link) {
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const langCode = this.getAttribute('data-langcode');
            const langName = this.textContent;
            
            console.log('Language selected:', langCode, langName);
            
            try {
                // Update UI
                langSwitch.innerHTML = '🌐 ' + langName + ' <i class="fa fa-caret-down"></i>';
                langDropdown.style.display = 'none';
                
                // Save preference
                localStorage.setItem('selectedLang', langCode);
                
                // Update server
                const response = await fetch('update_language.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ lang: langCode })
                });

                if (!response.ok) {
                    throw new Error('Failed to update language preference on server');
                }

                // Translate page
                await translatePage(langCode);
            } catch (error) {
                console.error('Language change error:', error);
            }
        });
    });

    // Apply saved language on page load
    const savedLang = localStorage.getItem('selectedLang');
    if (savedLang && savedLang !== 'en') {
        console.log('Applying saved language:', savedLang);
        const langLink = document.querySelector(`#lang-dropdown a[data-langcode="${savedLang}"]`);
        if (langLink) {
            langSwitch.innerHTML = '🌐 ' + langLink.textContent + ' <i class="fa fa-caret-down"></i>';
            translatePage(savedLang).catch(error => {
                console.error('Error applying saved language:', error);
            });
        }
    }
});

function toggleMenu() {
  const menu = document.getElementById('mobileMenu');
  menu.classList.toggle('show');
}

function toggleLoginForm() {
  window.location.href = 'login.php';
}

function toggleRegisterForm() {
  // Check current page
  const currentPage = window.location.pathname.split('/').pop();
  if (currentPage === 'register.php') {
    window.location.href = 'index.php';
  } else {
    window.location.href = 'register.php';
  }
}
</script>

<!-- Styles -->  
<style>  
/* ================= Top Header ================= */  
.top-header {  
  background-color: #004153;  
  color: white;  
  padding: 4px 0 2px 0;
  font-size: 15px;  
  text-align: center;  
  margin: 0;  
  border: 0;
  position: fixed;
  width: 100%;
  top: 64px; /* Height of main-header */
  z-index: 998;
}  

body {  
  margin: 0;  
  padding: 0;
  padding-top: 90px; /* Combined height of both headers */
}  

/* ================= Main Header ================= */  
.main-header {  
  background-color: white;  
  border-bottom: 2px solid #e0e0e0;  
  display: flex;  
  align-items: center;  
  justify-content: space-between;  
  padding: 6px 20px;
  min-height: unset;
  position: fixed;
  width: 100%;
  top: 0;
  left: 0;
  z-index: 999;
  box-sizing: border-box;
  height: 64px;
}  

.header-left {  
  display: flex;  
  align-items: center;  
  gap: 15px;  
}  

.hamburger-menu {  
  font-size: 26px;  
  cursor: pointer;  
  user-select: none;  
}  

.main-logo {  
  height: 50px;  
  width: auto;  
}  

.top-nav ul {  
  list-style: none;  
  display: flex;  
  align-items: center;  
  gap: 20px;  
  margin: 0;  
  padding: 0;  
}  

.top-nav ul li a {  
  text-decoration: none;  
  color: #004D4D;  
  font-size: 15px;  
  font-weight: 600;  
  display: flex;  
  align-items: center;  
  gap: 5px;  
  transition: color 0.3s; 
  font-weight: bold;
}  

.top-nav ul li a:hover {  
  color: #f4c542;  
}  

/* ================= Dropdown Menu ================= */  
.dropdown {  
  position: relative;  
  display: inline-block;  
}  

.dropbtn {  
  cursor: pointer;  
  text-decoration: none;  
  color: #004153;  
  font-weight: 600;  
  font-size: 14px;  
  display: flex;  
  align-items: center;  
  gap: 5px;  
  user-select: none;  
}  

.dropdown-content {  
  display: none;  
  position: absolute;  
  background-color: white;  
  min-width: 160px;  
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);  
  z-index: 1001;  
  right: 0;  
  border: 1px solid #ddd;  
  border-radius: 4px;  
  padding: 8px 0;  
}  

.dropdown-content a {  
  color: #004153;  
  padding: 10px 20px;  
  text-decoration: none;  
  display: block;  
  font-weight: normal;  
  font-size: 14px;  
}  

.dropdown-content a:hover {  
  background-color: #f4c542;  
  color: white;  
}  

.dropdown:hover .dropdown-content {  
  display: block;  
}  

.dropdown:hover .dropbtn {  
  color: #f4c542;  
}  

/* ================= Sidebar ================= */  
.sidebar {  
  position: fixed;  
  top: 0;  
  left: 0;  
  width: 260px;  
  height: 100%;  
  background-color: white;  
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);  
  padding: 20px;  
  overflow-y: auto;  
  transform: translateX(-100%);  
  transition: transform 0.3s ease;  
  z-index: 1000;  
}  

.sidebar.show {  
  transform: translateX(0);  
}  

.sidebar-header {  
  display: flex;  
  align-items: center;  
  justify-content: space-between;  
  margin-bottom: 30px;  
}  

.sidebar-logo {  
  max-height: 50px;  
  width: auto;  
}  

.close-btn {  
  font-size: 26px;  
  cursor: pointer;  
  user-select: none;  
}  

.menu-items {  
  list-style: none;  
  padding: 0;  
}  

.menu-items li {  
  margin-bottom: 15px;  
}  

.menu-items li a {  
  text-decoration: none;  
  color: #004153;  
  font-weight: 600;  
  font-size: 18px;  
}  

.menu-items li a small {  
  font-weight: normal;  
  font-size: 12px;  
  color: #666;  
}  

.lang-modal-overlay {
  position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.15); z-index: 2000; display: flex; align-items: center; justify-content: center;
}
.lang-modal {
  background: #fff; border-radius: 18px; padding: 32px 32px 24px 32px; min-width: 700px; max-width: 90vw; position: relative;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}
.lang-modal-header {
  display: flex; gap: 12px; margin-bottom: 24px;
}
.lang-tab {
  background: #f6f5fa; border: none; border-radius: 14px; padding: 16px 36px; font-size: 1.2rem; font-weight: 500; cursor: pointer;
}
.lang-tab.active { background: #222; color: #fff; }
.lang-search {
  width: 100%; padding: 16px; border-radius: 12px; border: 1px solid #ddd; font-size: 1.1rem; margin-bottom: 32px;
}
.lang-list-section h2 { font-size: 2rem; margin-bottom: 24px; }
.lang-list {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;
}
.lang-item {
  border: 2px solid #eee; border-radius: 12px; padding: 18px 16px; margin-bottom: 10px; cursor: pointer; transition: border 0.2s;
}
.lang-item.selected, .lang-item:hover { border: 2px solid #222; }
.lang-item-title { font-size: 1.2rem; font-weight: 700; }
.lang-item-region { color: #888; font-size: 1rem; }
.lang-modal-close {
  position: absolute; top: 18px; right: 18px; background: none; border: none; font-size: 1.5rem; cursor: pointer;
}
@media (max-width: 900px) {
  .lang-modal { min-width: 90vw; }
  .lang-list { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
  .lang-modal { min-width: 98vw; padding: 12px 4px; }
  .lang-list { grid-template-columns: 1fr; }
}
</style>

<?php
// Server-side translation function using LibreTranslate API
function libretranslate_php($text, $target_language = 'es', $source_language = 'en') {
    $url = 'https://libretranslate.de/translate';
    $data = array(
        'q' => $text,
        'source' => $source_language,
        'target' => $target_language,
        'format' => 'text'
    );
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    if ($response === false) {
        curl_close($ch);
        return 'Error: ' . curl_error($ch);
    } else {
        $response_data = json_decode($response, true);
        curl_close($ch);
        return $response_data['translatedText'] ?? '';
    }
}
// Example usage:
// echo libretranslate_php('Hello, how are you?', 'es');
?>