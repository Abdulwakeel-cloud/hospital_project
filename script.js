document.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('nav');
  const hamburger = document.getElementById('hamburger');
  const appointmentBtn = document.getElementById('appointmentBtn');
  const header = document.getElementById('header');
  const yearEl = document.getElementById('year');
  const priceToggle = document.getElementById('priceToggle');
  const form = document.getElementById('appointmentForm');
  const formNote = document.getElementById('formNote');
  const dropdownToggle = document.querySelector('.dropdown-toggle');
  const themeToggle = document.getElementById('themeToggle');
  const htmlEl = document.documentElement;

  // Scroll Progress Indicator
  const scrollProgress = document.createElement('div');
  scrollProgress.className = 'scroll-progress';
  document.body.appendChild(scrollProgress);

  const updateScrollProgress = () => {
    const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (window.scrollY / windowHeight) * 100;
    scrollProgress.style.width = `${scrolled}%`;
  };
  window.addEventListener('scroll', updateScrollProgress, { passive: true });

  // Back to Top Button
  const backToTop = document.createElement('button');
  backToTop.className = 'back-to-top';
  backToTop.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
  backToTop.setAttribute('aria-label', 'Back to top');
  document.body.appendChild(backToTop);

  const toggleBackToTop = () => {
    if (window.scrollY > 300) {
      backToTop.classList.add('visible');
    } else {
      backToTop.classList.remove('visible');
    }
  };
  window.addEventListener('scroll', toggleBackToTop, { passive: true });
  backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Set year
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  // Scroll Animations using Intersection Observer
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        // Stop observing once animated
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  // Observe all elements with animation classes
  document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-in').forEach(el => {
    observer.observe(el);
  });

  // Animated Counters
  const animateCounter = (element, target, duration = 2000) => {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;

    const updateCounter = () => {
      current += increment;
      if (current < target) {
        const displayValue = Math.floor(current);
        element.textContent = displayValue.toLocaleString() + (element.textContent.includes('+') ? '+' : '') + 
                             (element.textContent.includes('K') ? 'K' : '');
        requestAnimationFrame(updateCounter);
      } else {
        element.textContent = target.toLocaleString() + (element.textContent.includes('+') ? '+' : '') + 
                             (element.textContent.includes('K') ? 'K' : '');
      }
    };

    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !element.dataset.animated) {
          element.dataset.animated = 'true';
          updateCounter();
          counterObserver.unobserve(element);
        }
      });
    }, { threshold: 0.5 });

    counterObserver.observe(element);
  };

  // Initialize counters
  document.querySelectorAll('.stat-card strong').forEach(stat => {
    const text = stat.textContent;
    const number = parseInt(text.replace(/[^0-9]/g, ''));
    if (!isNaN(number)) {
      stat.textContent = '0' + (text.includes('+') ? '+' : '') + (text.includes('K') ? 'K' : '');
      animateCounter(stat, number, 2000);
    }
  });

  // Hamburger menu
  if (hamburger) {
    hamburger.addEventListener('click', () => {
      const open = nav.classList.toggle('nav-open');
      hamburger.classList.toggle('active', open);
      hamburger.setAttribute('aria-expanded', String(open));
    });
  }

  // Close mobile menu on navigation
  nav.querySelectorAll('a').forEach((a) => {
    a.addEventListener('click', () => {
      nav.classList.remove('nav-open');
      hamburger?.classList.remove('active');
      hamburger?.setAttribute('aria-expanded', 'false');
    });
  });

  // Header shadow on scroll - throttled for performance
  let scrollTimeout;
  const onScroll = () => {
    if (scrollTimeout) return;
    scrollTimeout = setTimeout(() => {
      if (window.scrollY > 8) header.classList.add('header-shadow');
      else header.classList.remove('header-shadow');
      scrollTimeout = null;
    }, 16); // ~60fps
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  // Dropdown (Pages)
  dropdownToggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    const parent = dropdownToggle.closest('.has-dropdown');
    if (!parent) return;
    const isOpen = parent.classList.contains('open');
    
    // Close all other dropdowns first
    document.querySelectorAll('.has-dropdown').forEach(dd => {
      dd.classList.remove('open');
      const toggle = dd.querySelector('.dropdown-toggle');
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
    });
    
    // Toggle current dropdown
    if (!isOpen) {
      parent.classList.add('open');
      dropdownToggle.setAttribute('aria-expanded', 'true');
    } else {
      parent.classList.remove('open');
      dropdownToggle.setAttribute('aria-expanded', 'false');
    }
  });
  
  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    const dd = document.querySelector('.has-dropdown');
    if (!dd) return;
    if (!dd.contains(e.target)) {
      dd.classList.remove('open');
      dropdownToggle?.setAttribute('aria-expanded', 'false');
    }
  });
  
  // Close dropdown when clicking on a dropdown link
  document.querySelectorAll('.has-dropdown .dropdown a').forEach(link => {
    link.addEventListener('click', () => {
      const dd = link.closest('.has-dropdown');
      if (dd) {
        dd.classList.remove('open');
        dropdownToggle?.setAttribute('aria-expanded', 'false');
      }
    });
  });

  appointmentBtn?.addEventListener('click', (e) => {
    const target = document.getElementById('appointment');
    if (!target) return;
    setTimeout(() => form?.querySelector('input,select,button')?.focus(), 400);
  });

  // Carousel functionality with passive listeners
  document.querySelectorAll('.carousel').forEach((carousel) => {
    const track = carousel.querySelector('.carousel-track');
    const prev = carousel.querySelector('.prev');
    const next = carousel.querySelector('.next');
    const scrollAmount = 300;
    prev?.addEventListener('click', () => {
      track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });
    next?.addEventListener('click', () => {
      track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });

    // Dots for testimonials
    if (carousel.dataset.carousel === 'testimonials') {
      const items = Array.from(track.children);
      const dotsContainer = carousel.querySelector('.carousel-dots');
      const dots = items.map((_, idx) => {
        const b = document.createElement('button');
        b.setAttribute('aria-label', `Go to slide ${idx + 1}`);
        dotsContainer.appendChild(b);
        b.addEventListener('click', () => {
          const left = items[idx].offsetLeft;
          track.scrollTo({ left, behavior: 'smooth' });
          updateDots(idx);
        });
        return b;
      });
      const updateDots = (activeIndex) => {
        dots.forEach((d, i) => d.classList.toggle('active', i === activeIndex));
      };
      const getIndex = () => {
        const scrollLeft = track.scrollLeft;
        const w = items[0].offsetWidth + 16; // item width + gap
        return Math.round(scrollLeft / w);
      };
      track.addEventListener('scroll', () => updateDots(getIndex()), { passive: true });
      updateDots(0);

      // Autoplay - only start after user interaction
      let i = 0;
      let autoplayInterval = setInterval(() => {
        i = (i + 1) % items.length;
        const left = items[i].offsetLeft;
        track.scrollTo({ left, behavior: 'smooth' });
        updateDots(i);
      }, 5000);

      // Pause autoplay on user interaction
      track.addEventListener('scroll', () => {
        clearInterval(autoplayInterval);
      }, { once: true });
    }
  });

  // Pricing toggle
  const updatePrices = () => {
    const yearly = priceToggle.checked;
    document.querySelectorAll('.plan').forEach((plan) => {
      const valueEl = plan.querySelector('.plan-price .value');
      const price = plan.getAttribute(yearly ? 'data-yearly' : 'data-monthly');
      if (valueEl && price) valueEl.textContent = price;
      const periodEl = plan.querySelector('.plan-price .period');
      if (periodEl) periodEl.textContent = yearly ? '/yr' : '/mo';
    });
  };
  priceToggle?.addEventListener('change', updatePrices);
  updatePrices();

  // Enhanced Form Validation with Animations
  const validateField = (field) => {
    const value = field.value.trim();
    field.classList.remove('error', 'success');
    
    if (!value) {
      field.classList.add('error');
      field.style.animation = 'shake 0.5s';
      return false;
    }
    
    // Email validation for email fields
    if (field.type === 'email' && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        field.classList.add('error');
        field.style.animation = 'shake 0.5s';
        return false;
      }
    }
    
    // Phone validation
    if (field.type === 'tel' && value) {
      const phoneRegex = /^[\d\s\-\(\)]+$/;
      if (!phoneRegex.test(value) || value.replace(/\D/g, '').length < 10) {
        field.classList.add('error');
        field.style.animation = 'shake 0.5s';
        return false;
      }
    }
    
    field.classList.add('success');
    return true;
  };

  // Real-time validation
  form?.querySelectorAll('input, select').forEach(field => {
    field.addEventListener('blur', () => validateField(field));
    field.addEventListener('input', () => {
      if (field.classList.contains('error')) {
        validateField(field);
      }
    });
  });

  // Form submission with celebration
  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    formNote.textContent = '';
    formNote.className = 'form-note';
    
    const dept = document.getElementById('department');
    const doctor = document.getElementById('doctor');
    const name = document.getElementById('name');
    const phone = document.getElementById('phone');
    const date = document.getElementById('date');
    const fields = [dept, doctor, name, phone, date];
    
    let valid = true;
    fields.forEach((f) => {
      if (!validateField(f)) {
        valid = false;
        if (!f.value) f.focus();
      }
    });
    
    if (!valid) {
      formNote.textContent = 'Please fill in all required fields correctly.';
      formNote.classList.add('error');
      form.style.animation = 'shake 0.5s';
      return;
    }
    
    // Success animation
    formNote.textContent = '✓ Appointment request submitted. We\'ll contact you soon!';
    formNote.classList.add('success');
    
    // Confetti effect
    createConfetti();
    
    // Reset form after delay
    setTimeout(() => {
      form.reset();
      form.querySelectorAll('.success').forEach(f => f.classList.remove('success'));
      updatePrices();
      formNote.textContent = '';
      formNote.classList.remove('success');
    }, 3000);
  });

  // Confetti Effect Function
  function createConfetti() {
    const colors = ['#0b63ce', '#0d0588', '#3eb5b3', '#ffc107', '#dc3545'];
    const confettiCount = 50;
    
    for (let i = 0; i < confettiCount; i++) {
      setTimeout(() => {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.width = Math.random() * 10 + 5 + 'px';
        confetti.style.height = confetti.style.width;
        confetti.style.animationDelay = Math.random() * 0.5 + 's';
        document.body.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 3000);
      }, i * 20);
    }
  }

  // Theme toggle (dark/light) with localStorage
  const applyTheme = (t) => {
    htmlEl.setAttribute('data-theme', t);
    const icon = themeToggle?.querySelector('i');
    if (!icon) return;
    // Update icon: sun for dark mode, moon for light mode
    icon.className = t === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    // Update aria-label for accessibility
    themeToggle?.setAttribute('aria-label', t === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
  };
  
  // Get saved theme or use system preference
  const getInitialTheme = () => {
    const saved = localStorage.getItem('theme');
    if (saved) return saved;
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      return 'dark';
    }
    return 'light';
  };
  
  const savedTheme = getInitialTheme();
  applyTheme(savedTheme);
  
  // Theme toggle click handler
  themeToggle?.addEventListener('click', () => {
    const currentTheme = htmlEl.getAttribute('data-theme') || 'light';
    const next = currentTheme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('theme', next);
    applyTheme(next);
  });

  // Active nav link on scroll - with Intersection Observer for better performance
  const sections = ['hero','about','departments','services','features','testimonials','pricing','team','process','appointment','blog','footer']
    .map((id) => document.getElementById(id))
    .filter(Boolean);
  const navLinks = Array.from(document.querySelectorAll('.nav-list a'));

  if ('IntersectionObserver' in window) {
    const sectionObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          navLinks.forEach((a) => {
            a.classList.toggle('active', a.getAttribute('href') === `#${entry.target.id}`);
          });
        }
      });
    }, { threshold: 0.3 });

    sections.forEach((section) => sectionObserver.observe(section));
  } else {
    // Fallback for older browsers
    const setActive = () => {
      let currentId = sections[0].id;
      sections.forEach((sec) => {
        const rect = sec.getBoundingClientRect();
        if (rect.top <= 120 && rect.bottom >= 140) currentId = sec.id;
      });
      navLinks.forEach((a) => a.classList.toggle('active', a.getAttribute('href') === `#${currentId}`));
    };
    setActive();
    window.addEventListener('scroll', setActive, { passive: true });
  }
});