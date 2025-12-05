<?php
require_once "includes/session_config.php";
require_once "includes/dbh.php";

$sql = "
    SELECT 
        d.*, 
        c.category_name AS job_name
    FROM doctors d
    LEFT JOIN categories c ON d.title = c.id
    ORDER BY d.id DESC
";

$stmt = $db->prepare($sql);
$stmt->execute();
$doctor = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Team – Medixal</title>
    <meta name="description" content="Meet our expert team of doctors and medical professionals." />
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
            <h1 style="font-size: clamp(2rem, 4vw, 3.5rem); margin-bottom: 1rem;">Our Medical Team</h1>
            <p style="font-size: 1.2rem;">Meet the professionals behind your care</p>
          </header>
        </div>
      </section>

      <section class="team" id="team" style="padding: 60px 0;">
        <div class="container">
          <div class="team-grid">
            <?php if(!empty($doctor)) : ?>
              <?php foreach($doctor as $doc) : ?>
            <article class="doctor fade-in">
              <?php if(!empty($doc["image"])) : ?>
                  <img src="admin-panel/uploads/profile/<?php echo htmlspecialchars($doc["image"]); ?>" alt="<?php echo htmlspecialchars($doc["firstname"]); ?>">
                <?php else : ?>
                     <img src="asset/default.png" alt="default image">
                <?php endif; ?>

              <h3>Dr. <?php echo htmlspecialchars($doc["firstname"] . " " . $doc["lastname"]); ?></h3>
              <p><?php echo htmlspecialchars($doc["job_name"] ?? "General Practitioner"); ?></p>
              <div class="doctor-overlay">
                <?php if(!empty($doc["facebook"])) : ?>
                  <a href="<?php echo htmlspecialchars($doc["facebook"]); ?>" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if(!empty($doc["twitter"])) : ?>
                  <a href="<?php echo htmlspecialchars($doc["twitter"]); ?>" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                <?php endif; ?>
                <?php if(!empty($doc["linkedin"])) : ?>
                  <a href="<?php echo htmlspecialchars($doc["linkedin"]); ?>" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                <?php endif; ?>
              </div>
            </article>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="fade-in" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p style="font-size: 1.2rem; color: var(--color-muted);">No doctors found.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Back to Home</a>
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

    <script src="script.js" defer></script>
  </body>
</html>

