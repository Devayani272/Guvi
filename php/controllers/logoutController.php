<html>
  <body>
    <script type="text/javascript">      
      localStorage.clear();
    </script>
  </body>
</html>
<?php $jsString= 'testing';
session_start();
session_unset();
session_destroy();
header('location: ../../login.html');
 ?>

