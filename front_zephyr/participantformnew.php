<?php
include "config.php";
$csrf_token = generate_csrf_token();

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid request. Please try again.";
    } else {
        // Validate and process form
        $fname = sanitize_input($_POST['fname'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $phone = sanitize_input($_POST['phone'] ?? '');
        
        // Validation
        if (empty($fname)) {
            $errors[] = "First name is required";
        }
        if (!validate_email($email)) {
            $errors[] = "Valid email is required";
        }
        if (!validate_phone($phone)) {
            $errors[] = "Valid 10-digit phone number is required";
        }
        
        if (empty($errors)) {
            // Check if email already exists
            $stmt = $mysqli->prepare("SELECT id FROM participants WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = "Email already registered";
            } else {
                // Insert new participant
                $participant_id = 'ZEP' . strtoupper(substr(uniqid(), -6));
                $stmt = $mysqli->prepare("INSERT INTO participants (participant_id, fname, email, phone, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssss", $participant_id, $fname, $email, $phone);
                
                if ($stmt->execute()) {
                    $success_message = "Registration successful! Your ID: " . $participant_id;
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        }
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registration form - Zephyr Fest</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="modern-3d.css">
    <style>
        .registration-hero {
            background: var(--gradient-dark);
            padding: 120px 0 60px;
            position: relative;
            overflow: hidden;
        }
        
        .registration-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 30% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
            z-index: 1;
        }
        
        .form-container-3d {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 3rem;
            margin: 2rem auto;
            max-width: 800px;
            box-shadow: var(--shadow-3d);
            position: relative;
            z-index: 2;
            transform-style: preserve-3d;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .form-container-3d:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }
        
        .form-header-3d {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .form-title-3d {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .form-subtitle-3d {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .form-group-3d {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .form-input-3d {
            width: 100%;
            padding: 1.2rem 1.5rem 1.2rem 3.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid transparent;
            border-radius: 15px;
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
            transform-style: preserve-3d;
        }
        
        .form-input-3d:focus {
            outline: none;
            border-color: var(--accent-purple);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.2);
        }
        
        .form-input-3d::placeholder {
            color: var(--text-muted);
        }
        
        .input-icon-3d {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-purple);
            font-size: 1.1rem;
            z-index: 1;
        }
        
        .form-label-3d {
            display: block;
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-label-3d .required {
            color: var(--accent-pink);
        }
        
        .submit-btn-3d {
            width: 100%;
            padding: 1.2rem;
            background: var(--gradient-primary);
            border: none;
            border-radius: 15px;
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
        }
        
        .submit-btn-3d::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-secondary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .submit-btn-3d:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4);
        }
        
        .submit-btn-3d:hover::before {
            opacity: 1;
        }
        
        .submit-btn-3d span {
            position: relative;
            z-index: 1;
        }
        
        .alert-3d {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(20px) saturate(180%);
        }
        
        .alert-success-3d {
            border-left: 4px solid var(--accent-cyan);
            color: var(--accent-cyan);
        }
        
        .alert-danger-3d {
            border-left: 4px solid var(--accent-pink);
            color: var(--accent-pink);
        }
        
        .back-btn-3d {
            position: fixed;
            top: 2rem;
            left: 2rem;
            z-index: 1000;
            padding: 0.8rem 1.5rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            color: var(--text-primary);
            text-decoration: none;
            backdrop-filter: blur(20px) saturate(180%);
            transition: all 0.3s ease;
        }
        
        .back-btn-3d:hover {
            transform: translateX(-5px);
            color: var(--text-primary);
            text-decoration: none;
            background: var(--accent-purple);
        }
        
        @media (max-width: 768px) {
            .form-container-3d {
                margin: 1rem;
                padding: 2rem;
            }
            
            .form-title-3d {
                font-size: 2rem;
            }
            
            .back-btn-3d {
                top: 1rem;
                left: 1rem;
            }
        }
        }

        h1 {
            text-align: center;
            color: #666;
            text-shadow: 1px 1px 0px #FFF;
            margin: 50px 0px 0px 0px
        }
        h3 {
            text-align: center;
            color: blue;
            text-shadow: 1px 1px 0px #FFF;
            margin: 50px 0px 0px 0px
        }

        input {
            border-radius: 0px 5px 5px 0px;
            border: 1px solid #eee;
            margin-bottom: 15px;
            width: 75%;
            height: 40px;
            float: left;
            padding: 0px 15px;
        }

        .form-group {
            overflow: inherit;
            clear: both;
        }

        .icon-case {
            width: 35px;
            float: left;
            border-radius: 5px 0px 0px 5px;
            background: #eeeeee;
            height: 42px;
            position: relative;
            text-align: center;
            line-height: 40px;
        }
        

        i {
            color: #555;
            padding-top: 11px;
        }

        .contentform {
            padding: 40px 30px;
        }

        .button-contact {
            background-color: #81BDA4;
            color: #FFF;
            text-align: center;
            width: 100%;
            border: 0;
            padding: 17px 25px;
            border-radius: 0px 0px 5px 5px;
            cursor: pointer;
            margin-top: 40px;
            font-size: 18px;
        }

        .leftcontact {
            width: 49.5%;
            float: left;
            border-right: 1px dotted #CCC;
            box-sizing: border-box;
            padding: 0px 15px 0px 0px;
        }

        .rightcontact {
            width: 49.5%;
            float: right;
            box-sizing: border-box;
            padding: 0px 0px 0px 15px;
        }
        input[type=button]
        {
        background-color:#000080;
        border: none;
        color: white;
        padding: 15px 20px;
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
    </style>

</head>

<body class="transform-gpu">
    <!-- Back to Home Button -->
    <a href="mainpage.php" class="back-btn-3d">
        <i class="fas fa-arrow-left mr-2"></i>Back to Home
    </a>
    
    <!-- Registration Hero Section -->
    <section class="registration-hero">
        <div class="container">
            <div class="form-container-3d floating-element">
                <div class="form-header-3d">
                    <h1 class="form-title-3d">
                        <i class="fas fa-rocket mr-3"></i>Join Zephyr
                    </h1>
                    <p class="form-subtitle-3d">
                        Embark on an extraordinary journey of creativity and innovation
                    </p>
                </div>
                
                <!-- Display errors -->
                <?php if (!empty($errors)): ?>
                    <div class="alert-3d alert-danger-3d">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Please fix the following issues:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Display success message -->
                <?php if ($success_message): ?>
                    <div class="alert-3d alert-success-3d">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Modern 3D Registration Form -->
                <form action="" method="post" id="registrationForm" class="needs-validation" novalidate>

<div class="contentform">

    <div class="leftcontact">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-3d">
                                <label class="form-label-3d">
                                    First Name <span class="required">*</span>
                                </label>
                                <div class="position-relative">
                                    <i class="fas fa-user input-icon-3d"></i>
                                    <input type="text" name="fname" id="f-name" class="form-input-3d" required 
                                           value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : ''; ?>"
                                           pattern="[A-Za-z\s]{2,50}" 
                                           placeholder="Enter your first name" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group-3d">
                                <label class="form-label-3d">Last Name</label>
                                <div class="position-relative">
                                    <i class="fas fa-user-tag input-icon-3d"></i>
                                    <input type="text" name="lname" id="l-name" class="form-input-3d" 
                                           value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : ''; ?>"
                                           pattern="[A-Za-z\s]{0,50}" 
                                           placeholder="Enter your last name" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group-3d">
                        <label class="form-label-3d">
                            Email Address <span class="required">*</span>
                        </label>
                        <div class="position-relative">
                            <i class="fas fa-envelope input-icon-3d"></i>
                            <input type="email" name="email" id="email" class="form-input-3d" required 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   placeholder="your.email@example.com" />
                        </div>
                    </div>

                    <div class="form-group-3d">
                        <label class="form-label-3d">
                            Phone Number <span class="required">*</span>
                        </label>
                        <div class="position-relative">
                            <i class="fas fa-phone input-icon-3d"></i>
                            <input type="tel" name="phone" id="phone" class="form-input-3d" required 
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                   pattern="[0-9]{10}" 
                                   placeholder="10-digit phone number" />
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-3d">
                                <label class="form-label-3d">College/University</label>
                                <div class="position-relative">
                                    <i class="fas fa-university input-icon-3d"></i>
                                    <input type="text" name="college" id="college" class="form-input-3d" 
                                           value="<?php echo isset($_POST['college']) ? htmlspecialchars($_POST['college']) : ''; ?>"
                                           placeholder="Your institution name" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group-3d">
                                <label class="form-label-3d">Course/Field</label>
                                <div class="position-relative">
                                    <i class="fas fa-graduation-cap input-icon-3d"></i>
                                    <input type="text" name="course" id="course" class="form-input-3d" 
                                           value="<?php echo isset($_POST['course']) ? htmlspecialchars($_POST['course']) : ''; ?>"
                                           placeholder="Your field of study" />
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn-3d glow-effect">
                        <span>
                            <i class="fas fa-rocket mr-2"></i>
                            Launch My Journey
                        </span>
                    </button>

        

        
        <div class="form-group">
            <p>Phone number <span></span></p>
            <span class="icon-case"><i class="fa fa-phone"></i></span>
            <input name="pnumber" id="phone" />
        </div>
        

        

    </div>

    <div class="rightcontact">
        
        
        <div class="form-group">
            <p>Gender <span>*</span></p>
            <select class="form-control" id="gender" name="gender" required style="width: auto;">
                <option>Male</option>
                <option>Female</option>
                <option>Other</option>
            </select>
        </div>
        <div class="form-group">
            <p>Course <span>*</span></p>
            <select class="form-control" id="course" required name="course" style="width: 81%;">
                <option>B.Tech</option>
                <option>M.Tech</option>
                <option>P.hd</option>
                <option>BA/MA</option>
                <option>Other</option>
            </select><br>
        </div>
        <div class="form-group">
            <p>College Name <span>*</span></p>
            <span class="icon-case"><i class="fa fa-building-o"></i></span>
            <input type="text" name="cname" id="c-name" required/>
            
        </div>
        <div class="form-group">
            <p>College City <span>*</span></p>
            <input type="text" class="form-control" id="city" name="ccity" required style="width: 81%;">
                
        </div>

        <div class="form-group">
            <p>College State <span></span></p>
            <select class="form-control" id="state" name="cstate" style="width: 81%;">
            <option value="Andhra Pradesh">Andhra Pradesh</option>
            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
            <option value="Assam">Assam</option>
            <option value="Bihar">Bihar</option>
            <option value="Chandigarh">Chandigarh</option>
            <option value="Chhattisgarh">Chhattisgarh</option>
            <option value="Dadar and Nagar Haveli">Dadar and Nagar Haveli</option>
            <option value="Daman and Diu">Daman and Diu</option>
            <option value="Delhi">Delhi</option>
            <option value="Lakshadweep">Lakshadweep</option>
            <option value="Puducherry">Puducherry</option>
            <option value="Goa">Goa</option>
            <option value="Gujarat">Gujarat</option>
            <option value="Haryana">Haryana</option>
            <option value="Himachal Pradesh">Himachal Pradesh</option>
            <option value="Jammu and Kashmir">Jammu and Kashmir</option>
            <option value="Jharkhand">Jharkhand</option>
            <option value="Karnataka">Karnataka</option>
            <option value="Kerala">Kerala</option>
            <option value="Madhya Pradesh">Madhya Pradesh</option>
            <option value="Maharashtra">Maharashtra</option>
            <option value="Manipur">Manipur</option>
            <option value="Meghalaya">Meghalaya</option>
            <option value="Mizoram">Mizoram</option>
            <option value="Nagaland">Nagaland</option>
            <option value="Odisha">Odisha</option>
            <option value="Punjab">Punjab</option>
            <option value="Rajasthan">Rajasthan</option>
            <option value="Sikkim">Sikkim</option>
            <option value="Tamil Nadu">Tamil Nadu</option>
            <option value="Telangana">Telangana</option>
            <option value="Tripura">Tripura</option>
            <option value="Uttar Pradesh">Uttar Pradesh</option>
            <option value="Uttarakhand">Uttarakhand</option>
            <option value="West Bengal">West Bengal</option>
            </select>
            <br>
            
        </div>
        

        
        <div class="form-group">
            <p>Password <span>*</span></p>
            <span class="icon-case"><i class="fa fa-lock"></i></span>
            <input type="password" name="pw" id="password" required />
        </div>

    </div>
</div>
<button type="submit" name="sub" class="button-contact">Submit</button>
        </form>
        <!-- form ends -->
    </div>
    <?php
    // making connection
        include "linc.php";
        if(isset($_POST["sub"]))
        {
            //taking all variables from form
           
            $fname=mysqli_real_escape_string($mysqli,$_POST["fname"]);
            $mname=mysqli_real_escape_string($mysqli,$_POST["mname"]);
            $lname=mysqli_real_escape_string($mysqli,$_POST["lname"]);
            $gender=mysqli_real_escape_string($mysqli,$_POST["gender"]);
            $course=mysqli_real_escape_string($mysqli,$_POST["course"]);
            $email=mysqli_real_escape_string($mysqli,$_POST["email"]);
            $pnumber=mysqli_real_escape_string($mysqli,$_POST["pnumber"]);
            $cname=mysqli_real_escape_string($mysqli,$_POST["cname"]);
            $ccity=mysqli_real_escape_string($mysqli,$_POST["ccity"]);
            $cstate=mysqli_real_escape_string($mysqli,$_POST["cstate"]);
            $password=mysqli_real_escape_string($mysqli,$_POST["pw"]);

            $querycheck="select p_id from participants where emailid='$email'";
            $resultcheck=mysqli_query($mysqli,$querycheck);
            //to check whether already registered mail or not
            if(mysqli_num_rows($resultcheck)==0)
            {
                //insertion query
                $query="insert into participants (fname,mname,lname,gender,course,emailid,pnumber,cname,ccity,cstate,password) VALUES ('$fname','$mname','$lname','$gender','$course','$email','$pnumber','$cname','$ccity','$cstate','$password')";
               // $query="insert into participants (fname,mname,lname,gender,course,emailid,pnumber,cname,ccity,cstate,password) VALUES ('$fname','$mname','$lname','$gender','$course','$email','$pnumber','$cname','$ccity','$cstate','$password')";
                 if (!mysqli_query($mysqli,$query))
                {
                    echo "query error:".mysqli_error($mysqli);
                    die();
                }
                //echo "submitted successfully<br>";
                 $query2="select p_id from participants where emailid='$email'";
                 $result2=mysqli_query($mysqli,$query2);
                if(!$result2)
                {
                    echo "Query error:".mysli_error($mysqli);
                    die();
                }
                else
                {

                };
//echo "Your participant id is ";
                while($row=mysqli_fetch_assoc($result2))
                {   //displaying ID
                     echo "<h3>Submitted successfully.Your participant id is  ".$row["p_id"]."<br></h3>";
                    
                     
                }
                                        //echo "<hr>";

            }else{
                //displaying error
                echo '<script>alert("This email id is already registered.")</script>';
              // echo "<h3>This email id is already registered</h3>";
            }
            
        };

        ?>       
</body>

</html>