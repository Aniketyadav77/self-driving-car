<?php
 include "linc.php";
 //checking for session variable
 if (isset($_SESSION['pid']))
 {
  header('Location: processnew.php');
 }
 //checking for button
 if (isset($_POST["sub"]))
 {
 //$pid=$_POST["user"];
 //$pw=$_POST["pw"];
 //taking variables
 $pid= mysqli_real_escape_string($mysqli,$_POST["user"]);
 $pw= mysqli_real_escape_string($mysqli,$_POST["pw"]);
 $querycheck="select p_id from participants where p_id='$pid' and password='$pw'";
 $resultcheck=mysqli_query($mysqli,$querycheck);
 if(mysqli_num_rows($resultcheck)==1)
 { 
   //session_start
   $_SESSION['pid']=$pid;
   //echo $_SESSION['pid'];
   //echo "<script>window.location='processnew.php';</script>";
   header('Location: processnew.php');
 }
 else
 {  //display error
    echo "<script>alert('Invalid credentials for login')</script>";
 }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="modern-3d.css">

    <title>Participant Portal - Zephyr Gateway</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/zephyr-logo.JPG">
    <style>
        .participant-hero {
            background: var(--gradient-dark);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .participant-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 25% 25%, rgba(236, 72, 153, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(34, 197, 94, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            z-index: 1;
        }
        
        .login-container-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 3rem;
            transform-style: preserve-3d;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            z-index: 2;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.3),
                0 12px 24px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .login-container-3d:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 
                0 35px 70px rgba(0, 0, 0, 0.4),
                0 16px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        
        .login-header-3d {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-title-3d {
            background: var(--gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 0 4px 20px rgba(236, 72, 153, 0.3);
        }
        
        .login-subtitle-3d {
            color: var(--text-secondary);
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .back-home-btn {
            position: absolute;
            top: 2rem;
            left: 2rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: 12px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .back-home-btn:hover {
            background: var(--accent-orange);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

a {
  color: #92badd;
  display:inline-block;
  text-decoration: none;
  font-weight: 400;
}

h2 {
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
  display:inline-block;
  margin: 40px 8px 10px 8px; 
  color: #cccccc;
}



/* STRUCTURE */

.wrapper {
  display: flex;
  align-items: center;
  flex-direction: column; 
  justify-content: center;
  width: 100%;
  min-height: 100%;
  padding: 20px;
}

#formContent {
  -webkit-border-radius: 10px 10px 10px 10px;
  border-radius: 10px 10px 10px 10px;
  background: #fff;
  padding: 30px;
  width: 90%;
  max-width: 450px;
  position: relative;
  padding: 0px;
  -webkit-box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
  box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
  text-align: center;
}





/* TABS */

h2.inactive {
  color: #cccccc;
}

h2.active {
  color: #0d0d0d;
  border-bottom: 2px solid #5fbae9;
}



/* FORM TYPOGRAPHY*/

input[type=button]
{
  background-color:#000080;
  border: none;
  color: white;
  padding: 15px 25px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  text-transform: uppercase;
  font-size: 13px;
  -webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
  box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
  margin: 5px 20px 40px 20px;
  -webkit-transition: all 0.3s ease-in-out;
  -moz-transition: all 0.3s ease-in-out;
  -ms-transition: all 0.3s ease-in-out;
  -o-transition: all 0.3s ease-in-out;
  transition: all 0.3s ease-in-out;
}
input[type=button]:hover{
  background-color: #000056;
}

 input[type=submit], input[type=reset]  {
  background-color: #56baed;
  border: none;
  color: white;
  padding: 15px 80px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  text-transform: uppercase;
  font-size: 13px;
  -webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
  box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
  margin: 5px 20px 40px 20px;
  -webkit-transition: all 0.3s ease-in-out;
  -moz-transition: all 0.3s ease-in-out;
  -ms-transition: all 0.3s ease-in-out;
  -o-transition: all 0.3s ease-in-out;
  transition: all 0.3s ease-in-out;
}

 input[type=submit]:hover, input[type=reset]:hover  {
  background-color: #39ace7;
}


input[type=button]:active, input[type=submit]:active, input[type=reset]:active  {
  -moz-transform: scale(0.95);
  -webkit-transform: scale(0.95);
  -o-transform: scale(0.95);
  -ms-transform: scale(0.95);
  transform: scale(0.95);
}

input[type=text] {
  background-color: #98FB98 ;
  border: none;
  color: #0d0d0d;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 5px;
  width: 85%;
  border: 2px solid #f6f6f6;
  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
}

input[type=text]:focus {
  background-color: #FFDAB9;
  border-bottom: 2px solid #5fbae9;
}

input[type=text]:placeholder {
  color: #FF0000;
}
input[type=password] {
  background-color: #98FB98;
  border: none;
  color: #0d0d0d;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 5px;
  width: 85%;
  border: 2px solid #f6f6f6;
  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
  -webkit-border-radius: 5px 5px 5px 5px;
  border-radius: 5px 5px 5px 5px;
}

input[type=password]:focus {
  background-color: #FFDAB9;
  border-bottom: 2px solid #5fbae9;
}

input[type=password]:placeholder {
  color: #FF0000;
}



/* ANIMATIONS */

/* Simple CSS3 Fade-in-down Animation */
.fadeInDown {
  -webkit-animation-name: fadeInDown;
  animation-name: fadeInDown;
  -webkit-animation-duration: 1s;
  animation-duration: 1s;
  -webkit-animation-fill-mode: both;
  animation-fill-mode: both;
}

@-webkit-keyframes fadeInDown {
  0% {
    opacity: 0;
    -webkit-transform: translate3d(0, -100%, 0);
    transform: translate3d(0, -100%, 0);
  }
  100% {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}

@keyframes fadeInDown {
  0% {
    opacity: 0;
    -webkit-transform: translate3d(0, -100%, 0);
    transform: translate3d(0, -100%, 0);
  }
  100% {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}

/* Simple CSS3 Fade-in Animation */
@-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

.fadeIn {
  opacity:0;
  -webkit-animation:fadeIn ease-in 1;
  -moz-animation:fadeIn ease-in 1;
  animation:fadeIn ease-in 1;

  -webkit-animation-fill-mode:forwards;
  -moz-animation-fill-mode:forwards;
  animation-fill-mode:forwards;

  -webkit-animation-duration:1s;
  -moz-animation-duration:1s;
  animation-duration:1s;
}

.fadeIn.first {
  -webkit-animation-delay: 0.4s;
  -moz-animation-delay: 0.4s;
  animation-delay: 0.4s;
}

.fadeIn.second {
  -webkit-animation-delay: 0.6s;
  -moz-animation-delay: 0.6s;
  animation-delay: 0.6s;
}

.fadeIn.third {
  -webkit-animation-delay: 0.8s;
  -moz-animation-delay: 0.8s;
  animation-delay: 0.8s;
}

.fadeIn.fourth {
  -webkit-animation-delay: 1s;
  -moz-animation-delay: 1s;
  animation-delay: 1s;
}

*:focus {
    outline: none;
} 

#icon {
  width:40%;
  hieght:40%
}

    </style>
  </head>
  <body>
    <!-- Back to Home Button -->
    <a href="mainpage.php" class="back-home-btn">
        <i class="fas fa-arrow-left mr-2"></i>Back to Home
    </a>
    
    <div class="participant-hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="login-container-3d floating-element">
                        <div class="login-header-3d">
                            <h1 class="login-title-3d">
                                <i class="fas fa-user-astronaut mr-3"></i>Portal Access
                            </h1>
                            <p class="login-subtitle-3d">
                                Welcome back, space explorer! Enter your credentials to continue your journey.
                            </p>
                        </div>
                        
                        <!-- Zephyr Brand Image -->
                        <div class="text-center mb-4">
                            <div class="brand-image-3d">
                                <i class="fas fa-rocket" style="font-size: 4rem; color: var(--accent-orange); animation: pulse 2s infinite;"></i>
                            </div>
                        </div>
                        
                        <!-- Login Form -->
                        <form action="" method="POST">
                            <div class="text-center mb-4">
                                <h3 class="text-gradient mb-0">Participant Login</h3>
                                <p class="text-secondary">Access your mission dashboard</p>
                            </div>
                            
                            <div class="form-group-3d">
                                <div class="form-input-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <input type="text" class="form-input-3d" name="user" 
                                       placeholder="Enter your Participant ID" required>
                            </div>
                            
                            <div class="form-group-3d">
                                <div class="form-input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" class="form-input-3d" name="pw" 
                                       placeholder="Enter your password" required>
                            </div>
                            
                            <button type="submit" name="sub" class="submit-btn-3d mb-4">
                                <i class="fas fa-rocket mr-2"></i>Launch Mission
                            </button>
                            
                            <div class="text-center">
                                <p class="text-secondary mb-2">Don't have a Participant ID yet?</p>
                                <a href="participantformnew.php" class="nav-link-3d d-inline-block">
                                    <i class="fas fa-user-plus mr-2"></i>Join the Adventure
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
  

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	
  
</body>
</html>