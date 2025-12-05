<?php
require_once "includes/session_config.php";
require_once "includes/blog_data.php";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Latest Articles – Medixal</title>
    <meta name="description" content="Insights and news from our medical experts." />
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
            <h1 style="font-size: clamp(2rem, 4vw, 3.5rem); margin-bottom: 1rem;">Latest Articles</h1>
            <p style="font-size: 1.2rem;">Insights and news from our medical experts</p>
          </header>
        </div>
      </section>

      <section class="blog" id="blog" style="padding: 60px 0;">
        <div class="container">
          <div class="blog-grid">
           <?php if(!empty($posts)) : ?>
              <?php foreach($posts as $post) : ?>
            <article class="post fade-in">
              <img src="admin-panel/uploads/blog/<?php echo htmlspecialchars($post->image); ?>" alt="<?php echo htmlspecialchars($post->title); ?>" loading="lazy" />
              <div class="post-content">
                <h3><?php echo htmlspecialchars($post->title); ?></h3>
                <p class="meta">Created at <?php echo htmlspecialchars(date('F j, Y', strtotime($post->created_at))); ?></p>
                <?php if(!empty($post->content)) : ?>
                  <p style="color: var(--color-muted); margin: 10px 0;"><?php echo htmlspecialchars(substr($post->content, 0, 150)) . '...'; ?></p>
                <?php endif; ?>
                <a href="blog-details.php?id=<?php echo $post->id; ?>" class="read">Read More <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </article>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="fade-in" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <p style="font-size: 1.2rem; color: var(--color-muted);">No posts found.</p>
                <a href="index.php" class="btn btn-primary" style="margin-top: 20px;">Back to Home</a>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($totalPosts > 0 && $totalPages > 1): ?>
          <nav class="pagination-nav" aria-label="Blog pagination" style="margin-top: 40px; text-align: center;">
            <?php
              $baseUrl = 'blog.php';
              $query = $_GET;
            ?>

            <ul class="pagination-list" style="display: inline-flex; list-style: none; gap: 8px; padding: 0; margin: 0;">
              <?php if ($page > 1): ?>
                <?php $query['page'] = $page - 1; ?>
                <li>
                  <a class="btn btn-primary" href="<?php echo htmlspecialchars($baseUrl . '?' . http_build_query($query)); ?>">&laquo; Prev</a>
                </li>
              <?php endif; ?>

              <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php $query['page'] = $p; ?>
                <li>
                  <a
                    class="btn <?php echo $p === $page ? 'btn-accent' : 'btn-primary'; ?>"
                    href="<?php echo htmlspecialchars($baseUrl . '?' . http_build_query($query)); ?>"
                  >
                    <?php echo $p; ?>
                  </a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <?php $query['page'] = $page + 1; ?>
                <li>
                  <a class="btn btn-primary" href="<?php echo htmlspecialchars($baseUrl . '?' . http_build_query($query)); ?>">Next &raquo;</a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
          <?php endif; ?>
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

