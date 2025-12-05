<?php
require_once "includes/session_config.php";
require_once "includes/dbh.php";

// Get post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch post details
$sql = "SELECT * FROM posts WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_OBJ);

if (!$post) {
    header("Location: index.php");
    exit();
}

// Fetch related posts (excluding current post)
$relatedSql = "SELECT * FROM posts WHERE id != :id ORDER BY id DESC LIMIT 3";
$relatedStmt = $db->prepare($relatedSql);
$relatedStmt->bindParam(':id', $post_id, PDO::PARAM_INT);
$relatedStmt->execute();
$relatedPosts = $relatedStmt->fetchAll(PDO::FETCH_OBJ);

// Get recent posts for sidebar
$recentSql = "SELECT * FROM posts WHERE id != :id ORDER BY created_at DESC LIMIT 3";
$recentStmt = $db->prepare($recentSql);
$recentStmt->bindParam(':id', $post_id, PDO::PARAM_INT);
$recentStmt->execute();
$recentPosts = $recentStmt->fetchAll(PDO::FETCH_OBJ);
?>  
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php echo htmlspecialchars($post->title ?? 'Blog Post'); ?> – Medixal</title>
    <meta name="description" content="<?php echo htmlspecialchars(substr($post->subtitle ?? $post->content ?? '', 0, 160)); ?>" />
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
      .blog-details {
        padding: 60px 0;
      }
      .blog-content-wrapper {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
      }
      .blog-main {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 40px;
      }
      .blog-main img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        margin-bottom: 30px;
      }
      .blog-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        color: var(--color-muted);
        font-size: 0.95rem;
      }
      .blog-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .blog-main h1 {
        font-size: 2.5rem;
        margin-bottom: 20px;
        color: var(--color-text);
        line-height: 1.3;
      }
      .blog-main .content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: var(--color-text);
        margin-bottom: 30px;
      }
      .blog-main .content p {
        margin-bottom: 20px;
      }
      .blog-main .content img {
        width: 100%;
        border-radius: var(--radius-sm);
        margin: 30px 0;
      }
      .blog-main blockquote {
        border-left: 4px solid var(--color-primary);
        padding-left: 20px;
        margin: 30px 0;
        font-style: italic;
        color: var(--color-muted);
        background: var(--color-soft);
        padding: 20px;
        border-radius: var(--radius-sm);
      }
      .blog-main blockquote i {
        color: var(--color-primary);
        margin-right: 10px;
      }
      .blog-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid var(--border);
      }
      .blog-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 600;
        transition: all var(--transition-fast) var(--ease);
      }
      .blog-nav a:hover {
        color: var(--color-accent);
        gap: 15px;
      }
      .blog-sidebar {
        display: flex;
        flex-direction: column;
        gap: 30px;
      }
      .sidebar-widget {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 25px;
      }
      .sidebar-widget h3 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: var(--color-primary);
      }
      .sidebar-widget .search-form {
        display: flex;
        gap: 10px;
      }
      .sidebar-widget .search-form input {
        flex: 1;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--color-bg);
        color: var(--color-text);
        font-size: 1rem;
      }
      .sidebar-widget .search-form button {
        padding: 12px 20px;
        background: var(--color-primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all var(--transition-fast) var(--ease);
      }
      .sidebar-widget .search-form button:hover {
        background: var(--color-accent);
      }
      .sidebar-widget ul {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      .sidebar-widget ul li {
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
      }
      .sidebar-widget ul li:last-child {
        border-bottom: none;
      }
      .sidebar-widget ul li a {
        color: var(--color-text);
        text-decoration: none;
        transition: color var(--transition-fast) var(--ease);
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .sidebar-widget ul li a:hover {
        color: var(--color-primary);
      }
      .recent-post {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid var(--border);
      }
      .recent-post:last-child {
        border-bottom: none;
      }
      .recent-post img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: var(--radius-sm);
      }
      .recent-post-content h4 {
        font-size: 1rem;
        margin-bottom: 5px;
        line-height: 1.4;
      }
      .recent-post-content h4 a {
        color: var(--color-text);
        text-decoration: none;
        transition: color var(--transition-fast) var(--ease);
      }
      .recent-post-content h4 a:hover {
        color: var(--color-primary);
      }
      .recent-post-content .date {
        font-size: 0.85rem;
        color: var(--color-muted);
      }
      .tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
      }
      .tags a {
        padding: 6px 12px;
        background: var(--color-soft);
        color: var(--color-text);
        text-decoration: none;
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        transition: all var(--transition-fast) var(--ease);
      }
      .tags a:hover {
        background: var(--color-primary);
        color: #fff;
      }
      .comments-section {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid var(--border);
      }
      .comments-section h3 {
        font-size: 1.8rem;
        margin-bottom: 30px;
        color: var(--color-primary);
      }
      .comment {
        display: flex;
        gap: 20px;
        padding: 20px 0;
        border-bottom: 1px solid var(--border);
      }
      .comment:last-child {
        border-bottom: none;
      }
      .comment img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
      }
      .comment-content h4 {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: var(--color-text);
      }
      .comment-content .date {
        font-size: 0.85rem;
        color: var(--color-muted);
        margin-bottom: 10px;
      }
      .comment-content p {
        color: var(--color-text);
        line-height: 1.6;
      }
      .comment-form {
        margin-top: 40px;
        padding-top: 40px;
        border-top: 1px solid var(--border);
      }
      .comment-form h3 {
        font-size: 1.8rem;
        margin-bottom: 30px;
        color: var(--color-primary);
      }
      .form-group {
        margin-bottom: 20px;
      }
      .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--color-text);
        font-weight: 600;
      }
      .form-group input,
      .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--color-bg);
        color: var(--color-text);
        font-size: 1rem;
        font-family: inherit;
      }
      .form-group textarea {
        min-height: 150px;
        resize: vertical;
      }
      .form-group input[type="checkbox"] {
        width: auto;
        margin-right: 8px;
      }
      .form-group .checkbox-label {
        display: flex;
        align-items: center;
        font-weight: normal;
      }
      @media (max-width: 900px) {
        .blog-content-wrapper {
          grid-template-columns: 1fr;
        }
        .blog-main h1 {
          font-size: 2rem;
        }
        .blog-main img {
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
            <span>Blog Details</span>
          </div>
        </div>
      </section>

      <section class="blog-details">
        <div class="container">
          <div class="blog-content-wrapper">
            <div class="blog-main">
              <?php if(!empty($post->image)) : ?>
                <img src="admin-panel/uploads/blog/<?php echo htmlspecialchars($post->image); ?>" alt="<?php echo htmlspecialchars($post->title ?? 'Blog Post'); ?>" />
              <?php else : ?>
                <img src="resources/post_1.jpeg" alt="<?php echo htmlspecialchars($post->title ?? 'Blog Post'); ?>" />
              <?php endif; ?>
              
              <div class="blog-meta">
                <span><i class="fa-solid fa-calendar"></i> <?php echo htmlspecialchars(date('d M Y', strtotime($post->created_at ?? 'now'))); ?></span>
                <span><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($post->category ?? 'Technology'); ?></span>
                <span><i class="fa-solid fa-comments"></i> 2 Comments</span>
              </div>

              <h1><?php echo htmlspecialchars($post->title ?? 'Blog Post'); ?></h1>

              <div class="content">
                <?php if(!empty($post->subtitle)) : ?>
                  <p style="font-size: 1.2rem; color: var(--color-muted); margin-bottom: 20px;"><?php echo nl2br(htmlspecialchars($post->subtitle)); ?></p>
                <?php endif; ?>
                
                <?php if(!empty($post->content)) : ?>
                  <p><?php echo nl2br(htmlspecialchars($post->content)); ?></p>
                <?php elseif(!empty($post->body)) : ?>
                  <p><?php echo nl2br(htmlspecialchars($post->body)); ?></p>
                <?php else : ?>
                  <p>Vast numbers of employees now work remotely, and it's too late to develop a set of remote-work policies if you didn't already have one. But there are ways to make the remote-work experience productive and engaging for employees.</p>
                  <p>Use both direct conversations and indirect observations to get visibility into employees' challenges and concerns. Use every opportunity to make clear to employees that you support and care them. To facilitate regular conversations between managers and employees, provide managers with guidance on how best to broach sensitive subjects arising from the COVID-19 pandemic, including alternative work models, job security and prospects, impact on staffing.</p>
                <?php endif; ?>

                <img src="resources/casestydy_2.jpeg" alt="Content Image" />

                <p>The third Monday of January is supposed to be the most depressing day of the year. Whether you believe that or not, the long nights, cold weather, and trying to keep to new year resolutions are all probably getting to you a little by now. To make matters worse many will still be recovering from their Christmas spending. So how can you make today</p>

                <blockquote>
                  <i class="fa-solid fa-quote-left"></i>
                  We appreciate the consistent high-quality service provided by their team goes above and beyond concerns promptly
                </blockquote>

                <p>Vast numbers of employees now work remotely, and it's too late to develop a set of remote-work policies if you didn't already have one. But there are ways to make the remote-work experience productive and engaging for employees.</p>
                <p>Use both direct conversations and indirect observations to get visibility into employees' challenges and concerns. Use every opportunity to make clear to employees that you support and care them. To facilitate regular conversations between managers and employees, provide managers with guidance on how best to broach sensitive subjects arising from the COVID-19 pandemic</p>
              </div>

              <div class="tags">
                <a href="blog.php">Medical</a>
                <a href="blog.php">Rehab</a>
                <a href="blog.php">Psychology</a>
                <a href="blog.php">Eyecare</a>
                <a href="blog.php">Dental</a>
                <a href="blog.php">Phytotherapy</a>
                <a href="blog.php">Hospitality</a>
                <a href="blog.php">Senior Care</a>
              </div>

              <div class="blog-nav">
                <a href="blog.php"><i class="fa-solid fa-arrow-left"></i> Prev Post</a>
                <a href="blog.php">Next Post <i class="fa-solid fa-arrow-right"></i></a>
              </div>

              <div class="comments-section">
                <h3>2 Comments</h3>
                
                <div class="comment">
                  <img src="resources/team_1.jpeg" alt="Alexander Cameron" />
                  <div class="comment-content">
                    <h4>Alexander Cameron</h4>
                    <div class="date">July 28, 2024</div>
                    <p>Legal expertise and is client focused we enhance entrepreneurial environment flexible supportive, allowing our lawyers introduced</p>
                    <a href="#" style="color: var(--color-primary); text-decoration: none; font-size: 0.9rem; margin-top: 10px; display: inline-block;">Reply</a>
                  </div>
                </div>

                <div class="comment">
                  <img src="resources/team_2.jpeg" alt="Brooklyn Simmons" />
                  <div class="comment-content">
                    <h4>Brooklyn Simmons</h4>
                    <div class="date">Jan 28, 2024</div>
                    <p>Legal expertise and is client focused we enhance entrepreneurial environment flexible supportive, allowing our lawyers introduced</p>
                    <a href="#" style="color: var(--color-primary); text-decoration: none; font-size: 0.9rem; margin-top: 10px; display: inline-block;">Reply</a>
                  </div>
                </div>
              </div>

              <div class="comment-form">
                <h3>Leave a Comment</h3>
                <form>
                  <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea id="comment" name="comment" placeholder="Your comment" required></textarea>
                  </div>
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your name" required />
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Your email" required />
                  </div>
                  <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" placeholder="Your website (optional)" />
                  </div>
                  <div class="form-group">
                    <label class="checkbox-label">
                      <input type="checkbox" name="save" />
                      Save my name, email, and website in this browser for the next time I comment.
                    </label>
                  </div>
                  <button type="submit" class="btn btn-accent">Send Message</button>
                </form>
              </div>
            </div>

            <div class="blog-sidebar">
              <div class="sidebar-widget">
                <h3>Search</h3>
                <form class="search-form">
                  <input type="search" placeholder="Search..." />
                  <button type="submit"><i class="fa-solid fa-search"></i></button>
                </form>
              </div>

              <div class="sidebar-widget">
                <h3>Categories</h3>
                <ul>
                  <li><a href="blog.php">Medical <span>(18)</span></a></li>
                  <li><a href="blog.php">Eye Care <span>(5)</span></a></li>
                  <li><a href="blog.php">Dental <span>(2)</span></a></li>
                  <li><a href="blog.php">Consulting <span>(11)</span></a></li>
                  <li><a href="blog.php">Council Rehab <span>(4)</span></a></li>
                  <li><a href="blog.php">Hospitality <span>(8)</span></a></li>
                </ul>
              </div>

              <div class="sidebar-widget">
                <h3>Recent Posts</h3>
                <?php if(!empty($recentPosts)) : ?>
                  <?php foreach($recentPosts as $recent) : ?>
                    <div class="recent-post">
                      <?php if(!empty($recent->image)) : ?>
                        <img src="admin-panel/uploads/blog/<?php echo htmlspecialchars($recent->image); ?>" alt="<?php echo htmlspecialchars($recent->title ?? 'Post'); ?>" />
                      <?php else : ?>
                        <img src="resources/post_1.jpeg" alt="Post" />
                      <?php endif; ?>
                      <div class="recent-post-content">
                        <h4><a href="blog-details.php?id=<?php echo $recent->id; ?>"><?php echo htmlspecialchars(substr($recent->title ?? 'Post', 0, 50)); ?></a></h4>
                        <div class="date"><?php echo htmlspecialchars(date('d M Y', strtotime($recent->created_at ?? 'now'))); ?></div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else : ?>
                  <div class="recent-post">
                    <img src="resources/post_1.jpeg" alt="Post" />
                    <div class="recent-post-content">
                      <h4><a href="blog.php">Planning your online business goals with a specialists.</a></h4>
                      <div class="date">26 Sep 2024</div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="sidebar-widget">
                <h3>Tags</h3>
                <div class="tags">
                  <a href="blog.php">Medical</a>
                  <a href="blog.php">Rehab</a>
                  <a href="blog.php">Psychology</a>
                  <a href="blog.php">Eyecare</a>
                  <a href="blog.php">Dental</a>
                  <a href="blog.php">Phytotherapy</a>
                  <a href="blog.php">Hospitality</a>
                  <a href="blog.php">Senior Care</a>
                </div>
              </div>
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

