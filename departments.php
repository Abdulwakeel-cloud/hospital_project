<?php
require_once "includes/session_config.php";
require_once "includes/dbh.php";

$categories = "SELECT * FROM categories ORDER BY id DESC";
$category_stmt = $db->prepare($categories);
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Departments – Medixal</title>
    <meta name="description" content="Explore all our medical departments and specialized care services." />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Initial theme applied early to avoid flash -->
    <script src="theme-init.js" onerror="document.documentElement.classList.add('script-error')"></script>

    <link rel="stylesheet" href="style.css" onerror="document.documentElement.classList.add('style-error')" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  </head>
  <body>
    <header class="site-header" id="header">
      <div class="container header-inner">
        <a href="index.php" class="logo">Medixal</a>
        <nav class="nav" id="nav">
          <ul class="nav-list">
            <li><a href="index.php#hero">Home</a></li>
            <li><a href="index.php#team">Doctors</a></li>
            <li><a href="index.php#about">About Us</a></li>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#blog">Blog</a></li>
            <li><a href="index.php#footer">Contact</a></li>
          </ul>
        </nav>
        <div class="header-cta">
          <a href="index.php#appointment" class="btn btn-accent">Make An Appointment</a>
          <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" aria-pressed="false">
            <i class="fa-solid fa-moon"></i>
          </button>
          <button class="hamburger" id="hamburger" aria-label="Open menu">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <main>
      <section class="hero page-hero">
        <div class="container">
          <header class="section-head fade-in">
            <h1 class="hero-title">Our Departments</h1>
            <p class="hero-subtitle">Comprehensive care across specialized medical fields</p>
          </header>
        </div>
      </section>

      <section class="departments" id="departments">
        <div class="container">
          <div class="dept-grid">
            <?php if(!empty($categories)) : ?>
              <?php 
              $delay = 1;
              foreach($categories as $category) : 
              ?>
            <article class="card fade-in delay-<?php echo $delay; ?>">
              <?php if(!empty($category->img)) : ?>
                <img src="admin-panel/uploads/services/<?php echo htmlspecialchars($category->img); ?>" 
                     alt="<?php echo htmlspecialchars($category->category_name); ?>" />
              <?php else : ?>
                <i class="fa-solid fa-hospital"></i>
              <?php endif; ?>
              <h3><?php echo htmlspecialchars($category->category_name); ?></h3>
              <p>Comprehensive care and specialized treatment in <?php echo htmlspecialchars($category->category_name); ?>.</p>
              <a href="index.php#appointment" class="btn btn-primary">Book Appointment</a>
            </article>
              <?php 
              $delay++;
              if($delay > 5) $delay = 1;
              endforeach; 
              ?>
            <?php else: ?>
              <div class="fade-in dept-empty">
                <p>No departments available at the moment.</p>
                <a href="index.php" class="btn btn-primary">Back to Home</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>

    <footer class="site-footer" id="footer">
      <div class="container footer-grid">
        <div class="footer-col">
          <a href="index.php" class="logo">Medixal</a>
          <p>Trusted medical services delivering personalized care with advanced technology.</p>
        </div>
        <div class="footer-col">
          <h4>Links</h4>
          <ul>
            <li><a href="index.php#hero">Home</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Resources</h4>
          <ul>
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#pricing">Pricing</a></li>
            <li><a href="index.php#team">Team</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Contact Info</h4>
          <ul class="contact-list">
            <li><i class="fa-solid fa-location-dot"></i> 123 Health St, Wellness City</li>
            <li><i class="fa-solid fa-phone"></i> +1 (555) 010‑2025</li>
            <li><i class="fa-solid fa-envelope"></i> info@medixal.com</li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        <div class="container">
          © <span id="year"></span> Medixal. All rights reserved.
        </div>
      </div>
    </footer>

    <div id="assetNotice" class="asset-notice" role="status" aria-live="polite">Some assets failed to load. Please refresh or check your connection.</div>
    <script src="script.js" defer onerror="document.documentElement.classList.add('script-error')"></script>
  </body>
</html>

