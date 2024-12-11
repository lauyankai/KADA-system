<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home Page</title>
    </head>
    <body>
    <h1><?php echo $message; ?></h1>  <!-- Display the message passed from controller -->
    <br>
    <img src="<?php echo $imageUrl; ?>" alt="Image" style="max-width: 65%; height: auto;">
    </body>
</html>

