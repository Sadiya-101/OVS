<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College Online Voting System</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            box-sizing: border-box;
            overflow-x: hidden;
        }
        /* Video background styles */
        .video-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            z-index: -2;
        }
        .overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(120deg, rgba(67,206,162,0.3) 0%, rgba(185,147,214,0.3) 100%);
            z-index: -1;
        }
        header {
            background: rgba(24, 114, 51, 0.92);
            color: #fff;
            padding: 22px 40px 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 16px rgba(24,90,157,0.18);
            border-bottom-left-radius: 18px;
            border-bottom-right-radius: 18px;
        }
        header h1 {
            margin: 0;
            font-size: 2.2rem;
            letter-spacing: 2px;
            font-family: 'Segoe UI', Arial, sans-serif;
            text-shadow: 2px 2px 8px #43cea2;
        }
        nav {
            display: flex;
            gap: 28px;
        }
        nav a {
            color: #ffe066;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.08rem;
            padding-bottom: 2px;
            border-bottom: 2px solid transparent;
            transition: border 0.2s, color 0.2s;
        }
        nav a:hover {
            color: #fff;
            border-bottom: 2px solid #ffe066;
        }
        .top-buttons {
            display: flex;
            gap: 12px;
        }
        .top-buttons a {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s, transform 0.2s;
            border: none;
            box-shadow: 0 2px 8px rgba(67,206,162,0.10);
        }
        .top-buttons a:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            color: #ffe066;
            transform: scale(1.07);
        }
        .marquee-container {
            width: 100%;
            overflow: hidden;
            margin-top: 0;
            background: rgba(255,255,255,0.7);
            border-bottom: 2px solid #43cea2;
        }
        .marquee {
            display: block;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            box-sizing: border-box;
            animation: marquee 13s linear infinite;
            font-size: 1.3rem;
            color:rgb(31, 97, 42);
            font-weight: bold;
            padding: 12px 0;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px #43cea2;
        }
        @keyframes marquee {
            0% { transform: translateX(100%);}
            100% { transform: translateX(-100%);}
        }
        .section {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            padding: 80px 0 40px 0;
            background: rgba(255,255,255,0.82);
            margin: 0;
            border-radius: 18px;
            margin: 40px 40px 0 40px;
            box-shadow: 0 4px 24px rgba(67,206,162,0.13);
        }
        .section .text {
            flex: 1 1 350px;
            padding: 40px 40px 40px 60px;
            color: #185a9d;
        }
        .section .text h2 {
            font-size: 2.2rem;
            margin-bottom: 18px;
            color: #185a9d;
            font-weight: bold;
            text-shadow: 0 2px 8px #43cea2;
        }
        .section .text p, .section .text ul, .section .text h3 {
            font-size: 1.08rem;
            line-height: 1.7;
        }
        .section .text ul {
            margin: 12px 0 18px 18px;
        }
        .section .text h3 {
            margin-top: 18px;
            font-size: 1.15rem;
            color: #185a9d;
        }
        .section .image {
            flex: 2 2 620px;
            min-width: 650px;
            min-height: 420px;
            max-width: 420px;
            max-height: 650px;
            margin: 20px 40px 20px 0;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(67,206,162,0.13);
            background-size: cover;
            background-position: center;
            border: 4px solid #fff;
        }
        .home .image {
            background-image: url('college-image.jpg');
        }
        .about .image {
            background-image: url('about-image.jpg');
        }
        .gallery {
            background: rgba(255,255,255,0.93);
            flex-direction: column;
            padding: 40px 0 30px 0;
            margin: 40px 40px 0 40px;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(67,206,162,0.13);
        }
        .gallery h2 {
            color: #185a9d;
            font-size: 2rem;
            margin-bottom: 18px;
            text-align: center;
            text-shadow: 0 2px 8px #43cea2;
        }
        .gallery-images {
            display: flex;
            gap: 28px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0 0 0;
        }
        .gallery-img {
            width: 260px;
            height: 170px;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(67,206,162,0.18);
            object-fit: cover;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 3px solid #fff;
        }
        .gallery-img:hover {
            transform: scale(1.07) rotate(-2deg);
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            border-color: #43cea2;
        }
        .gallery-captions {
            display: flex;
            justify-content: center;
            gap: 28px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .gallery-caption {
            width: 260px;
            text-align: center;
            color: #185a9d;
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .contact {
            background: linear-gradient(120deg, #43cea2 0%, #b993d6 100%);
            color: white;
            padding: 60px 20px 40px 20px;
            text-align: center;
            border-radius: 18px;
            margin: 40px 40px 40px 40px;
            box-shadow: 0 4px 24px rgba(67,206,162,0.13);
        }
        .contact h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 10px #185a9d;
        }
        .contact p {
            font-size: 1.1rem;
            margin-bottom: 10px;
            text-shadow: 1px 1px 3px #43cea2;
        }
        footer {
            text-align: center;
            padding: 18px;
            background: #185a9d;
            color: white;
            font-size: 1.05rem;
            letter-spacing: 1px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
            margin-top: 40px;
        }
        .container {
            background: rgba(255,255,255,0.45); /* to make transparent */
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 38px 32px 32px 32px;
            max-width: 1200px;
            margin: 40px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        @media (max-width: 900px) {
            .section, .gallery, .contact {
                margin: 18px 4vw 0 4vw;
                border-radius: 12px;
            }
            .section {
                flex-direction: column;
                padding: 30px 0 20px 0;
            }
            .section .text {
                padding: 24px;
            }
            .section .image {
                margin: 18px 0 0 0;
            }
            .gallery-images, .gallery-captions {
                flex-direction: column;
                gap: 18px;
            }
        }
        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
                padding: 10px 10px 10px 10px;
            }
            .top-buttons {
                margin-top: 10px;
            }
            .section .text {
                padding: 12px;
            }
            .section .text h2 {
                font-size: 1.2rem;
            }
            .gallery-img, .gallery-caption {
                width: 95vw;
                height: 120px;
            }
        }
    </style>
</head>
<body>

<!-- Video background (replace 'campus-video.mp4' with your own video file) -->
<video class="video-bg" src="campus-video-new.mp4" autoplay muted loop playsinline></video>
<div class="overlay"></div>

<header>
    <h1>AL-AMEEN INSTITUTE OF INFORMATION SCIENCES</h1>
    <nav>
        <a href="#home">Home</a>
        <a href="#about">About Us</a>
        <a href="#gallery">Gallery</a>
        <a href="#contact">Contact</a>
    </nav>
    <div class="top-buttons">
        <a href="voter_login.php">Voter Login</a>
        <a href="admin_login.php">Admin Login</a>
    </div>
</header>

<div class="marquee-container">
    <div class="marquee">
        ✨ Welcome Al-Ameen Institute of information Sciences— Empowering Students for a Brighter Future! ✨
    </div>
</div>

<div id="home" class="section home">
    <div class="text">
        <h2>Welcome to Al-Ameen Institute of Information Sciences</h2>
        <p>Empowering students with knowledge and skills to excel in their careers. Join us in shaping the future.</p>
    </div>
    <div class="image"></div>
</div>

<div id="about" class="section about">
    <div class="text">
        <h2>About Us</h2>
        <p>Our college offers state-of-the-art facilities to ensure the best learning experience.</p>
        <h3>Courses Offered</h3>
        <ul>
            <li>Bachelor of Computer Applications (BCA)</li>
            <li>Bachelor of Business Administration (BBA)</li>
            <li>Bachelor of Commerce (B.Com)</li>
            <li>Master of Computer Applications (MCA)</li>
            <li>Master of Business Administration (MBA)</li>
            <li>Postgraduate Diploma in Data Science</li>
        </ul>
    </div>
    <div class="image"></div>
</div>

<div id="gallery" class="gallery section">
    <h2>Campus Life & Facilities</h2>
    <div class="gallery-images">
        <img src="college-image.jpg" alt="College Main Building" class="gallery-img">
        <img src="about-image.jpg" alt="About College" class="gallery-img">
        <img src="contact-bg.jpg" alt="Campus Life" class="gallery-img">
        <img src="campus-life.jpg" alt="Students Event" class="gallery-img">
    </div>
    <div class="gallery-captions">
        <div class="gallery-caption">Main Building</div>
        <div class="gallery-caption">Student life</div>
        <div class="gallery-caption">Campus</div>
        <div class="gallery-caption">View</div>
    </div>
</div>

<div id="contact" class="contact">
    <h2>Contact Us</h2>
    <p>Phone: +1 234 567 890</p>
    <p>Email: info@AIIS.edu</p>
    <p>Address: hosur road near lal bagh main gate, Bangalore</p>
</div>

<footer>
    <p>&copy; <?= date("Y"); ?> Al-Ameen Institute of Information Sciences. All Rights Reserved.</p>
</footer>

</body>
</html>