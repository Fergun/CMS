<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <script type="text/javascript" src='//cdn.tinymce.com/4/tinymce.min.js'></script>
    <script type="text/javascript">
        tinymce.init({
            selector: '#myTextarea',
            theme: 'modern',
            width: 600,
            height: 300,
            plugins: [
                'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
                'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
                'save table contextmenu directionality emoticons template paste textcolor'
            ],
            content_css: 'css/content.css',
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview media fullpage | forecolor backcolor emoticons'
        });
    </script>
</head>
<body>
<div id="container">
    <?php

require('functions.php');
    if (isset($_POST['query']))
        $query = iconv(mb_detect_encoding($_POST['sql_query']), 'ISO-8859-2', $_POST['sql_query']);

    echo '<form action="" method="post" name="sql" style="margin-bottom:0px" ENCTYPE="multipart/form-data">';

    echo '<div id="message"></div>';
    echo '<input type="text" name="head">';
    echo '<textarea name="query" id="myTextarea">'.$query.'</textarea>';
    echo '<input type="submit" value="Wykonaj" style="display: block; margin-bottom: 20px">';
    echo '</form>';
    echo '<div>';

//    echo $query;
    if ($_POST['query'] || $_POST['head']) {
        $res = $db->query('select a_id from adm_offer order by a_id desc limit 1 ');
        $values = $res->fetch_all(MYSQLI_ASSOC);
        $no = $values[0]['a_id'] + 1;
        $content='\''.iconv(mb_detect_encoding($_POST['query']), 'ISO-8859-2', $_POST['query']).'\'';
        $head='\''.iconv(mb_detect_encoding($_POST['head']), 'ISO-8859-2', $_POST['head']).'\'';
        $sql = 'INSERT INTO adm_offer (a_content,a_id,a_head) VALUES ('.$content.','.$no.','.$head.')';
        $res = $db->query($sql);
        if($res) {
            echo 'Zapisano <br><br>';
            echo $_POST['query'];
            echo $_POST['head'];
            return;
        }
        else {
            echo 'Coś poszło nie tak';
            return;
        }
    }

?>

</div>
</body>
</html>
