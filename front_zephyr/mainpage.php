<?php
include "linc.php";
// to count and update the total number of views
$qv="update view set v=v+1 where id=1;";
$qr=mysqli_query($mysqli,$qv);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />

    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <title>ZEPHYR!</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/zephyr-logo.JPG">   <!-- icon shown on top of heading of admin page -->
    <link
      href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"
      rel="stylesheet"
    />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="css/fontawesome-all.min.css" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="css/swiper.min.css" />

    <!-- Styles -->
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="modern-3d.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  </head>
  <script src="js/jssor.slider-28.0.0.min.js" type="text/javascript"></script>
  

  <body class="transform-gpu">
    <!-- Modern 3D Navigation -->
    <nav class="navbar-3d">
      <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center w-100">
          <div class="navbar-brand">
            <h2 class="text-gradient mb-0 floating-element">
              <i class="fas fa-rocket mr-2"></i>ZEPHYR
            </h2>
            <small class="text-secondary d-block">Experience the Future</small>
          </div>
          
          <div class="d-none d-lg-flex align-items-center">
            <a href="mainpage.php" class="nav-link-3d mx-2 active">
              <i class="fas fa-home mr-2"></i>Home
            </a>
            <a href="events.php" class="nav-link-3d mx-2">
              <i class="fas fa-calendar mr-2"></i>Events
            </a>
            <a href="plogin.php" class="nav-link-3d mx-2">
              <i class="fas fa-user mr-2"></i>Portal
            </a>
            <a href="#sponsors" class="nav-link-3d mx-2">
              <i class="fas fa-handshake mr-2"></i>Sponsors
            </a>
            <a href="admin_auth.php" class="nav-link-3d mx-2">
              <i class="fas fa-cog mr-2"></i>Admin
            </a>
            <a href="participantformnew.php" class="btn-modern ml-3">
              <span><i class="fas fa-rocket mr-2"></i>Join Now</span>
            </a>
          </div>
          
          <!-- Mobile Menu Button -->
          <button class="btn btn-secondary d-lg-none" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
          </button>
          
          <!-- Mobile Menu -->
          <div class="d-lg-none mt-3" id="mobileMenu" style="display: none;">
            <div class="card-3d">
              <div class="d-flex flex-column">
                <a href="mainpage.php" class="nav-link-3d mb-2 active">
                  <i class="fas fa-home mr-2"></i>Home
                </a>
                <a href="events.php" class="nav-link-3d mb-2">
                  <i class="fas fa-calendar mr-2"></i>Events
                </a>
                <a href="plogin.php" class="nav-link-3d mb-2">
                  <i class="fas fa-user mr-2"></i>Portal
                </a>
                <a href="#sponsors" class="nav-link-3d mb-2">
                  <i class="fas fa-handshake mr-2"></i>Sponsors
                </a>
                <a href="admin_auth.php" class="nav-link-3d mb-2">
                  <i class="fas fa-cog mr-2"></i>Admin
                </a>
                <a href="participantformnew.php" class="btn-modern">
                  <span><i class="fas fa-rocket mr-2"></i>Join Now</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>
    <div id="particles-js"></div>
    <!-- adding particles to make it look better -->
    <!-- particles.js lib - https://github.com/VincentGarreau/particles.js -->
    <script src="http://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    <!-- Modern 3D Hero Section -->
    <section class="hero-3d">
      <!-- Animated Particles Background -->
      <div class="particles-container">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 1s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 2.5s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 4.5s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 1.5s;"></div>
      </div>
      
      <div class="container">
        <div class="hero-content-3d">
          <div class="hero-title-3d floating-element">
            ZEPHYR
          </div>
          <div class="hero-subtitle-3d">
            Experience the Future of Festival Technology
          </div>
          
          <!-- Modern Countdown -->
          <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
              <div class="card-3d text-center">
                <h3 class="text-gradient mb-4">Event Countdown</h3>
                <div class="row">
                  <div class="col-3">
                    <div class="card-3d bg-transparent">
                      <div class="display-4 text-gradient" id="dday">--</div>
                      <small class="text-secondary">Days</small>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="card-3d bg-transparent">
                      <div class="display-4 text-gradient" id="dhour">--</div>
                      <small class="text-secondary">Hours</small>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="card-3d bg-transparent">
                      <div class="display-4 text-gradient" id="dmin">--</div>
                      <small class="text-secondary">Minutes</small>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="card-3d bg-transparent">
                      <div class="display-4 text-gradient" id="dsec">--</div>
                      <small class="text-secondary">Seconds</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="participantformnew.php" class="btn-modern glow-effect">
              <span><i class="fas fa-rocket mr-2"></i>Join the Revolution</span>
            </a>
            <a href="events.php" class="btn-modern btn-secondary">
              <span><i class="fas fa-calendar mr-2"></i>Explore Events</span>
            </a>
          </div>
        </div>
      </div>
    </section>    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="lineup-artists-headline">
            <div class="entry-title">
              <p>JUST THE BEST</p>
              <h2>The Lineup Artists-Headliners</h2>
            </div>
            <!-- entry-title -->

            <div class="lineup-artists">
              <div class="lineup-artists-wrap flex flex-wrap">
                <figure class="featured-image">
                  <a href="#">
                    <img src="images/black-chick.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <div class="lineup-artists-description">
                  <div class="lineup-artists-description-container">
                    <div class="entry-title">
                      Jamila Williams
                    </div>
                    <!-- entry-title -->

                    <div class="entry-content">
                      <p>
                        Quisque at erat eu libero consequat tempus. Quisque mole
                        stie convallis tempus. Ut semper purus metus, a euismod
                        sapien sodales ac. Duis viverra eleifend fermentum.
                      </p>
                    </div>
                    <!-- entry-content -->

                    <div class="box-link">
                      <a href=""><img src="images/box.jpg" alt="" /></a>
                    </div>
                    <!-- box-link -->
                  </div>
                  <!-- lineup-artists-description-container -->
                </div>
                <!-- lineup-artists-description -->
              </div>
              <!-- lineup-artists-wrap -->

              <div class="lineup-artists-wrap flex flex-wrap">
                <div class="lineup-artists-description">
                  <figure class="featured-image d-md-none">
                    <a href="#">
                      <img src="images/mathew-kane.jpg" alt="" />
                    </a>
                  </figure>
                  <!-- featured-image -->

                  <div class="lineup-artists-description-container">
                    <div class="entry-title">
                      Sandra Superstar
                    </div>
                    <!-- entry-title -->

                    <div class="entry-content">
                      <p>
                        Quisque at erat eu libero consequat tempus. Quisque mole
                        stie convallis tempus. Ut semper purus metus, a euismod
                        sapien sodales ac. Duis viverra eleifend fermentum.
                      </p>
                    </div>
                    <!-- entry-content -->

                    <div class="box-link">
                      <a href="#"><img src="images/box.jpg" alt="" /></a>
                    </div>
                    <!-- box-link -->
                  </div>
                  <!-- lineup-artists-description-container -->
                </div>
                <!-- lineup-artists-description -->

                <figure class="featured-image d-none d-md-block">
                  <a href="#">
                    <img src="images/mathew-kane.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->
              </div>
              <!-- lineup-artists-wrap -->

              <div class="lineup-artists-wrap flex flex-wrap">
                <figure class="featured-image">
                  <a href="#"> <img src="images/eric-ward.jpg" alt="" /> </a>
                </figure>
                <!-- featured-image -->

                <div class="lineup-artists-description">
                  <div class="lineup-artists-description-container">
                    <div class="entry-title">
                      DJ Crazyhead
                    </div>
                    <!-- entry-title -->

                    <div class="entry-content">
                      <p>
                        Quisque at erat eu libero consequat tempus. Quisque mole
                        stie convallis tempus. Ut semper purus metus, a euismod
                        sapien sodales ac. Duis viverra eleifend fermentum.
                      </p>
                    </div>
                    <!-- entry-content -->

                    <div class="box-link">
                      <a href="#"> <img src="images/box.jpg" alt="" /></a>
                    </div>
                    <!-- box-link -->
                  </div>
                  <!-- lineup-artists-description-container -->
                </div>
                <!-- lineup-artists-description -->
              </div>
              <!-- lineup-artists-wrap -->
            </div>
            <!-- lineup-artists -->
          </div>
          <!-- lineup-artists-headline -->
        </div>
        <!-- col-12 -->
      </div>
      <!-- row -->

      <div class="row">
        <div class="col-12">
          <div class="the-complete-lineup">
            <div class="entry-title">
              <p>JUST THE BEST</p>
              <h2>The Complete Lineup</h2>
            </div>
            <!-- entry-title -->

            <div class="row the-complete-lineup-artists">
              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-1.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>Miska Smith</h2>
              </div>
              <!-- artist-single -->

              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-2.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>Hayley Down</h2>
              </div>
              <!-- artist-single -->

              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-3.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>The Band Song</h2>
              </div>
              <!-- artist-single -->

              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-4.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>Pink Machine</h2>
              </div>
              <!-- artist-single -->

              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-5.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>Brasil Band</h2>
              </div>
              <!-- artist-single -->

              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-6.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>Mickey</h2>
              </div>
              <!-- artist-single -->

              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-7.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>DJ Girl</h2>
              </div>
              <!-- artist-single -->

              <div class="col-6 col-md-4 col-lg-3 artist-single">
                <figure class="featured-image">
                  <a href="#"> <img src="images/image-8.jpg" alt="" /> </a>
                  <a href="#" class="box-link">
                    <img src="images/box.jpg" alt="" />
                  </a>
                </figure>
                <!-- featured-image -->

                <h2>Stan Smith</h2>
              </div>
              <!-- artist-single -->
            </div>
            <!-- the-complete-lineup-artists -->
          </div>
          <!-- the-complete-lineup -->
        </div>
        <!-- col-12 -->
      </div>
      <!-- row -->
<!-- Modern 3D Sponsors Section -->
    <section id="sponsors" class="py-5" style="background: var(--gradient-dark); position: relative; overflow: hidden;">
      <!-- Background Effects -->
      <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                  background: radial-gradient(circle at 30% 40%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                             radial-gradient(circle at 70% 60%, rgba(6, 182, 212, 0.1) 0%, transparent 50%); 
                  z-index: 1;"></div>
      
      <div class="container" style="position: relative; z-index: 2;">
        <!-- Section Header -->
        <div class="text-center mb-5">
          <p class="text-secondary text-uppercase letter-spacing mb-2 floating-element" style="animation-delay: 0.1s;">
            <i class="fas fa-crown mr-2"></i>Diamond Partners
          </p>
          <h2 class="display-4 text-gradient mb-4 floating-element" style="animation-delay: 0.2s;">
            COSMIC SPONSORS
          </h2>
          <p class="lead text-secondary mb-0 floating-element" style="animation-delay: 0.3s;">
            Powering the future with visionary partnerships
          </p>
        </div>

        <!-- Diamond Sponsors -->
        <div class="row justify-content-center mb-5">
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card-3d text-center floating-element sponsor-card-3d" style="animation-delay: 0.4s;">
              <div class="sponsor-logo-container mb-4">
                <img src="images/google.jpg" alt="Google" class="sponsor-logo-3d">
              </div>
              <h4 class="text-gradient mb-3">Google</h4>
              <p class="text-secondary mb-3">
                Empowering innovation and driving technological excellence in the digital frontier.
              </p>
              <div class="sponsor-tier-badge diamond-tier">
                <i class="fas fa-gem mr-2"></i>Diamond Partner
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card-3d text-center floating-element sponsor-card-3d" style="animation-delay: 0.5s;">
              <div class="sponsor-logo-container mb-4">
                <img src="images/facebook.png" alt="Facebook" class="sponsor-logo-3d">
              </div>
              <h4 class="text-gradient mb-3">Meta</h4>
              <p class="text-secondary mb-3">
                Connecting communities and building the metaverse of tomorrow's experiences.
              </p>
              <div class="sponsor-tier-badge diamond-tier">
                <i class="fas fa-gem mr-2"></i>Diamond Partner
              </div>
            </div>
          </div>
          
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card-3d text-center floating-element sponsor-card-3d" style="animation-delay: 0.6s;">
              <div class="sponsor-logo-container mb-4">
                <img src="images/tesla.png" alt="Tesla" class="sponsor-logo-3d">
              </div>
              <h4 class="text-gradient mb-3">Tesla</h4>
              <p class="text-secondary mb-3">
                Accelerating sustainable transport and energy solutions for a better world.
              </p>
              <div class="sponsor-tier-badge diamond-tier">
                <i class="fas fa-gem mr-2"></i>Diamond Partner
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
      <div class="row">
        <div class="col-12">
          <div class="the-complete-lineup">
            <div class="entry-title" style="margin-bottom: 40px; text-align: center;">
              <p>Platinum</p>
              <h2>SPONSORS</h2>
            </div>
            <div class="container">
              <div class="row">
                <div class="col-sm-12 col-md-4">
                  <div class="card">
                    <img
                     class="card-img-top"
                     src="images/microsoft.png"
                     alt="Card image cap"
                    />
                    <div class="card-body">
                      <p class="card-text">
                        Some detailed text of sponsor.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 col-md-4">
                  <div class="card">
                    <img
                    class="card-img-top"
                     src="images/iphone.png"
                     alt="Card image cap"
                    />
                    <div class="card-body">
                      <p class="card-text">
                        Some detailed text of sponsor.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 col-md-4">
                  <div class="card">
                    <img
                    class="card-img-top"
                     src="images/adobe.png"
                     alt="Card image cap"
                    />
                    <div class="card-body">
                      <p class="card-text">
                        Some detailed text of sponsor.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- entry-title -->
          </div>
          <!-- the-complete-lineup -->
        </div>
        <!-- col-12 -->
      </div>
      <div class="row">
        <div class="col-12">
          <div class="the-complete-lineup">
            <div class="entry-title" style="margin-bottom: 40px; text-align: center;">
              <p>Gold</p>
              <h2>SPONSORS</h2>
            </div>
            <div class="container">
              <div class="row">
                <div class="col-sm-12 col-md-4">
                  <div class="card">
                    <img
                    class="card-img-top"
                     src="images/tcs.jpg"
                     alt="Card image cap"
                    />
                    <div class="card-body">
                      <p class="card-text">
                        Some detailed text of sponsor.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 col-md-4">
                  <div class="card">
                    <img
                    class="card-img-top"
                     src="images/wipro.jpg"
                     alt="Card image cap"
                    />
                    <div class="card-body">
                      <p class="card-text">
                        Some detailed text of sponsor.
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 col-md-4">
                  <div class="card">
                    <img
                    class="card-img-top"
                     src="images/samsung.jpg"
                     alt="Card image cap"
                    />
                    <div class="card-body">
                      <p class="card-text">
                        Some detailed text of sponsor.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- entry-title -->
          </div>
          <!-- the-complete-lineup -->
        </div>
        <!-- col-12 -->
      </div></div>
    </div>
    <!-- container -->
    <!-- sponsors end -->

    <!-- Modern 3D Footer -->
    <footer class="footer-3d">
      <!-- Footer Hero Section -->
      <div class="footer-hero-3d">
        <div class="container">
          <h1 class="footer-brand-3d floating-element">ZEPHYR</h1>
          <p class="lead text-secondary mb-0">
            Where innovation meets imagination in the cosmic dance of creativity
          </p>
        </div>
      </div>

      <!-- Footer Content -->
      <div class="footer-content-3d">
        <div class="container">
          <div class="row">
            <!-- About Section -->
            <div class="col-lg-4 col-md-6 footer-section-3d">
              <h3 class="footer-title-3d">
                <i class="fas fa-rocket mr-2"></i>About Zephyr
              </h3>
              <p class="text-secondary mb-4">
                Experience the future of festival celebrations where technology, 
                art, and creativity converge to create extraordinary moments that 
                transcend the ordinary.
              </p>
              
              <div class="footer-contact-3d">
                <div class="contact-item-3d">
                  <div class="contact-icon-3d">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <div class="contact-text-3d">
                    <span class="contact-label-3d">Email Us</span>
                    <span class="contact-value-3d">hello@zephyr.space</span>
                  </div>
                </div>
                
                <div class="contact-item-3d">
                  <div class="contact-icon-3d">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <div class="contact-text-3d">
                    <span class="contact-label-3d">Location</span>
                    <span class="contact-value-3d">JNU School of Engineering</span>
                  </div>
                </div>
                
                <div class="contact-item-3d">
                  <div class="contact-icon-3d">
                    <i class="fas fa-phone"></i>
                  </div>
                  <div class="contact-text-3d">
                    <span class="contact-label-3d">Mission Control</span>
                    <span class="contact-value-3d">+91 XXX XXX XXXX</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 footer-section-3d">
              <h3 class="footer-title-3d">
                <i class="fas fa-link mr-2"></i>Quick Links
              </h3>
              <a href="mainpage.php" class="footer-link-3d">
                <i class="fas fa-home mr-2"></i>Home Base
              </a>
              <a href="events.php" class="footer-link-3d">
                <i class="fas fa-calendar-alt mr-2"></i>Events
              </a>
              <a href="participantformnew.php" class="footer-link-3d">
                <i class="fas fa-user-plus mr-2"></i>Join Mission
              </a>
              <a href="admin_auth.php" class="footer-link-3d">
                <i class="fas fa-cog mr-2"></i>Command Center
              </a>
            </div>

            <!-- Festival Info -->
            <div class="col-lg-3 col-md-6 footer-section-3d">
              <h3 class="footer-title-3d">
                <i class="fas fa-star mr-2"></i>Festival Hub
              </h3>
              <a href="#" class="footer-link-3d">
                <i class="fas fa-trophy mr-2"></i>Competitions
              </a>
              <a href="#" class="footer-link-3d">
                <i class="fas fa-music mr-2"></i>Performances
              </a>
              <a href="#" class="footer-link-3d">
                <i class="fas fa-palette mr-2"></i>Art Gallery
              </a>
              <a href="#" class="footer-link-3d">
                <i class="fas fa-gamepad mr-2"></i>Gaming Zone
              </a>
              <a href="#" class="footer-link-3d">
                <i class="fas fa-flask mr-2"></i>Tech Expo
              </a>
            </div>

            <!-- Social & Newsletter -->
            <div class="col-lg-3 col-md-6 footer-section-3d">
              <h3 class="footer-title-3d">
                <i class="fas fa-globe mr-2"></i>Connect
              </h3>
              <p class="text-secondary mb-4">
                Join our cosmic community and stay updated with the latest from the Zephyr universe.
              </p>
              
              <div class="social-icons-3d mb-4">
                <a href="#" class="social-icon-3d">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-icon-3d">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-icon-3d">
                  <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-icon-3d">
                  <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" class="social-icon-3d">
                  <i class="fab fa-youtube"></i>
                </a>
                <a href="#" class="social-icon-3d">
                  <i class="fab fa-discord"></i>
                </a>
              </div>
              
              <div class="newsletter-3d">
                <form class="d-flex">
                  <input type="email" class="form-input-3d flex-fill" 
                         placeholder="Enter your email for updates" 
                         style="border-radius: 15px 0 0 15px; margin-bottom: 0;">
                  <button type="submit" class="btn-modern" 
                          style="border-radius: 0 15px 15px 0; padding: 0 20px;">
                    <i class="fas fa-rocket"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Copyright Section -->
      <div class="footer-copyright-3d">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-6">
              <p class="copyright-text-3d">
                <script>document.write(new Date().getFullYear());</script>
                © ZEPHYR Festival. All rights reserved.
              </p>
            </div>
            <div class="col-md-6 text-md-right">
              <p class="copyright-text-3d">
                Crafted with <i class="fas fa-heart copyright-heart"></i> by 
                <span class="text-gradient">Ritika & Daneshwari</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="js/jquery.collapsible.min.js"></script>
    <script type="text/javascript" src="js/swiper.min.js"></script>
    <script type="text/javascript" src="js/jquery.countdown.min.js"></script>
    <script type="text/javascript" src="js/circle-progress.min.js"></script>
    <script type="text/javascript" src="js/jquery.countTo.min.js"></script>
    <script type="text/javascript" src="js/custom.js"></script>
    <script>
    // script for countdown
      // Set the date we're counting down to
      var countDownDate = new Date("July 30, 2020 23:59:59").getTime();

      // Update the count down every 1 second
      var x = setInterval(function () {
        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor(
          (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("dday").innerHTML = days;
        document.getElementById("dhour").innerHTML = hours;
        document.getElementById("dmin").innerHTML = minutes;
        document.getElementById("dsec").innerHTML = seconds;

        // If the count down is over, write some text
        if (distance < 0) {
          clearInterval(x);
          document.getElementById("demo").innerHTML = "EXPIRED";
        }
      }, 1000);
    </script>
    <script>
    // script for particle js
      particlesJS("particles-js", {
        particles: {
          number: { value: 80, density: { enable: true, value_area: 800 } },
          color: { value: "#25ffbf" },
          shape: {
            type: "star",
            stroke: { width: 0, color: "#25ffbf" },
            polygon: { nb_sides: 5 },
            image: { src: "img/github.svg", width: 100, height: 100 },
          },
          opacity: {
            value: 0.7,
            random: false,
            anim: { enable: false, speed: 1, opacity_min: 0.1, sync: false },
          },
          size: {
            value: 2,
            random: true,
            anim: { enable: false, speed: 40, size_min: 0.1, sync: false },
          },
          line_linked: {
            enable: true,
            distance: 150,
            color: "#ffffff",
            opacity: 0.4,
            width: 1,
          },
          move: {
            enable: true,
            speed: 6,
            direction: "none",
            random: false,
            straight: false,
            out_mode: "out",
            bounce: false,
            attract: { enable: false, rotateX: 600, rotateY: 1200 },
          },
        },
        interactivity: {
          detect_on: "canvas",
          events: {
            onhover: { enable: true, mode: "grab" },
            onclick: { enable: true, mode: "push" },
            resize: true,
          },
          modes: {
            grab: { distance: 400, line_linked: { opacity: 1 } },
            bubble: {
              distance: 400,
              size: 40,
              duration: 2,
              opacity: 8,
              speed: 3,
            },
            repulse: { distance: 200, duration: 0.4 },
            push: { particles_nb: 4 },
            remove: { particles_nb: 2 },
          },
        },
        retina_detect: true,
      });
      var count_particles, stats, update;
      stats = new Stats();
      stats.setMode(0);
      stats.domElement.style.position = "absolute";
      stats.domElement.style.left = "0px";
      stats.domElement.style.top = "0px";
      document.body.appendChild(stats.domElement);
      count_particles = document.querySelector(".js-count-particles");
      update = function () {
        stats.begin();
        stats.end();
        if (
          window.pJSDom[0].pJS.particles &&
          window.pJSDom[0].pJS.particles.array
        ) {
          count_particles.innerText =
            window.pJSDom[0].pJS.particles.array.length;
        }
        requestAnimationFrame(update);
      };
      requestAnimationFrame(update);
    </script>

    <!-- Mobile Optimization & Touch Interactions -->
    <script>
      $(document).ready(function() {
        // Mobile menu enhancement
        let isMenuOpen = false;
        
        $('#mobileMenuBtn').on('click', function() {
          isMenuOpen = !isMenuOpen;
          const menu = $('#mobileMenu');
          const icon = $(this).find('i');
          
          if (isMenuOpen) {
            menu.slideDown(300).css('display', 'block');
            icon.removeClass('fa-bars').addClass('fa-times');
            $(this).addClass('menu-open');
          } else {
            menu.slideUp(300);
            icon.removeClass('fa-times').addClass('fa-bars');
            $(this).removeClass('menu-open');
          }
        });
        
        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
          if (isMenuOpen && !$(e.target).closest('.navbar-3d').length) {
            $('#mobileMenu').slideUp(300);
            $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
            $('#mobileMenuBtn').removeClass('menu-open');
            isMenuOpen = false;
          }
        });
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
          e.preventDefault();
          const target = $(this.getAttribute('href'));
          if (target.length) {
            $('html, body').animate({
              scrollTop: target.offset().top - 80
            }, 1000);
          }
          
          // Close mobile menu if open
          if (isMenuOpen) {
            $('#mobileMenu').slideUp(300);
            $('#mobileMenuBtn i').removeClass('fa-times').addClass('fa-bars');
            $('#mobileMenuBtn').removeClass('menu-open');
            isMenuOpen = false;
          }
        });
        
        // Touch interactions for cards
        $('.card-3d, .event-card-3d, .sponsor-card-3d').on('touchstart', function() {
          $(this).addClass('touch-active');
        }).on('touchend', function() {
          const self = $(this);
          setTimeout(() => self.removeClass('touch-active'), 150);
        });
        
        // Optimize images for mobile
        function optimizeImages() {
          if (window.innerWidth <= 768) {
            $('img[data-mobile]').each(function() {
              $(this).attr('src', $(this).data('mobile'));
            });
          }
        }
        
        // Lazy loading for better performance
        function lazyLoad() {
          const images = document.querySelectorAll('img[data-src]');
          const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
              if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
              }
            });
          });
          
          images.forEach(img => imageObserver.observe(img));
        }
        
        // Viewport height fix for mobile browsers
        function setVH() {
          let vh = window.innerHeight * 0.01;
          document.documentElement.style.setProperty('--vh', `${vh}px`);
        }
        
        setVH();
        window.addEventListener('resize', setVH);
        window.addEventListener('orientationchange', setVH);
        
        // Preload critical resources
        function preloadCritical() {
          const criticalLinks = [
            'participantformnew.php',
            'events.php',
            'plogin.php'
          ];
          
          criticalLinks.forEach(link => {
            const linkElement = document.createElement('link');
            linkElement.rel = 'prefetch';
            linkElement.href = link;
            document.head.appendChild(linkElement);
          });
        }
        
        // Performance monitoring
        function trackPerformance() {
          if ('performance' in window) {
            window.addEventListener('load', () => {
              const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
              console.log('Page load time:', loadTime + 'ms');
              
              // Track if page loads fast on mobile
              if (window.innerWidth <= 768 && loadTime > 3000) {
                console.warn('Slow mobile performance detected');
              }
            });
          }
        }
        
        // Initialize optimizations
        optimizeImages();
        lazyLoad();
        preloadCritical();
        trackPerformance();
        
        // Swipe gestures for mobile navigation
        let startX = 0;
        let startY = 0;
        
        document.addEventListener('touchstart', function(e) {
          startX = e.touches[0].clientX;
          startY = e.touches[0].clientY;
        });
        
        document.addEventListener('touchend', function(e) {
          if (!startX || !startY) return;
          
          const endX = e.changedTouches[0].clientX;
          const endY = e.changedTouches[0].clientY;
          
          const diffX = startX - endX;
          const diffY = startY - endY;
          
          // Swipe threshold
          if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 100) {
            if (diffX > 0) {
              // Swipe left - could trigger next section
              console.log('Swipe left detected');
            } else {
              // Swipe right - could trigger previous section
              console.log('Swipe right detected');
            }
          }
          
          startX = 0;
          startY = 0;
        });
        
        // Adaptive loading based on connection
        if ('connection' in navigator) {
          const connection = navigator.connection;
          
          if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
            // Disable particles and heavy animations on slow connections
            document.getElementById('particles-js').style.display = 'none';
            document.body.classList.add('reduced-motion');
          }
        }
      });
    </script>

    <!-- Additional CSS for mobile enhancements -->
    <style>
      .touch-active {
        transform: translateY(-5px) scale(0.98) !important;
        transition: transform 0.1s ease !important;
      }
      
      .menu-open {
        background: var(--gradient-primary) !important;
        color: white !important;
      }
      
      /* Use viewport units with fallback */
      .hero-3d {
        min-height: 100vh;
        min-height: calc(var(--vh, 1vh) * 100);
      }
      
      /* Improved touch targets */
      @media (max-width: 768px) {
        .nav-link-3d,
        .btn-modern,
        .social-icon-3d {
          min-height: 44px;
          min-width: 44px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }
      
      /* Reduced motion class */
      .reduced-motion * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
      
      /* Lazy loading placeholder */
      img.lazy {
        opacity: 0;
        transition: opacity 0.3s;
      }
      
      img.lazy.loaded {
        opacity: 1;
      }
    </style>
  </body>
</html>
