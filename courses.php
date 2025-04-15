<?php
require_once 'config/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Courses - Skill India</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #1a1a2e;
        }

        .content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            margin-top: 85px;
        }

        /* Course Grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        /* Course Card */
        .course-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .course-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .course-title {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .course-body {
            padding: 15px;
        }

        .course-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .course-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 13px;
        }

        .meta-item i {
            color: #007bff;
            width: 16px;
        }

        .course-footer {
            padding: 15px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .level-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            background: #e9ecef;
            color: #495057;
        }

        .level-badge.beginner { background: #28a745; color: white; }
        .level-badge.intermediate { background: #ffc107; color: #000; }
        .level-badge.advanced { background: #dc3545; color: white; }

        .btn-enroll {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn-enroll:hover {
            background: #0056b3;
        }

        /* Search and Filter Section */
        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-options {
            display: flex;
            gap: 15px;
        }

        .filter-select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-width: 120px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .filter-options {
                flex-direction: column;
            }
            
            .search-box {
                flex-direction: column;
            }
            
            .filter-select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo" style="font-size: 38px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(45deg, #6c5ce7, #00cec9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Skill<span style="font-style: italic; -webkit-text-fill-color: #00cec9; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">India</span></a>
            <div class="nav-links">
            <a href="index.php" class="nav" style="margin-left: 5px; margin-right: 5px;">Home</a>
            <a href="index.php#courses" class="nav" style="margin-left: 5px; margin-right: 5px;">Courses</a>
            <a href="index.php#about" class="nav" style="margin-left: 5px; margin-right: 5px;">About</a>
            <a href="index.php#contact" class="nav" style="margin-left: 5px; margin-right: 5px;">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="?logout=1" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="signup.php" class="btn btn-signup">Sign Up</a>
                <?php endif; ?>
            </div>

        </div>
    </nav>

    <div class="content">
        <div class="filters">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search courses..." id="searchInput">
            </div>
            <div class="filter-options">
                <select class="filter-select" id="levelFilter">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
                <select class="filter-select" id="durationFilter">
                    <option value="">All Durations</option>
                    <option value="0-2">0-2 Hours</option>
                    <option value="2-5">2-5 Hours</option>
                    <option value="5+">5+ Hours</option>
                </select>
            </div>
        </div>

        <div class="courses-grid" id="coursesGrid">
            <!-- Courses will be loaded here dynamically -->
            <div class="course-card">
                <div class="course-header">
                    <h3 class="course-title">Loading courses...</h3>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Course display and filtering functionality
        const CourseDisplay = {
            courses: [],

            init: async function() {
                await this.loadCourses();
                this.setupEventListeners();
            },

            loadCourses: async function() {
                try {
                    const response = await fetch('api/courses.php');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.courses = data.courses;
                        this.displayCourses(this.courses);
                    } else {
                        this.showError('Failed to load courses');
                    }
                } catch (error) {
                    console.error('Error loading courses:', error);
                    this.showError('Failed to load courses');
                }
            },

            displayCourses: function(courses) {
                const grid = document.getElementById('coursesGrid');
                
                if (!courses || courses.length === 0) {
                    grid.innerHTML = '<div class="no-courses">No courses found</div>';
                    return;
                }

                grid.innerHTML = courses.map(course => `
                    <div class="course-card">
                        <div class="course-header">
                            <h3 class="course-title">${this.escapeHtml(course.title)}</h3>
                        </div>
                        <div class="course-body">
                            <p class="course-description">${this.escapeHtml(course.description || 'No description available.')}</p>
                            <div class="course-meta">
                                <div class="meta-item">
                                    <i class="fas fa-video"></i>
                                    <span>${course.video_count || 0} Videos</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>${course.duration || 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="course-footer">
                            <span class="level-badge ${course.level?.toLowerCase()}">${this.capitalizeFirst(course.level || 'N/A')}</span>
                            <a href="course-details.php?id=${course.course_id}" class="btn-enroll">View Course</a>
                        </div>
                    </div>
                `).join('');
            },

            setupEventListeners: function() {
                const searchInput = document.getElementById('searchInput');
                const levelFilter = document.getElementById('levelFilter');
                const durationFilter = document.getElementById('durationFilter');

                searchInput.addEventListener('input', () => this.filterCourses());
                levelFilter.addEventListener('change', () => this.filterCourses());
                durationFilter.addEventListener('change', () => this.filterCourses());
            },

            filterCourses: function() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const level = document.getElementById('levelFilter').value;
                const duration = document.getElementById('durationFilter').value;

                const filtered = this.courses.filter(course => {
                    const matchesSearch = course.title.toLowerCase().includes(searchTerm) ||
                                        (course.description || '').toLowerCase().includes(searchTerm);
                    const matchesLevel = !level || course.level?.toLowerCase() === level;
                    const matchesDuration = this.filterByDuration(course.duration, duration);

                    return matchesSearch && matchesLevel && matchesDuration;
                });

                this.displayCourses(filtered);
            },

            filterByDuration: function(courseDuration, filterValue) {
                if (!filterValue) return true;
                
                const hours = parseInt(courseDuration);
                if (isNaN(hours)) return false;

                switch(filterValue) {
                    case '0-2': return hours <= 2;
                    case '2-5': return hours > 2 && hours <= 5;
                    case '5+': return hours > 5;
                    default: return true;
                }
            },

            escapeHtml: function(unsafe) {
                return unsafe
                    ? unsafe
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;")
                    : '';
            },

            capitalizeFirst: function(string) {
                return string ? string.charAt(0).toUpperCase() + string.slice(1) : '';
            }
        };

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            CourseDisplay.init();
        });
    </script>
</body>
</html> 