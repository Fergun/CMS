<?php
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html><head>';
echo '<title>'. $title .'</title>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">';
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>';
echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
echo '<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow" rel="stylesheet">';
echo '<link rel="stylesheet" href="style.css?v=1" type="text/css">';
echo '<!-- Include the plugin\'s CSS and JS: -->
      <script type="text/javascript" src="support/js/bootstrap-multiselect.js"></script>
      <link rel="stylesheet" href="support/css/bootstrap-multiselect.css" type="text/css"/>';
echo  '<script>
      $(document).ready(function(){
          // zamiana wszystkich selectow na bootstrap-multiselect
          $(\'select\').multiselect({
              numberDisplayed: 1,
              disableIfEmpty: true,
              enableFiltering: true,
              onDropdownHidden: function(e){
                console.log($(this));
                if($(this)[0].$select.hasClass("refresh")){
                    $("form").submit();   //JAK WIÊCEJ NIZ JEDEN FORM TO ZMIENIÆ        
                }
              }
          });
      });
      </script>';
echo '</head>';