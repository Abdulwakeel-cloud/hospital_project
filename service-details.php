<?php
require_once "includes/session_config.php";
require_once "includes/dbh.php";

// Get service ID from URL
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($service_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch service details
$sql = "SELECT * FROM services WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $service_id, PDO::PARAM_INT);
$stmt->execute();
$service = $stmt->fetch(PDO::FETCH_OBJ);

if (!$service) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php echo htmlspecialchars($service->service_name); ?> – Medixal</title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($service->description ?? '', 0, 160)); ?>" />
    <meta name="theme-color" content="#0b63ce" />
    
    <!-- Performance & Preloading -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com" />
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style" />
    
    <!-- Stylesheets -->
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
    <link rel="stylesheet" href="fonts/css/all.css">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      integrity="sha512-1LVvQqHqVbXyQ6x0kCk8GkqU7k8mHhYcWn4cWZC3H2C3lQwz7H9E3cJmYF0O2xKZtq9dVx2kQpWm8y9KpFQ7wQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <style>
      .breadcrumb {
        padding: 120px 0 60px;
        background: var(--color-soft);
      }
      .breadcrumb-inner {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--color-muted);
        font-size: 0.95rem;
      }
      .breadcrumb a {
        color: var(--color-muted);
        text-decoration: none;
        transition: color var(--transition-fast) var(--ease);
      }
      .breadcrumb a:hover {
        color: var(--color-primary);
      }
      .breadcrumb span {
        color: var(--color-text);
        font-weight: 600;
      }
      .case-details {
        padding: 60px 0;
      }
      .case-header {
        margin-bottom: 40px;
      }
      .case-header img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        margin-bottom: 30px;
      }
      .case-content h2 {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: var(--color-text);
      }
      .case-content h3 {
        font-size: 1.8rem;
        margin: 40px 0 20px;
        color: var(--color-primary);
      }
      .case-content p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--color-text);
        margin-bottom: 20px;
      }
      .case-content img {
        width: 100%;
        border-radius: var(--radius-sm);
        margin: 30px 0;
      }
      .case-meta {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 30px;
        margin-top: 40px;
      }
      .case-meta h4 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: var(--color-primary);
      }
      .case-meta ul {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .case-meta li {
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
      }
      .case-meta li:last-child {
        border-bottom: none;
      }
      .case-meta strong {
        color: var(--color-text);
      }
      .case-meta span {
        color: var(--color-muted);
      }
      .contact-box {
        background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
        color: #fff;
        border-radius: var(--radius-sm);
        padding: 40px;
        margin-top: 40px;
      }
      .contact-box h4 {
        font-size: 1.5rem;
        margin-bottom: 20px;
      }
      .contact-box ul {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .contact-box li {
        padding: 10px 0;
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .contact-box i {
        font-size: 1.2rem;
      }
      @media (max-width: 768px) {
        .case-content h2 {
          font-size: 2rem;
        }
        .case-content h3 {
          font-size: 1.5rem;
        }
        .case-header img {
          height: 250px;
        }
      }
    </style>
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
            <li class="has-dropdown">
              <button class="nav-link dropdown-toggle" aria-haspopup="true" aria-expanded="false">Pages <i class="fa-solid fa-chevron-down"></i></button>
              <ul class="dropdown" role="menu">
                <li><a href="index.php#services" role="menuitem">Services</a></li>
                <li><a href="index.php#pricing" role="menuitem">Pricing</a></li>
                <li><a href="index.php#team" role="menuitem">Team</a></li>
                <li><a href="index.php#process" role="menuitem">Process</a></li>
                <li><a href="index.php#appointment" role="menuitem">Appointment</a></li>
              </ul>
            </li>
            <li><a href="index.php#blog">Blog</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </nav>
        <div class="header-cta">
          <a href="index.php#appointment" class="btn btn-accent" id="appointmentBtn">Make An Appointment</a>
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
          <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <main>
      <section class="breadcrumb">
        <div class="container">
          <div class="breadcrumb-inner">
            <a href="index.php">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($service->service_name); ?></span>
          </div>
        </div>
      </section>

      <section class="case-details">
        <div class="container">
          <div class="case-header">
            <?php if(!empty($service->image)) : ?>
              <img src="admin-panel/uploads/services/<?php echo htmlspecialchars($service->image); ?>" alt="<?php echo htmlspecialchars($service->service_name); ?>" />
            <?php else : ?>
              <img src="resources/casestydy_1.jpeg" alt="<?php echo htmlspecialchars($service->service_name); ?>" />
            <?php endif; ?>
          </div>

          <div class="case-content">
            <h2><?php echo htmlspecialchars($service->service_name); ?></h2>
            
            <h3>Overview</h3>
            <p><?php echo nl2br(htmlspecialchars($service->description ?? 'No description available.')); ?></p>

            <h3>Features</h3>
            <p>Our <?php echo htmlspecialchars($service->service_name); ?> service provides comprehensive care and specialized treatment. We leverage advanced technology to deliver personalized, trustworthy medical care that prioritizes your well-being.</p>
            <ul style="list-style: none; padding: 0; margin: 20px 0;">
              <li style="padding: 8px 0;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i>Creating and editing content</li>
              <li style="padding: 8px 0;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i>Workflows, reporting, and content organization</li>
              <li style="padding: 8px 0;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i>User & role-based administration and security</li>
              <li style="padding: 8px 0;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i>Flexibility, scalability, and performance and analysis</li>
              <li style="padding: 8px 0;"><i class="fa-solid fa-check" style="color: var(--color-primary); margin-right: 10px;"></i>Multilingual content capabilities</li>
            </ul>

            <h3>Goal</h3>
            <p>Our goal is to provide exceptional medical care through our <?php echo htmlspecialchars($service->service_name); ?> service. We combine cutting-edge equipment with experienced professionals to deliver world-class care tailored to you.</p>

            <div class="case-meta">
              <h4>Service Information</h4>
              <ul>
                <li>
                  <strong>Service Name:</strong>
                  <span><?php echo htmlspecialchars($service->service_name); ?></span>
                </li>
                <?php if(!empty($service->created_at)) : ?>
                <li>
                  <strong>Created:</strong>
                  <span><?php echo htmlspecialchars(date('d M Y', strtotime($service->created_at))); ?></span>
                </li>
                <?php endif; ?>
                <li>
                  <strong>Category:</strong>
                  <span>Healthcare</span>
                </li>
                <li>
                  <strong>Website:</strong>
                  <span>medixal.com</span>
                </li>
              </ul>
            </div>

            <div class="contact-box">
              <h4>Need Your Help?</h4>
              <ul>
                <li><i class="fa-solid fa-phone"></i> +(323) 750-1234</li>
                <li><i class="fa-solid fa-envelope"></i> infoyour@albashti.com</li>
                <li><i class="fa-solid fa-user"></i> Abubokkor AK</li>
                <li><i class="fa-solid fa-location-dot"></i> 374 FA Tower, William S Blvd 2721, IL, USA</li>
              </ul>
            </div>
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
            <li><a href="index.php#footer">Refund</a></li>
            <li><a href="index.php#footer">Help Center</a></li>
            <li><a href="index.php#footer">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Resources</h4>
          <ul>
            <li><a href="index.php#services">Demos</a></li>
            <li><a href="index.php#services">Instructions</a></li>
            <li><a href="index.php#appointment">Personal Meeting</a></li>
            <li><a href="index.php#team">Doctor List</a></li>
            <li><a href="index.php#pricing">Refund Policy</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Office</h4>
          <ul class="contact-list">
            <li><i class="fa-solid fa-location-dot"></i> America- 66 Brooklyn golden street 600 New York. USA</li>
            <li><i class="fa-solid fa-phone"></i> +1 (212) 621-5896</li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        <div class="container">
          medixal© <span id="year"></span>. All Rights Reserved.
          <a href="index.php#footer" style="color: #bcd1f3; margin-left: 10px;">Privacy & Cookie Policy</a>
        </div>
      </div>
    </footer>

    <script src="script.js" defer></script>
  </body>
</html>

