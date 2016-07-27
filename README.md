# SagiView

Sagi view is simple view library like laravel blade.


---------

<code>
 <?php 
 $view = new View(array(
    'view_path' => 'views',
    'dalvik_path' => 'dalviks
 ), 'test'); // this will 'views/test.blade.php'
 
 
 $view->render()->show();
 ?>
</code>