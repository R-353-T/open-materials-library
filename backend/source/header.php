<?php

$header_infos = array(
    "charset"           => get_bloginfo("charset"),
    "description"       => get_bloginfo("description"),
    "body_classes"      => join(" ", get_body_class()),
    "lang_attributes"   => get_language_attributes()
);

?>

<!DOCTYPE html>
<html lang="<?php echo $header_infos["lang_attributes"]; ?>">
<head>
    <!-- meta -->
    <meta charset="<?php echo $header_infos["charset"]; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $header_infos["description"]; ?>">

    <!-- link -->
    <!-- <link rel="icon" type="image/png" href=""> -->
    
    <!-- wp_head -->
    <?php wp_head(); ?>
</head>
<body
    class="<?php echo $header_infos["body_classes"]; ?>"
    style="
        background:  #101010;
        color:  #dedede;
        min-height: 100vh;
        font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
    ">
    <!-- wp_body_open -->
    <?php wp_body_open(); ?>
