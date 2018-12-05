<?php
require_once './vendor/autoload.php';

$helperLoader = new SplClassLoader('Helpers', './vendor');
$mailLoader   = new SplClassLoader('SimpleMail', './vendor');

$helperLoader->register();
$mailLoader->register();

use Helpers\Config;
use SimpleMail\SimpleMail;

$config = new Config;
$config->load('./config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['form-name'])) {
    $name    = stripslashes(trim($_GET['form-name']));
    $email   = stripslashes(trim($_GET['form-email']));
    $phone   = stripslashes(trim($_GET['form-phone']));
    $subject = stripslashes(trim($_GET['form-subject']));
    $message = stripslashes(trim($_GET['form-message']));
    $pattern = '/[\r\n]|Content-Type:|Bcc:|Cc:/i';

    if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $subject)) {
        die("Header injection detected");
    }

    $emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($name && $email && $emailIsValid && $subject && $message) {
        $emailSent = true;
    } else {
        $hasError = true;
    }
}
?><!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
    <div class="jumbotron">
        <div class="container">
            <h1>PII Contact Form</h1>
            <p>Give us your information. You can trust us!</p>
        </div>
    </div>
    <?php if(!empty($emailSent)): ?>
        <div class="col-md-6 col-md-offset-3">
            <div class="alert alert-success text-center"><?php echo $config->get('messages.success'); ?></div>
        </div>
    <?php endif; ?>
    <?php if(!empty($hasError)): ?>
        <div class="col-md-5 col-md-offset-4">
            <div class="alert alert-danger text-center"><?php echo $config->get('messages.error'); ?></div>
        </div>
    <?php endif; ?>
    <iframe src="https://evil-third-party.herokuapp.com/"></iframe>
    <div class="col-md-6 col-md-offset-3">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="application/x-www-form-urlencoded" id="contact-form" class="form-horizontal" method="GET">
            <div class="form-group">
                <label for="form-name" class="col-lg-2 control-label"><?php echo $config->get('fields.name'); ?></label>
                <div class="col-lg-10">
                    <input type="text" class="form-control" id="form-name" name="form-name" placeholder="<?php echo $config->get('fields.name'); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="form-email" class="col-lg-2 control-label"><?php echo $config->get('fields.email'); ?></label>
                <div class="col-lg-10">
                    <input type="email" class="form-control" id="form-email" name="form-email" placeholder="<?php echo $config->get('fields.email'); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="form-phone" class="col-lg-2 control-label"><?php echo $config->get('fields.phone'); ?></label>
                <div class="col-lg-10">
                    <input type="tel" class="form-control" id="form-phone" name="form-phone" placeholder="<?php echo $config->get('fields.phone'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="form-subject" class="col-lg-2 control-label"><?php echo $config->get('fields.subject'); ?></label>
                <div class="col-lg-10">
                    <input type="text" class="form-control" id="form-subject" name="form-subject" placeholder="<?php echo $config->get('fields.subject'); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label for="form-message" class="col-lg-2 control-label"><?php echo $config->get('fields.message'); ?></label>
                <div class="col-lg-10">
                    <textarea class="form-control" rows="3" id="form-message" name="form-message" placeholder="<?php echo $config->get('fields.message'); ?>" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <button type="submit" class="btn btn-default"><?php echo $config->get('fields.btn-send'); ?></button>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript" src="public/js/contact-form.js"></script>
    <script type="text/javascript">
        new ContactForm('#contact-form');
    </script>
</body>
</html>
