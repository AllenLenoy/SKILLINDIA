<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skill India - Bridging the Education-Employment Gap</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo" style="font-size: 38px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(45deg, #6c5ce7, #00cec9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Skill<span style="font-style: italic; -webkit-text-fill-color: #00cec9; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">India</span></a>
            <div class="nav-links">
                <a href="index.php" class="active nav" style="margin-left: 5px; margin-right: 5px;">Home</a>
                <a href="#courses" class="nav" style="margin-left: 5px; margin-right: 5px;">Courses</a>
                <a href="#about" class="nav" style="margin-left: 5px; margin-right: 5px;">About</a>
                <a href="#contact" class="nav" style="margin-left: 5px; margin-right: 5px;">Contact</a>

                <!-- Navbar conditional rendering based on sessionStorage -->
                <div id="userLinks" style="display: flex; gap: 10px;">
                    <!-- Default Links for Guest Users -->
                    <a href="login.php" class="btn btn-login">Login</a>
                    <a href="signup.php" class="btn btn-signup">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" style="padding-top: 80px !important; padding-bottom: 20px !important;">
        <div class="container">
            <div class="hero-content">
                <h1>Unlock Your Potential with Skill India</h1>
                <p>Bridge the gap between education and employment with our skill development programs and certifications.</p>
                <div class="hero-btns">
                    <a href="<?php echo isset($_SESSION['user_id']) ? 'dashboard.php' : 'signup.php'; ?>" class="btn btn-primary">Get Started</a>
                    <a href="#courses" class="btn btn-secondary">Explore Courses</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="images/img2.png" alt="Skill Development" id="hero-img">
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <?php
            // Fetch stats from database
            $stats = [
                'courses' => 50,
                'students' => 10000,
                'partners' => 500,
                'placement' => 80
            ];
            
            $stmt = $pdo->prepare("SELECT 
                (SELECT COUNT(*) FROM courses) as courses,
                (SELECT COUNT(*) FROM users WHERE role = 'student') as students");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $stats = [
                    'courses' => 90,
                    'students' => 20000,
                    'partners' => 50, // Keeping static value for partners
                    'placement' => 99.8
                ];
            }
            ?>
            <div class="stat-card">
                <h3><?php echo $stats['courses'] ?? 50; ?>+</h3>
                <p>Courses Available</p>
            </div>
            <div class="stat-card">
                <h3><?php echo number_format($stats['students'] ?? 10000); ?>+</h3>
                <p>Students Enrolled</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['partners'] ?? 500; ?>+</h3>
                <p>Industry Partners</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['placement'] ?? 80; ?>%</h3>
                <p>Placement Rate</p>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section class="courses" id="courses">
        <div class="container">
            <h2 class="section-title">Popular Courses</h2>
            <p class="section-subtitle">Enhance your skills with our industry-relevant programs</p>
            
            <div class="course-grid">
                <?php
                // Course title => Image path mapping (keys have no spaces and are lowercase)
                $courseImages = [
                    'fullstack' => 'images/img7.png',
                    'cloudcomputing' => 'images/cloudcomputing.jpg',
                    'datascience' => 'images/datascience.jpg',
                    'artificialintelligence' => 'images/ai.png',
                    'machinelearning' => 'images/machinelearning.png',
                    'cybersecurity' => 'images/cyber.jpg',
                    // Add more mappings here
                ];

                // Fetch popular courses from database
                $stmt = $pdo->prepare("SELECT * FROM courses ORDER BY RAND() LIMIT 3");
                $stmt->execute();
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($courses)) {
                    foreach ($courses as $course):
                        $title = $course['title'];
                        $key = strtolower(str_replace(' ', '', trim($title))); // Normalize title: lowercase, trim, remove all spaces
                        $imagePath = isset($courseImages[$key]) ? $courseImages[$key] : 'images/default.png';
                ?>
                <div class="course-card">
                    <div class="course-img">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($title); ?>">
                    </div>
                    <div class="course-info">
                        <h3><?php echo htmlspecialchars($title); ?></h3>
                        <p><?php echo htmlspecialchars($course['description']); ?></p>
                        <div class="course-meta">
                            <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($course['duration']); ?> hours</span>
                            <span><i class="fas fa-user-graduate"></i> <?php echo ucfirst($course['level']); ?></span>
                        </div>
                        <a href="course-details.php?id=<?php echo $course['course_id']; ?>" class="btn btn-outline">View Details</a>
                    </div>
                </div>
                <?php
                    endforeach;
                } else {
                    echo '<p class="text-center">No courses available at the moment.</p>';
                }
                ?>
            </div> 

            
            <div class="text-center">
                <a href="courses.php" class="btn btn-primary">View All Courses</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-img">
                <img src="images/img4.png" alt="About Skill India">
            </div>
            <div class="about-content">
                <h2 class="section-title">About Skill India</h2>
                <p>Skill India is a platform dedicated to bridging the gap between education and employment by providing industry-relevant skill development programs, certifications, and job opportunities.</p>
                <p>Our mission is to empower individuals with the skills they need to succeed in today's competitive job market.</p>
                <ul class="about-features">
                    <li><i class="fas fa-check-circle"></i> Industry-aligned curriculum</li>
                    <li><i class="fas fa-check-circle"></i> Expert instructors</li>
                    <li><i class="fas fa-check-circle"></i> Hands-on projects</li>
                    <li><i class="fas fa-check-circle"></i> Job placement assistance</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title">Success Stories</h2>
            <p class="section-subtitle">What our students say about us</p>
            
            <div class="testimonial-grid">
                <?php
                // Hardcoded testimonials
                $testimonials = [
                    [
                        'content' => "The courses at Skill India transformed my career. I learned practical skills that helped me land my dream job in web development.",
                        'name' => "Rahul Kumar",
                        'position' => "Full Stack Developer",
                        'image_url' => "images/man1.jpg"
                    ],
                    [
                        'content' => "The instructors are highly knowledgeable and the hands-on projects gave me real-world experience. Highly recommended!",
                        'name' => "Priya Sharma",
                        'position' => "Data Analyst",
                        'image_url' => "images/lady1.jpg"
                    ],
                    [
                        'content' => "Thanks to Skill India's comprehensive training program, I successfully transitioned into a tech career. The support was incredible!",
                        'name' => "Amit Patel",
                        'position' => "Software Engineer",
                        'image_url' => "images/man2.jpg"
                    ]
                ];
                
                foreach ($testimonials as $testimonial):
                ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="<?php echo htmlspecialchars($testimonial['image_url']); ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                        <div>
                            <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                            <p><?php echo htmlspecialchars($testimonial['position']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Transform Your Career?</h2>
            <p>Join thousands of students who have enhanced their skills and secured better opportunities.</p>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'dashboard.php' : 'signup.php'; ?>" class="btn btn-primary">Enroll Now</a>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            <p class="section-subtitle">Have questions? Get in touch with our team</p>
            
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Address</h4>
                            <p>123 Skill Street, Learning City, India 110001</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>+91 9876543210</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>info@skillindia.com</p>
                        </div>
                    </div>
                </div>
                
                <form class="contact-form" method="POST" action="contact-submit.php">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="Subject">
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Skill India</h3>
                    <p>Bridging the gap between education and employment through skill development programs and certifications.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="courses.php">Courses</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="signup.php">Sign Up</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Courses</h3>
                    <ul>
                        <?php
                        // Hardcoded course categories
                        $categories = [
                            'Web Development',
                            'Data Science',
                            'Mobile App Development',
                            'Cloud Computing',
                            'Artificial Intelligence',
                            'Digital Marketing'
                        ];
                        
                        foreach ($categories as $category):
                        ?>
                        <li><a href="courses.php?category=<?php echo urlencode($category); ?>"><?php echo htmlspecialchars($category); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for the latest updates and course offerings.</p>
                    <form class="newsletter-form" method="POST" action="subscribe.php">
                        <input type="email" name="email" placeholder="Your Email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Skill India. All Rights Reserved.</p>
                <div class="footer-links">
                    <a href="privacy.php">Privacy Policy</a>
                    <a href="terms.php">Terms of Service</a>
                    <a href="faq.php">FAQ</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const counters = document.querySelectorAll(".stat-card h3");

            counters.forEach(counter => {
                const isPercent = counter.textContent.includes('%');
                const valueText = counter.textContent.replace(/[^\d.]/g, '');
                const target = parseFloat(valueText);
                let count = 0;
                const duration = 2000;
                const increment = target / (duration / 16); // ~60fps

                function updateCount() {
                    count += increment;
                    if (count < target) {
                        counter.textContent = isPercent
                            ? `${count.toFixed(1)}%`
                            : `${Math.floor(count)}+`;
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.textContent = isPercent
                            ? `${target}%`
                            : `${Math.floor(target)}+`;
                    }
                }

                updateCount();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userLinks = document.getElementById('userLinks');

            // Check if user data is in localStorage
            const user = JSON.parse(localStorage.getItem('userData'));

            // If a user is logged in, display Dashboard and Logout buttons
            if (user && user.user_id) {
                const dashboardLink = document.createElement('a');
                dashboardLink.href = user.role === 'admin' ? 'admin/dashboard.php' : 'dashboard.php';
                
                dashboardLink.textContent = 'Dashboard';
                
                const logoutLink = document.createElement('a');
                logoutLink.href = '#';
                
                logoutLink.textContent = 'Logout';
                logoutLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    localStorage.removeItem('userData'); // Remove user from localStorage
                    window.location.reload(); // Reload the page to reflect changes
                });

                // Clear default guest links and add the new ones
                userLinks.innerHTML = ''; // Remove Login & Signup links
                userLinks.appendChild(dashboardLink);
                userLinks.appendChild(logoutLink);
            }
        });
    </script>


</body>
</html>