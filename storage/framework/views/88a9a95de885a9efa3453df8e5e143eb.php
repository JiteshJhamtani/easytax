<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7fa; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #1e9c5d; color: #ffffff; padding: 30px 20px; text-align: center; }
        .content { padding: 30px; color: #333333; line-height: 1.6; font-size: 16px; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; color: #888888; font-size: 12px; border-top: 1px solid #eeeeee; }
        .data-box { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">Application Completed!</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Great news! Your application for <strong><?php echo e($application->service->name ?? 'our service'); ?></strong> has been successfully processed and marked as Completed.</p>
            
            <div class="data-box">
                <strong>Application ID:</strong> #<?php echo e($application->id); ?><br>
                <strong>Completed On:</strong> <?php echo e(now()->format('F j, Y')); ?><br>
                <strong>Agent:</strong> <?php echo e($application->agent->name ?? 'EasyTax Representative'); ?>

            </div>

            <p>If there are any final documents (like a GST Certificate or Receipt), your agent will be providing them to you shortly.</p>
            
            <p>Thank you for choosing EasyTax!</p>
        </div>
        <div class="footer">
            &copy; <?php echo e(date('Y')); ?> EasyTax. All rights reserved.
        </div>
    </div>
</body>
</html><?php /**PATH /var/www/uat.easytax.live/resources/views/emails/application_completed.blade.php ENDPATH**/ ?>