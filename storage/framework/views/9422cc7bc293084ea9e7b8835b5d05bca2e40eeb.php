<!DOCTYPE html>
<html>
<head>
    <?php echo app('Illuminate\Foundation\Vite')('resources/scss/app.scss'); ?>
</head>
<body>
    <div id="app">
        <?php echo $__env->yieldContent('content'); ?>
        <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuhv4VUKB2bOVdjVMp6A-e6URwWZs6E2g&libraries=places"></script>
    </div>
</body>
</html>
<?php /**PATH /Users/apple/Downloads/tasks/lara-vue-m/resources/views/layouts/app.blade.php ENDPATH**/ ?>