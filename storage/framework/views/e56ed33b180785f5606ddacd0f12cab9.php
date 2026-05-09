<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found | EasyTax</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8F9FA; display: flex;
            justify-content: center; align-items: center;
            height: 100vh; text-align: center;
        }
        .error-container { max-width: 500px; padding: 2rem; }
        .error-code { font-size: 7rem; font-weight: 800; color: #1E9C5D; margin: 0; line-height: 1; }
        .error-title { font-size: 1.8rem; font-weight: 800; color: #2E3D4E; margin: 1rem 0; }
        .error-desc { color: #7a8799; line-height: 1.6; margin-bottom: 2.5rem; font-size: 1.1rem; }
        .btn-home {
            display: inline-block; padding: 14px 32px;
            background-color: #1E9C5D; color: #ffffff;
            text-decoration: none; font-weight: 600;
            border-radius: 50px; font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(30, 156, 93, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 156, 93, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Looks like you're lost!</h2>
        <p class="error-desc">The page you are looking for doesn't exist, has been moved, or you don't have permission to view it.</p>
        <!-- we have to field in when we add a new agent like his address, mobile and more you first suggest which one details we should ask then i select and then we add filed in his form then tell me which file you need to change and make changes in database all of it  -->
        
<a href="<?php echo e(route('login')); ?>" class="btn-home">Take Me to Login</a>    </div>
</body>
</html><?php /**PATH /var/www/uat.easytax.live/resources/views/errors/404.blade.php ENDPATH**/ ?>