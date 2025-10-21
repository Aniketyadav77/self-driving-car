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
              <i class="fas fa-music mr-2"></i>ZEPHYR
            </h2>
            <small class="text-secondary d-block">School of Engineering, JNU</small>
          </div>
          
          <div class="d-none d-lg-flex align-items-center">
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
              <span><i class="fas fa-rocket mr-2"></i>Register Now</span>
            </a>
          </div>
          
          <!-- Mobile Menu Button -->
          <button class="btn btn-secondary d-lg-none" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
          </button>
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
<!-- sponsors begin -->
      <div id="sponsor">
      <div class="row">
        <div class="col-12">
          <div class="the-complete-lineup">
            <div class="entry-title" style="margin-bottom: 40px; text-align: center;">
              <p>Diamond</p>
              <h2>SPONSORS</h2>
            </div>
            <div class="container">
              <div class="row">
                <div class="col-sm-12 col-md-4">
                  <div class="card">
                    <img
                      class="card-img-top"
                      src="images/google.jpg"
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
                      src="images/facebook.png"
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
                      src="images/tesla.png"
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

    <footer class="site-footer">
      <div
        class="footer-cover-title flex justify-content-center align-items-center"
      >
        <h2>ZEPHYR</h2>
      </div>

      <div class="footer-content-wrapper">
        <div class="container">
          <div class="row">
            <div class="col-12">
              <div class="entry-title">
                <a href="#">ZEPHYR</a>
              </div>

              <div class="entry-mail">
                <a href="#">SAYHELLO@zephyr.COM</a>
              </div>

              <div class="copyright-info">
                
                <script>
                  document.write(new Date().getFullYear());
                </script>
                
                <i class="fa fa-heart" aria-hidden="true"></i> by
                <a href="mainpage.php" target="_blank">RITIKA and DANESHWARI</a>
              </div>

              <div class="footer-social">
                <ul class="flex justify-content-center align-items-center">
                  <li>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                  </li>
                  <li>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                  </li>
                  <li>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                  </li>
                  <li>
                    <a href="#"><i class="fab fa-dribbble"></i></a>
                  </li>
                  <li>
                    <a href="#"><i class="fab fa-behance"></i></a>
                  </li>
                  <li>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                  </li>
                </ul>
              </div>
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
  </body>
</html>
