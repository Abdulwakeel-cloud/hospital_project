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
    <title>Our Services – Medixal</title>
    <meta name="description" content="From preventive care to complex procedures." />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Initial theme (light/dark) applied before CSS to avoid flash -->
    <script>
      (function() {
        try {
          var saved = localStorage.getItem('theme');
          var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
          var theme = saved || (prefersDark ? 'dark' : 'light');
          document.documentElement.setAttribute('data-theme', theme);
        } catch (e) {
          document.documentElement.setAttribute('data-theme', 'light');
        }
      })();
    </script>

    <link rel="stylesheet" href="style.css" />
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
          <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
            <i class="fa-solid fa-moon"></i>
          </button>
          <script>
            // Update icon immediately based on current theme
            (function() {
              try {
                var theme = document.documentElement.getAttribute('data-theme') || 'light';
                var icon = document.getElementById('themeToggle')?.querySelector('i');
                if (icon) {
                  icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                }
              } catch(e) {}
            })();
          </script>
          <button class="hamburger" id="hamburger" aria-label="Open menu">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <main>
      <section class="hero" style="padding: 120px 0 60px;">
        <div class="container">
          <header class="section-head fade-in">
            <h1 style="font-size: clamp(2rem, 4vw, 3.5rem); margin-bottom: 1rem;">Our Services</h1>
            <p style="font-size: 1.2rem;">From preventive care to complex procedures</p>
          </header>
        </div>
      </section>

      <section class="services" id="services" style="padding: 60px 0;">
        <div class="container">
          <div class="carousel" data-carousel="services">
            <button class="carousel-btn prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="carousel-track">
              <?php if(!empty($categories)) : ?>
                <?php foreach($categories as $category) : ?>
              <article class="service-card fade-in">
                <?php if(!empty($category->img)) : ?>
                  <img src="admin-panel/uploads/services/<?php echo htmlspecialchars($category->img); ?>" alt="<?php echo htmlspecialchars($category->category_name); ?>" loading="lazy" />
                <?php else : ?>
                  <div style="height: 160px; background: linear-gradient(135deg, var(--color-primary), var(--color-accent)); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-hospital" style="font-size: 48px; color: #fff;"></i>
                  </div>
                <?php endif; ?>
                <div class="content">
                  <h3><?php echo htmlspecialchars($category->category_name); ?></h3>
                  <a href="index.php#appointment" class="learn">Learn More<i class="fa-solid fa-arrow-right"></i></a>
                </div>
              </article>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="fade-in" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                  <p style="font-size: 1.2rem; color: var(--color-muted);">No services available at the moment.</p>
                  <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Back to Home</a>
                </div>
              <?php endif; ?>
            </div>
            <button class="carousel-btn next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
          </div>
        </div>
      </section>

      <section class="features" id="features" style="padding: 60px 0; background: var(--color-soft);">
        <div class="container">
          <div class="feature-grid">
            <article class="feature fade-in delay-1">
              <i class="fa-solid fa-stethoscope"></i>
              <h3>Medical Service</h3>
              <p>Routine checkups and specialized treatments for all ages.</p>
            </article>
            <article class="feature fade-in delay-2">
              <i class="fa-solid fa-vials"></i>
              <h3>Radiology & Pathology Test</h3>
              <p>Accurate diagnostics with fast reporting.</p>
            </article>
            <article class="feature fade-in delay-3">
              <i class="fa-solid fa-heart-circle-check"></i>
              <h3>Heart Beat Checkup</h3>
              <p>Continuous monitoring and preventive heart care.</p>
            </article>
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
            <li><a href="index.php#footer">Contact</a></li>
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

    <script src="script.js" defer></script>
  </body>
</html>

