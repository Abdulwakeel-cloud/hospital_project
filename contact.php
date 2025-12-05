<?php
require_once "includes/session_config.php";
require_once "includes/dbh.php";

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Enhanced validation
    if (empty($name)) {
        $errors[] = "Name is required.";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Name is too long. Maximum 100 characters allowed.";
    } elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $name)) {
        $errors[] = "Name contains invalid characters.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } elseif (strlen($email) > 255) {
        $errors[] = "Email address is too long.";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } else {
        // Remove common phone formatting characters
        $phoneDigits = preg_replace('/[\s\-\(\)\+]/', '', $phone);
        // Check if phone contains only digits and has reasonable length
        if (!preg_match('/^\d+$/', $phoneDigits)) {
            $errors[] = "Phone number contains invalid characters.";
        } elseif (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
            $errors[] = "Phone number must be between 10 and 15 digits.";
        }
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    } elseif (strlen($subject) > 200) {
        $errors[] = "Subject is too long. Maximum 200 characters allowed.";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required.";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long.";
    } elseif (strlen($message) > 2000) {
        $errors[] = "Message is too long. Maximum 2000 characters allowed.";
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        try {
            // Insert into contacts table
            $stmt = $db->prepare("INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES (:name, :email, :phone, :subject, :message, NOW())");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->execute();
            
            // Set success message
            $success = true;
            
            // Clear form data after successful submission
            $_POST = [];
            
            // Optional: Send email notification to admin
            // You can uncomment and configure this if you want email notifications
            
            $to = "wakinographix@gmail.com";
            $emailSubject = "New Contact Form Submission: " . $subject;
            $emailMessage = "Name: $name\n";
            $emailMessage .= "Email: $email\n";
            $emailMessage .= "Phone: $phone\n";
            $emailMessage .= "Subject: $subject\n\n";
            $emailMessage .= "Message:\n$message";
            $headers = "From: wakinographix@gmail.com\r\n";
            $headers .= "Reply-To: $email\r\n";
            mail($to, $emailSubject, $emailMessage, $headers);
            
            
        } catch (PDOException $e) {
            // Log error for debugging
            error_log('Contact form insert error: ' . $e->getMessage());
            $errors[] = "Failed to submit your message. Please try again later.";
            $success = false;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us – Medixal</title>
    <meta name="description" content="Get in touch with Medixal. We're here to help with all your medical needs." />
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
    <style>
      .contact-section {
        padding: 60px 0;
      }
      .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
      }
      .contact-info {
        display: grid;
        gap: 24px;
      }
      .info-item {
        display: flex;
        align-items: start;
        gap: 16px;
        padding: 20px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        transition: all var(--transition-base) var(--ease);
      }
      .info-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow);
        border-color: var(--color-primary);
      }
      .info-item i {
        font-size: 24px;
        color: var(--color-primary);
        min-width: 30px;
      }
      .info-item h4 {
        margin: 0 0 4px;
        color: var(--color-text);
      }
      .info-item p {
        margin: 0;
        color: var(--color-muted);
      }
      .contact-form {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 32px;
        box-shadow: var(--shadow);
      }
      .form-group {
        margin-bottom: 20px;
      }
      .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--color-text);
      }
      .form-group input,
      .form-group textarea,
      .form-group select {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--color-bg);
        font-family: inherit;
        font-size: 1rem;
        color: var(--color-text);
        transition: all var(--transition-fast) var(--ease);
      }
      .form-group input:focus,
      .form-group textarea:focus,
      .form-group select:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(11, 99, 206, 0.1);
      }
      html[data-theme="dark"] .form-group input:focus,
      html[data-theme="dark"] .form-group textarea:focus,
      html[data-theme="dark"] .form-group select:focus {
        box-shadow: 0 0 0 3px rgba(11, 99, 206, 0.2);
      }
      .form-group input.error,
      .form-group textarea.error,
      .form-group select.error {
        border-color: #e05252;
        box-shadow: 0 0 0 3px rgba(224, 82, 82, 0.1);
      }
      html[data-theme="dark"] .form-group input.error,
      html[data-theme="dark"] .form-group textarea.error,
      html[data-theme="dark"] .form-group select.error {
        border-color: #e05252;
        box-shadow: 0 0 0 3px rgba(224, 82, 82, 0.2);
      }
      .form-group textarea {
        resize: vertical;
        min-height: 120px;
      }
      .map-container {
        margin-top: 60px;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        height: 450px;
      }
      .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
      }
      .alert {
        padding: 16px;
        border-radius: var(--radius-sm);
        margin-bottom: 24px;
      }
      .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
      }
      html[data-theme="dark"] .alert-success {
        background: rgba(31, 157, 134, 0.2);
        color: #1f9d86;
        border: 1px solid rgba(31, 157, 134, 0.3);
      }
      .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
      }
      html[data-theme="dark"] .alert-error {
        background: rgba(220, 53, 69, 0.2);
        color: #e05252;
        border: 1px solid rgba(220, 53, 69, 0.3);
      }
      .alert ul {
        margin: 0;
        padding-left: 20px;
      }
      @media (max-width: 900px) {
        .contact-grid {
          grid-template-columns: 1fr;
        }
        .map-container {
          height: 350px;
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
            <li><a href="index.php#services">Services</a></li>
            <li><a href="index.php#blog">Blog</a></li>
            <li><a href="contact.php">Contact</a></li>
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
            <h1 style="font-size: clamp(2rem, 4vw, 3.5rem); margin-bottom: 1rem;">Contact Us</h1>
            <p style="font-size: 1.2rem;">We're here to help. Get in touch with us today.</p>
          </header>
        </div>
      </section>

      <section class="contact-section" id="contact">
        <div class="container">
          <div class="contact-grid">
            <div class="contact-info fade-in">
              <div class="info-item">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                  <h4>Our Location</h4>
                  <p>123 Health Street, Wellness City<br>Medical District, 12345</p>
                </div>
              </div>
              <div class="info-item">
                <i class="fa-solid fa-phone"></i>
                <div>
                  <h4>Phone Number</h4>
                  <p>+1 (555) 010-2025<br>+1 (555) 010-2026</p>
                </div>
              </div>
              <div class="info-item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                  <h4>Email Address</h4>
                  <p>info@medixal.com<br>support@medixal.com</p>
                </div>
              </div>
              <div class="info-item">
                <i class="fa-solid fa-clock"></i>
                <div>
                  <h4>Working Hours</h4>
                  <p>Monday - Friday: 8:00 AM - 8:00 PM<br>Saturday - Sunday: 9:00 AM - 5:00 PM</p>
                </div>
              </div>
            </div>

            <div class="contact-form fade-in">
              <h2 style="margin-top: 0; margin-bottom: 24px; color: var(--color-primary);">Send us a Message</h2>
              
              <?php if ($success): ?>
                <div class="alert alert-success">
                  <i class="fa-solid fa-check-circle"></i> Thank you! Your message has been sent successfully. We'll get back to you soon.
                </div>
              <?php endif; ?>
              
              <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                  <strong><i class="fa-solid fa-exclamation-circle"></i> Please fix the following errors:</strong>
                  <ul>
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <form method="POST" action="" id="contactForm">
                <div class="form-group">
                  <label for="name"><i class="fa-solid fa-user"></i> Full Name *</label>
                  <input type="text" id="name" name="name" placeholder="Your full name" required 
                         value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                  <label for="email"><i class="fa-solid fa-envelope"></i> Email Address *</label>
                  <input type="email" id="email" name="email" placeholder="your.email@example.com" required 
                         value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                  <label for="phone"><i class="fa-solid fa-phone"></i> Phone Number *</label>
                  <input type="tel" id="phone" name="phone" placeholder="+1 (555) 000-0000" required 
                         value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                  <label for="subject"><i class="fa-solid fa-tag"></i> Subject *</label>
                  <select id="subject" name="subject" required>
                    <option value="">Select a subject</option>
                    <option value="General Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                    <option value="Appointment" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Appointment') ? 'selected' : ''; ?>>Appointment</option>
                    <option value="Emergency" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                    <option value="Feedback" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Feedback') ? 'selected' : ''; ?>>Feedback</option>
                    <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="message"><i class="fa-solid fa-comment"></i> Message *</label>
                  <textarea id="message" name="message" placeholder="Tell us how we can help you..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="submit" class="btn btn-primary btn-block">
                  <i class="fa-solid fa-paper-plane"></i> Send Message
                </button>
              </form>
            </div>
          </div>

          <div class="map-container fade-in">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.184132576834!2d-73.98811768459398!3d40.75889597932681!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Medixal Hospital Location">
            </iframe>
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
    <script>
      // Form validation enhancement
      document.getElementById('contactForm')?.addEventListener('submit', function(e) {
        const form = this;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
          input.classList.remove('error');
          if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
          }
        });

        if (!isValid) {
          e.preventDefault();
          alert('Please fill in all required fields.');
        }
      });
    </script>
  </body>
</html>

