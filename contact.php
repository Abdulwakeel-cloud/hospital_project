<?php
require_once "includes/session_config.php";
require_once "includes/dbh.php";

$errors = [];
$success = false;
$csrfToken = $_SESSION['csrf_token'] ?? null;
if (!$csrfToken) {
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrfToken;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        $errors[] = "Invalid request. Please reload the page.";
    }
    $now = time();
    $last = $_SESSION['contact_last_time'] ?? 0;
    if ($now - $last < 10) {
        $errors[] = "Please wait a few seconds before submitting again.";
    }
    $hourBucket = (int) floor($now / 3600);
    if (!isset($_SESSION['contact_hour']) || $_SESSION['contact_hour'] !== $hourBucket) {
        $_SESSION['contact_hour'] = $hourBucket;
        $_SESSION['contact_count'] = 0;
    }
    if (($_SESSION['contact_count'] ?? 0) >= 3) {
        $errors[] = "Too many submissions. Please try again later.";
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $booking_date = trim($_POST['booking_date'] ?? '');
    $booking_time = trim($_POST['booking_time'] ?? '');
    $service_type = trim($_POST['service_type'] ?? '');

    if ($name === '') {
        $errors[] = "Name is required.";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long.";
    } elseif (strlen($name) > 100) {
        $errors[] = "Name is too long. Maximum 100 characters allowed.";
    } elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $name)) {
        $errors[] = "Name contains invalid characters.";
    }

    if ($email === '') {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } elseif (strlen($email) > 255) {
        $errors[] = "Email address is too long.";
    }

    if ($phone === '') {
        $errors[] = "Phone number is required.";
    } else {
        $phoneDigits = preg_replace('/[\s\-\(\)\+]/', '', $phone);
        if (!preg_match('/^\d+$/', $phoneDigits)) {
            $errors[] = "Phone number contains invalid characters.";
        } elseif (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
            $errors[] = "Phone number must be between 10 and 15 digits.";
        }
    }

    if ($subject === '') {
        $errors[] = "Subject is required.";
    } elseif (strlen($subject) > 200) {
        $errors[] = "Subject is too long. Maximum 200 characters allowed.";
    }

    if ($message === '') {
        $errors[] = "Message is required.";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters long.";
    } elseif (strlen($message) > 2000) {
        $errors[] = "Message is too long. Maximum 2000 characters allowed.";
    }

    if ($booking_date !== '') {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $booking_date)) {
            $errors[] = "Please provide a valid date (YYYY-MM-DD).";
        }
    }
    if ($booking_time !== '') {
        if (!preg_match('/^\d{2}:\d{2}$/', $booking_time)) {
            $errors[] = "Please provide a valid time (HH:MM).";
        }
    }
    if ($service_type !== '') {
        if (strlen($service_type) > 100) {
            $errors[] = "Service type is too long.";
        } elseif (!preg_match('/^[a-zA-Z\s\-]+$/', $service_type)) {
            $errors[] = "Service type contains invalid characters.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO contact (name, email, phone, subject, message, booking_date, booking_time, service_type) VALUES (:name, :email, :phone, :subject, :message, :booking_date, :booking_time, :service_type)");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $bd = $booking_date !== '' ? $booking_date : null;
            $bt = $booking_time !== '' ? $booking_time . ':00' : null;
            $st = $service_type !== '' ? $service_type : null;
            $stmt->bindParam(':booking_date', $bd, PDO::PARAM_STR);
            $stmt->bindParam(':booking_time', $bt, PDO::PARAM_STR);
            $stmt->bindParam(':service_type', $st, PDO::PARAM_STR);
            $stmt->execute();

            $accessKey = getenv('78adb03d-a0e8-4916-9781-87ddc69fafa5') ?: ($_ENV['78adb03d-a0e8-4916-9781-87ddc69fafa5'] ?? '');
            if ($accessKey) {
                $payload = [
                    'access_key' => $accessKey,
                    'subject' => 'New Contact/Booking: ' . $subject,
                    'from_name' => $name,
                    'reply_to' => $email,
                    'message' => $message,
                    'phone' => $phone,
                    'booking_date' => $booking_date,
                    'booking_time' => $booking_time,
                    'service_type' => $service_type
                ];
                $ch = curl_init('https://api.web3forms.com/submit');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $resp = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($httpCode < 200 || $httpCode >= 300) {
                    $errors[] = "Failed to send notification email.";
                }
            }

            $success = empty($errors);
            if ($success) {
                $_SESSION['contact_last_time'] = $now;
                $_SESSION['contact_count'] = ($_SESSION['contact_count'] ?? 0) + 1;
                $_POST = [];
            }
        } catch (PDOException $e) {
            error_log('Contact form error: ' . $e->getMessage());
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
            <li><a href="contact.php">Contact</a></li>
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
      <section class="hero contact-hero">
        <div class="container">
          <header class="section-head fade-in">
            <h1 class="hero-title">Contact Us</h1>
            <p class="hero-subtitle">We're here to help. Get in touch with us today.</p>
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
              <h2>Send us a Message</h2>
              
              <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                  <i class="fa-solid fa-check-circle"></i> Thank you! Your message has been sent successfully. We'll get back to you soon.
                </div>
              <?php endif; ?>
              
              <?php if (!empty($errors)): ?>
                <div class="alert alert-error" role="alert">
                  <strong><i class="fa-solid fa-exclamation-circle"></i> Please fix the following errors:</strong>
                  <ul>
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <form method="POST" action="" id="contactForm" novalidate>
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
                  <label for="booking_date"><i class="fa-solid fa-calendar"></i> Preferred Date</label>
                  <input type="date" id="booking_date" name="booking_date" 
                         value="<?php echo htmlspecialchars($_POST['booking_date'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                  <label for="booking_time"><i class="fa-solid fa-clock"></i> Preferred Time</label>
                  <input type="time" id="booking_time" name="booking_time" 
                         value="<?php echo htmlspecialchars($_POST['booking_time'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                  <label for="service_type"><i class="fa-solid fa-stethoscope"></i> Service Type</label>
                  <input type="text" id="service_type" name="service_type" placeholder="e.g., Cardiology" 
                         value="<?php echo htmlspecialchars($_POST['service_type'] ?? ''); ?>" />
                </div>

                <div class="form-group">
                  <label for="message"><i class="fa-solid fa-comment"></i> Message *</label>
                  <textarea id="message" name="message" placeholder="Tell us how we can help you..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                </div>

                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>" />

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

    <div id="assetNotice" class="asset-notice" role="status" aria-live="polite">Some assets failed to load. Please refresh or check your connection.</div>
    <script src="script.js" defer onerror="document.documentElement.classList.add('script-error')"></script>
  </body>
</html>

