<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Member'; ?> - Lab SE Member</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4A90E2;
            --secondary-color: #68BBE3;
            --sidebar-width: 260px;
            --sidebar-bg: #2C3E50;
            --sidebar-hover: #34495E;
            --topbar-height: 70px;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            /* Prevent FOUC */
            visibility: hidden;
            /* Smooth rendering */
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Mobile Toggle Button */
        .mobile-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .mobile-toggle:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }
        
        .mobile-toggle i {
            font-size: 1.5rem;
        }
        
        /* Backdrop Overlay */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .sidebar-backdrop.active {
            display: block;
            opacity: 1;
        }
        
        /* Sidebar Styles */
        .member-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            transition: transform 0.3s ease, left 0.3s ease;
        }
        
        .member-sidebar .sidebar-header {
            padding: 1.5rem;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }
        
        .member-sidebar .sidebar-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            display: none;
            width: 35px;
            height: 35px;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .member-sidebar .sidebar-close:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .member-sidebar .sidebar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .member-sidebar .sidebar-brand i {
            font-size: 2rem;
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .member-sidebar .sidebar-menu {
            padding: 1rem 0;
        }
        
        .member-sidebar .menu-item {
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .member-sidebar .menu-item:hover {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .member-sidebar .menu-item.active {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .member-sidebar .menu-item i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }
        
        .member-sidebar .menu-label {
            padding: 1.5rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }
        
        .member-sidebar .user-info {
            padding: 1rem 1.5rem;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.1);
            position: sticky;
            bottom: 0;
        }
        
        .member-sidebar .user-info .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 0.75rem;
        }
        
        /* Main Content */
        .member-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
            transition: margin-left 0.3s ease, width 0.3s ease;
        }
        
        /* Top Bar */
        .member-topbar {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            z-index: 10;
            /* Prevent glitch/flicker */
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }
        
        .member-topbar .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
        }
        
        .member-topbar .user-dropdown {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .member-topbar .user-dropdown img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);
            transition: all 0.3s ease;
        }
        
        .member-topbar .user-dropdown img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
        }
        
        /* Default avatar placeholder */
        .user-avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            border: 2px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);
        }
        
        /* Prevent animation glitches */
        .card, .member-topbar, .member-content {
            will-change: auto;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        
        /* Disable problematic animations that cause flicker */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Stabilize AOS animations */
        [data-aos] {
            pointer-events: none;
        }
        
        [data-aos].aos-animate {
            pointer-events: auto;
        }
        
        /* Smooth transitions only */
        .member-topbar, .card {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        /* Cards */
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        /* Tablet Responsive (768px - 1024px) */
        @media (max-width: 1024px) {
            .member-content {
                padding: 1.5rem;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }
        
        /* Mobile Responsive (< 768px) */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 280px;
            }
            
            .mobile-toggle {
                display: flex;
            }
            
            .member-sidebar {
                transform: translateX(-100%);
                left: 0;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }
            
            .member-sidebar.active {
                transform: translateX(0);
            }
            
            .member-sidebar .sidebar-close {
                display: flex;
            }
            
            .member-content {
                margin-left: 0;
                padding: 1rem;
                padding-top: 4.5rem;
                width: 100%;
            }
            
            .member-topbar {
                padding: 1rem;
                border-radius: 8px;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .member-topbar .breadcrumb {
                font-size: 0.875rem;
            }
            
            .member-topbar .user-dropdown {
                width: 100%;
                justify-content: space-between;
            }
            
            /* Make tables scrollable horizontally */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Adjust card padding */
            .card-body {
                padding: 1rem;
            }
            
            /* Stack stat cards */
            .stat-card {
                margin-bottom: 1rem;
            }
            
            /* Adjust font sizes */
            h1 { font-size: 1.75rem; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.25rem; }
            h4 { font-size: 1.1rem; }
            h5 { font-size: 1rem; }
            
            /* Button adjustments */
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
        
        /* Small Mobile (< 480px) */
        @media (max-width: 480px) {
            :root {
                --sidebar-width: 100%;
            }
            
            .member-content {
                padding: 0.75rem;
                padding-top: 4rem;
            }
            
            .member-topbar {
                padding: 0.75rem;
                margin-bottom: 1rem;
            }
            
            .mobile-toggle {
                width: 40px;
                height: 40px;
                top: 10px;
                left: 10px;
            }
            
            .mobile-toggle i {
                font-size: 1.25rem;
            }
            
            h1 { font-size: 1.5rem; }
            h2 { font-size: 1.25rem; }
            
            .btn {
                font-size: 0.8rem;
            }
        }
        
        /* Scrollbar */
        .member-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .member-sidebar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
        }
        
        .member-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .member-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
