<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Smart Waste Collection System</title>
  <style>
    body {
      background: linear-gradient(110deg, #eaf7ee 0%, #e3eefd 100%);
      font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
      margin: 0;
      padding: 0;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 54px 18px 48px;
      background-color: #fff;
      box-shadow: 0 2px 16px rgba(29,119,70,0.09);
    }
    .navbar .brand {
      display: flex;
      align-items: center;
      font-size: 1.45em;
      font-weight: bold;
      color: #222;
    }
    .navbar .brand-icon {
      width: 34px; height: 34px;
      margin-right: 12px;
    }
    .navbar .nav-btns {
      gap: 0 18px;
      display: flex;
      align-items: center;
    }
    .navbar a {
      color: #18613f;
      text-decoration: none;
      font-size: 18px;
      padding: 0 14px;
    }
    .signup-btn {
      display: inline-block;
      background: linear-gradient(90deg, #2086d7, #2dcc6b);
      color: #fff;
      padding: 10px 24px;
      border-radius: 7px;
      font-weight: 600;
      font-size: 19px;
      box-shadow: 0 2px 10px rgba(46,186,104,0.1);
      transition: background 0.2s;
      border: none;
    }
    .signup-btn:hover {
      background: linear-gradient(90deg, #146bb3, #26b45d);
    }
    .main-content {
      max-width: 750px;
      margin: 64px auto 0 auto;
      background: rgba(255,255,255,0.7);
      border-radius: 16px;
      text-align: center;
      padding: 80px 36px 60px 36px;
      box-shadow: 0 8px 36px rgba(29,119,70,0.11);
    }
    .main-content .logo {
      width: 74px;
      margin-bottom: 28px;
    }
    .main-content h1 {
      font-size: 2.7em;
      font-weight: 700;
      color: #23373a;
      margin-bottom: 0.2em;
    }
    .main-content h1 .highlight {
      color: #18a05e;
    }
    .tagline {
      margin-top: 15px;
      margin-bottom: 32px;
      color: #455c69;
      font-size: 1.18em;
      font-weight: 500;
    }
    .option-row {
      margin-top: 45px;
      display: flex; 
      justify-content: center;
      gap: 28px;
    }
    .option-row a {
      display: inline-block;
      font-size: 1.11em;
      border-radius: 9px;
      padding: 18px 38px;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 2px 14px rgba(62,191,115,0.11);
      border: none;
      text-decoration: none;
      background: #fff;
      color: #146bb3;
      transition: background 0.18s;
    }
    .option-row a.signup {
      background: linear-gradient(90deg, #2086d7, #2dcc6b);
      color: #fff;
      margin-right: 0;
    }
    .option-row a.signup:hover {
      background: linear-gradient(90deg, #146bb3, #26b45d);
      color: #fff;
    }
    .option-row a.login {
      border: 1.7px solid #2086d7;
      color: #2086d7;
      background: #fff;
    }
    .option-row a.login:hover {
      background: #f3fcf3;
      color: #159c4d;
      border-color: #159c4d;
    }
    @media (max-width:650px) {
      .main-content { padding:30px 7px; }
      .navbar { padding: 11px 11px; }
      .option-row { flex-direction: column; gap:22px;}
      .option-row a { width:100%; }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <span class="brand">
      <img class="brand-icon" src="images/hd-green-recycling-icon-transparent-background-701751695034668v6pjuzunhi.png" alt="logo">
      Smart Waste Collection System
    </span>
    <div class="nav-btns">
      <a href="login.php">Login</a>
      <a class="signup-btn" href="signup.php">Sign Up</a>
    </div>
  </nav>
  <div class="main-content">
    <img class="logo" src="https://cdn-icons-png.flaticon.com/512/3063/3063826.png" alt="waste logo">
    <h1>Smart Waste Collection<br> <span class="highlight">& Reporting System</span></h1>
    <div class="tagline">
      Efficient waste management with real-time reporting, tracking, and incentive systems for a cleaner environment. Join thousands of citizens making a difference.
    </div>
    <div class="option-row">
      <a href="signup.php" class="signup">Get Started - Sign Up</a>
      <a href="login.php" class="login">Already have account? Login</a>
    </div>
  </div>
</body>
</html>

