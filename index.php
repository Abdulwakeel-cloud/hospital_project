<?php
require_once "includes/session_config.php";
require_once "includes/home_data.php";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Medixal – Expert Medical Treatment</title>
    <meta name="description" content="Medixal – professional medical services, departments, doctors, appointments, and pricing." />
    <meta name="theme-color" content="#0b63ce" />
    
    <!-- Performance & Preloading -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com" />
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" as="style" />
    
    <!-- Stylesheets -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Initial theme applied early to avoid flash -->
    <script src="theme-init.js" onerror="document.documentElement.classList.add('script-error')"></script>

    <link rel="stylesheet" href="style.css" onerror="document.documentElement.classList.add('style-error')" />
  
    <link rel="stylesheet" href="fonts/css/all.css">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      integrity="sha512-1LVvQqHqVbXyQ6x0kCk8GkqU7k8mHhYcWn4cWZC3H2C3lQwz7H9E3cJmYF0O2xKZtq9dVx2kQpWm8y9KpFQ7wQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
  </head>
  <body>
    <header class="site-header" id="header">
      <div class="container header-inner">
        <a href="index.php#hero" class="logo">Medixal</a>
        <nav class="nav" id="nav">
          <ul class="nav-list">
            <li><a href="#hero">Home</a></li>
            <li><a href="#team">Doctors</a></li>
            <li><a href="#about">About Us</a></li>
            <li class="has-dropdown">
              <button class="nav-link dropdown-toggle" aria-haspopup="true" aria-expanded="false">Pages <i class="fa-solid fa-chevron-down"></i></button>
              <ul class="dropdown" role="menu">
                <li><a href="#services" role="menuitem">Services</a></li>
                <li><a href="#pricing" role="menuitem">Pricing</a></li>
                <li><a href="#team" role="menuitem">Team</a></li>
                <li><a href="#process" role="menuitem">Process</a></li>
                <li><a href="#appointment" role="menuitem">Appointment</a></li>
              </ul>
            </li>
            <li><a href="#blog">Blog</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </nav>
        <div class="header-cta">
          <a href="#appointment" class="btn btn-accent" id="appointmentBtn">Make An Appointment</a>
          <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" aria-pressed="false">
            <i class="fa-solid fa-moon"></i>
          </button>
          <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
            <span></span><span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <main>
      <section class="hero" id="hero">
        <div class="container hero-inner">
          <div class="hero-content fade-in">
            <h1 class="fade-in delay-1">Expert Medical Treatment</h1>
            <p class="subtitle fade-in delay-2">We Follow A Holistic Approach to Health care.</p>
            <p class="desc fade-in delay-3">We leverage advanced technology to deliver personalized, trustworthy medical care that prioritizes your well‑being.</p>
            <div class="hero-actions fade-in delay-4">
              <a href="#process" class="btn btn-primary">See How We Works</a>
            </div>
            <div class="hero-stats fade-in delay-5">
              <div class="stat-card"><strong><?php $stmt = $db->query("SELECT COUNT(*) FROM doctors");
                 echo $stmt->fetchColumn();
               ?>+</strong><span>Doctors</span></div>
              <div class="stat-card"><strong><?php $stmt = $db->query("SELECT COUNT(*) FROM patients");
                 echo $stmt->fetchColumn();
               ?>+</strong><span>Satisfied Patients</span></div>
            </div>
          </div>
          <div class="hero-visual slide-in-right">
            <img src="resources/hero_bg_1.jpeg" alt="Doctor" loading="lazy" />
            <div class="floating-stats">
              <div class="stat-card small scale-in delay-1"><strong><?php $stmt = $db->query("SELECT COUNT(*) FROM doctors");
                 echo $stmt->fetchColumn();
               ?>+</strong><span>Doctors</span></div>
              <div class="stat-card small scale-in delay-2"><strong><?php $stmt = $db->query("SELECT COUNT(*) FROM patients");
                 echo $stmt->fetchColumn();
               ?>+</strong><span>Patients</span></div>
            </div>
          </div>
        </div>
      </section>

      <section class="departments" id="departments">
        <div class="container">
          <header class="section-head fade-in">
            <h2>Our Department</h2>
            <p>Comprehensive care across specialized medical fields.</p>
          </header>
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
              <a href="#appointment" class="btn btn-primary">Book Appointment</a>
            </article>
              <?php 
              $delay++;
              if($delay > 5) $delay = 1;
              endforeach; 
              ?>
            <?php else: ?>
              <p class="fade-in">No departments available at the moment.</p>
            <?php endif; ?>
          </div>
          <div class="text-center fade-in mt-40">
            <a href="departments.php" class="btn btn-primary">See All Departments <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </section>

      <section class="about" id="about">
        <div class="container about-inner">
          <div class="about-visual slide-in-left">
            <div class="collage">
              <img src="resources/about_1.jpg.jpeg" alt="Clinic" class="img-a" loading="lazy" />
              <img src="resources/about_2.jpg.jpeg" alt="Nurse" class="img-b" loading="lazy" />
              <img src="resources/casestydy_3.jpeg" alt="Lab" class="img-c" loading="lazy" />
              <div class="badge">30+ Years Experience</div>
            </div>
          </div>
          <div class="about-content slide-in-right">
            <h2>Advanced technology and Specialist Doctors</h2>
            <p>We combine cutting‑edge equipment with experienced professionals to deliver world‑class care tailored to you.</p>
            <ul class="checks">
              <li class="fade-in delay-1"><i class="fa-solid fa-check"></i>Top quality care</li>
              <li class="fade-in delay-2"><i class="fa-solid fa-check"></i>World‑class facilities</li>
              <li class="fade-in delay-3"><i class="fa-solid fa-check"></i>Exclusive discounts</li>
            </ul>
            <a href="#appointment" class="btn btn-accent fade-in delay-4">Book An Appointment</a>
          </div>
        </div>
      </section>

      <section class="services" id="services">
        <div class="container">
          <header class="section-head fade-in">
            <h2>Our Services</h2>
            <p>From preventive care to complex procedures.</p>
          </header>
          <div class="carousel" data-carousel="services">
            <button class="carousel-btn prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="carousel-track">
              <?php if(!empty($services)) : ?>
                <?php foreach($services as $service) : ?>
                <article class="service-card">
                  <img src="admin-panel/uploads/services/<?php echo htmlspecialchars($service->image); ?>" alt="<?php echo htmlspecialchars($service->service_name); ?>" loading="lazy" />
                  <div class="content">
                    <h3><?php echo htmlspecialchars($service->service_name); ?></h3>
                    <a href="service-details.php?id=<?php echo $service->id; ?>" class="learn">Learn More<i class="fa-solid fa-arrow-right"></i></a>
                  </div>
                </article>
              <?php endforeach; ?>
              
              <?php endif; ?>
            </div>
            <button class="carousel-btn next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
          </div>
          <div class="text-center fade-in mt-40">
            <a href="services.php" class="btn btn-primary">See All Services <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </section>

      <section class="features" id="features">
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
          <div class="text-center fade-in" style="margin-top: 40px;">
            <a href="services.php#features" class="btn btn-primary">See All Features <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </section>

      <section class="testimonials" id="testimonials">
        <div class="container">
          <header class="section-head fade-in">
            <h2>What Patients Say</h2>
            <p>Trusted by thousands of patients worldwide.</p>
          </header>
          <div class="carousel" data-carousel="testimonials">
            <button class="carousel-btn prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="carousel-track">    
              <?php if (!empty($testimonials)) : ?>
                <?php foreach ($testimonials as $t) : ?>
                <article class="testimonial">
                  <?php if (!empty($t['image'])) : ?>
                    <img src="admin-panel/uploads/testimonials/<?php echo htmlspecialchars($t['image']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" loading="lazy" />
                  <?php else : ?>
                    <img src="resources/testimonial_thumbnail_1.jpeg" alt="<?php echo htmlspecialchars($t['name']); ?>" loading="lazy" />
                  <?php endif; ?>
                  <p class="quote">“<?php echo htmlspecialchars($t['message']); ?>”</p>
                  <div class="author"><?php echo htmlspecialchars($t['name']); ?> – <?php echo htmlspecialchars($t['profession']); ?></div>
                </article>
                <?php endforeach; ?>
              <?php else: ?>
                <article class="testimonial">
                  <img src="resources/testimonial_thumbnail_1.jpeg" alt="testimonial" loading="lazy" />
                  <p class="quote">“Professional and friendly staff. The care I received was exceptional.”</p>
                  <div class="author">John Smith – Framer Expert</div>
                </article>
              <?php endif; ?>
            </div>
            <div class="carousel-dots" aria-label="Testimonials pagination"></div>
            <button class="carousel-btn next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
          </div>
          <div class="text-center fade-in mt-40">
            <a href="#testimonials" class="btn btn-primary">Read More Testimonials <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </section>

      <section class="pricing" id="pricing">
        <div class="container">
          <header class="section-head fade-in">
            <h2>Pricing Plans</h2>
            <p>Flexible options to suit your needs.</p>
          </header>
          <div class="toggle">
            <span>Monthly</span>
            <label class="switch">
              <input type="checkbox" id="priceToggle" />
              <span class="slider"></span>
            </label>
            <span>Yearly</span>
          </div>
          <div class="plan-grid">
            <article class="plan scale-in delay-1" data-monthly="199" data-yearly="599">
              <h3>Premium</h3>
              <div class="plan-price"><span class="currency">$</span><span class="value">199</span><span class="period">/mo</span></div>
              <ul class="plan-features">
                <li><i class="fa-solid fa-check"></i>Priority support</li>
                <li><i class="fa-solid fa-check"></i>Unlimited visits</li>
                <li><i class="fa-solid fa-check"></i>Comprehensive tests</li>
              </ul>
              <a href="#appointment" class="btn btn-primary">Choose Plan</a>
            </article>
            <article class="plan popular scale-in delay-2" data-monthly="125" data-yearly="399">
              <div class="ribbon">Popular</div>
              <h3>Popular</h3>
              <div class="plan-price"><span class="currency">$</span><span class="value">125</span><span class="period">/mo</span></div>
              <ul class="plan-features">
                <li><i class="fa-solid fa-check"></i>Dedicated doctor</li>
                <li><i class="fa-solid fa-check"></i>10 visits / year</li>
                <li><i class="fa-solid fa-check"></i>Standard tests</li>
              </ul>
              <a href="#appointment" class="btn btn-accent">Choose Plan</a>
            </article>
            <article class="plan scale-in delay-3" data-monthly="149" data-yearly="449">
              <h3>Basic</h3>
              <div class="plan-price"><span class="currency">$</span><span class="value">149</span><span class="period">/mo</span></div>
              <ul class="plan-features">
                <li><i class="fa-solid fa-check"></i>General consultation</li>
                <li><i class="fa-solid fa-check"></i>4 visits / year</li>
                <li><i class="fa-solid fa-check"></i>Basic tests</li>
              </ul>
              <a href="#appointment" class="btn btn-primary">Choose Plan</a>
            </article>
          </div>
        </div>
      </section>

      <section class="team" id="team">
        <div class="container">
          <header class="section-head fade-in">
            <h2>Our Team</h2>
            <p>Meet the professionals behind your care.</p>
          </header>
          <div class="team-grid">
            <?php if(!empty($doctor)) : ?>
              <?php foreach($doctor as $doc) : ?>
            <article class="doctor" loading="lazy">
              <?php if(!empty($doc["image"])) : ?>
                  <img src="admin-panel/uploads/profile/<?php echo htmlspecialchars($doc["image"]); ?>" alt="<?php echo htmlspecialchars($doc["firstname"]); ?>">
                <?php else : ?>
                     <img src="asset/default.png" alt="default image">
                <?php endif; ?>

              <h3>Dr. <?php echo htmlspecialchars($doc["firstname"] . " " . $doc["lastname"]); ?></h3>
              <p><?php echo htmlspecialchars($doc["job_name"]); ?></p>
              <div class="doctor-overlay">
                <a href="<?php echo htmlspecialchars($doc["facebook"]) ;?>"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="<?php echo htmlspecialchars($doc["twitter"]) ;?>"><i class="fa-brands fa-twitter"></i></a>
                <a href="<?php echo htmlspecialchars($doc["linkedin"]) ;?>"><i class="fa-brands fa-linkedin-in"></i></a>
              </div>
            </article>  <?php endforeach; ?>             <?php else: ?>
              <p>No Doctors found.</p>
            <?php endif; ?>
          </div>
          <div class="text-center fade-in" style="margin-top: 40px;">
            <a href="team.php" class="btn btn-primary">See All Doctors <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </section>

      <section class="blog" id="blog">
        <div class="container">
          <header class="section-head fade-in">
            <h2>Latest Articles</h2>
            <p>Insights and news from our medical experts.</p>
          </header>
          <div class="blog-grid">
           <?php if(!empty($posts)) : ?>
              <?php foreach($posts as $post) : ?>
            <article class="post">
              
              <img src="admin-panel/uploads/blog/<?php echo htmlspecialchars($post->image) ; ?>" alt="Post" loading="lazy" />
              <div class="post-content">
                <h3><?php echo htmlspecialchars($post->title) ; ?></h3>
                <p class="meta">Created at <?php echo htmlspecialchars(date('F j, Y', strtotime($post->created_at))); ?></p>
                <a href="blog-details.php?id=<?php echo $post->id; ?>" class="read">Read More <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </article> <?php endforeach; ?>             <?php else: ?>
              <p>No post found.</p>
            <?php endif; ?>
          </div>
          <div class="text-center fade-in mt-40">
            <a href="blog.php" class="btn btn-primary">See All Articles <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      </section>

      <section class="process" id="process">
        <div class="container">
          <header class="section-head fade-in">
            <h2>Work Process</h2>
            <p>Your journey to better health.</p>
          </header>
          <div class="process-flow">
            <div class="step"><i class="fa-solid fa-calendar-check"></i><span>Booking</span></div>
            <div class="arrow"><i class="fa-solid fa-angles-right"></i></div>
            <div class="step"><i class="fa-solid fa-hospital-user"></i><span>Visit</span></div>
            <div class="arrow"><i class="fa-solid fa-angles-right"></i></div>
            <div class="step"><i class="fa-solid fa-user-doctor"></i><span>Meet Doctors</span></div>
            <div class="arrow"><i class="fa-solid fa-angles-right"></i></div>
            <div class="step"><i class="fa-solid fa-clipboard-check"></i><span>Follow Up</span></div>
            <div class="arrow"><i class="fa-solid fa-angles-right"></i></div>
            <div class="step"><i class="fa-solid fa-file-invoice-dollar"></i><span>Bills</span></div>
          </div>
        </div>
      </section>

      <section class="appointment" id="appointment">
        <div class="container">
          <header class="section-head fade-in">
            <h2>Book An Appointment</h2>
            <p>Fill out the form and we'll get back to you.</p>
          </header>
          <?php
          // Flash messages for appointment form
          if (!empty($_SESSION['appointment_success'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1rem;">
              <?php echo htmlspecialchars($_SESSION['appointment_success']); ?>
            </div>
          <?php
            unset($_SESSION['appointment_success']);
          endif;

          if (!empty($_SESSION['appointment_errors'])): ?>
            <div class="alert alert-danger" style="margin-bottom: 1rem;">
              <ul style="margin: 0; padding-left: 1.2rem;">
                <?php foreach ($_SESSION['appointment_errors'] as $err): ?>
                  <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php
            unset($_SESSION['appointment_errors']);
          endif;

          $old = $_SESSION['appointment_old'] ?? [];
          unset($_SESSION['appointment_old']);
          ?>
          <form class="form" id="appointmentForm"  action="includes/appointment.php" method="POST">
            <div class="form-row">
              <div class="form-field">
                <label for="department">Department</label>
                <select id="department" name="department_id" required>
                  <option value="">Select Department</option>
                  <?php if(!empty($categories)) : ?>
                 
                    <?php foreach($categories as $category) : ?>
                      <option value="<?php echo htmlspecialchars($category->id); ?>" <?php echo (!empty($old['department_id']) && (int)$old['department_id'] === (int)$category->id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category->category_name); ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
              <div class="form-field">
                <label for="doctor">Doctor</label>
                <select id="doctor" name="doctor_id" required>
                  <option value="">Select Doctor</option>
                  <?php if(!empty($doctor)) : ?>
                    <?php foreach($doctor as $doc) : ?>
                      <option value="<?php echo htmlspecialchars($doc["id"]); ?>" <?php echo (!empty($old['doctor_id']) && (int)$old['doctor_id'] === (int)$doc['id']) ? 'selected' : ''; ?>>
                        Dr. <?php echo htmlspecialchars($doc["firstname"] . " " . $doc["lastname"]); ?> - <?php echo htmlspecialchars($doc["job_name"] ?? "General"); ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Your full name" required value="<?php echo isset($old['name']) ? htmlspecialchars($old['name']) : ''; ?>" />
              </div>
              <div class="form-field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="000‑000‑0000" required value="<?php echo isset($old['phone']) ? htmlspecialchars($old['phone']) : ''; ?>" />
              </div>
            </div>
            <div class="form-row">
              <div class="form-field">
                <label for="date">Date</label>
                <input type="date" id="date" name="appointment_date" required value="<?php echo isset($old['appointment_date']) ? htmlspecialchars($old['appointment_date']) : ''; ?>" />
              </div>
              <div class="form-field">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-accent btn-block">Book An Appointment</button>
              </div>
            </div>
            <p class="form-note" id="formNote" role="status" aria-live="polite"></p>
          </form>
        </div>
      </section>
    </main>

    <footer class="site-footer" id="footer">
      <div class="container footer-grid">
        <div class="footer-col">
          <a href="index.php#hero" class="logo">Medixal</a>
          <p>Trusted medical services delivering personalized care with advanced technology.</p>
        </div>
        <div class="footer-col">
          <h4>Links</h4>
          <ul>
            <li><a href="#hero">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Resources</h4>
          <ul>
            <li><a href="#services">Services</a></li>
            <li><a href="#pricing">Pricing</a></li>
            <li><a href="#team">Team</a></li>
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