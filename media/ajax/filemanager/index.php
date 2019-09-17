<?php
$dirName = dirname(__FILE__);
header("Cache-control: no-store,max-age=0");
header("Content-type: text/html; charset=UTF-8");
include 'bootstrap.php';
include 'php/action.php';


// Создаем корневую директорию скрипта
$dirName = sscanf($dirName,DOCROOT.'%s');
$dirName = '/'.str_replace('\\','/',$dirName[0]);

/*
if(isset($_POST['image'])){
    echo 1;
    exit();
}*/

//echo $dirName;
/*Инициализация*/
if(isset($_POST['root'])){
    $root = trim($_POST['root'],'/').'/';
    $cononical = $root;
}

if((isset($_POST['dir']) AND !empty($_POST['dir'])) OR !empty($_POST['start'])){
    
    if($_POST['start']){
        $categories = Module::factory('categories',TRUE);
        $path = $categories->get_path_image(array('product_id' =>intval($_POST['start'])));
        if(!empty($path)){
            if(empty($_POST['dir'])){
                $_POST['dir'] = $path;
            }
        }
    }
    $dir = trim($_POST['dir'],'/').'/';
    $cononical .= $dir;
    // создаем кнопку назад
    $back = explode('/',trim($dir,'/'));
    array_pop($back);
    if(!empty($back))
        $back = implode('/',$back).'/';
    else
        $back = '';
}


$folders = GetFolders($cononical);
$files = GetFiles($cononical,0);
if(!empty($files)){
    $files = array_reverse($files);
}
/*
<div id="manager">
    <link rel="stylesheet" type="text/css" href="/admin/media/js/filemanager/css/filemanager.css"/>
</div>
*/
?>
<script type="text/javascript">

</script>
<style type="text/css">
#filemanager{
    position:relative;
}
#filemanager .name{
    padding:3px;
}
#filemanager .folder{
    background-color: #FFCC66;
    border: 1px solid #FFCC66;
    color: #FFFFFF;
    float: left;
    font-size: 12px;
    margin-bottom: 6px;
    margin-right: 6px;
    text-align: center;
}
#filemanager .folder:hover{
    background-color: #FF9900;
}
#filemanager .folder .image {
    background-color: #F6F9FB;
    cursor: pointer;
    height: 115px;
    padding-top: 5px;
    width: 120px;
    margin:auto;
}
#filemanager .folder .image img{
    margin-top: 39px;
}
#filemanager .images{
    background-color: #A8ADB4;
    border: 1px solid #FFCC66;
    color: #FFFFFF;
    float: left;
    font-size: 8pt;
    margin-bottom: 6px;
    margin-right: 6px;
    text-align: center;
}

#filemanager .images:hover{
    background-color: #1C3B51;
}
#filemanager .images .image{
    background-color: #F6F9FB;
    cursor: pointer;
    height: 115px;
    padding-top: 5px;
    width: 120px;
    overflow:hidden;
}
#filemanager .images .image img{
   display:block;
   margin:auto;
}
#filemanager .add .name{
   background-color: #769E1E;
}
#filemanager_top{
    height:22px;
    left:-10px;
    right:-10px;
    top:-23px;
    position:absolute;
    background: url("<?=$dirName?>/img/horizontal.gif") repeat 0 -23px;
}
#filemanager_top .batton{
    background: url("<?=$dirName?>/img/buttons.gif") no-repeat -87px -16px;
    width:29px;
    height:16px;
    position:absolute;
    right:6px;
    top:3px;
}
#filemanager_top .batton:hover{
    background: url("<?=$dirName?>/img/buttons.gif") no-repeat -87px -32px;
    cursor:pointer;
}
.toolbar {
    background: url("<?=$dirName?>/img/bg.png") repeat-x scroll 0 0 #F7F7F7;
    height: 33px;
    padding-left: 20px;
    width:100%;
    margin-left:-10px;
    margin-top:-10px;
}
.toolbar a {
    border-color: rgba(0, 0, 0, 0);
    border-style: solid;
    border-width: 1px;
    display: block;
    height: 24px;
    padding: 3px;
    text-decoration: none;
    padding-top:5px;
    cursor:pointer;
    border-bottom:1px solid #000;
}
#add_image{
    display:none;
}
</style>
<div id="filemanager">
    <div id="filemanager_top">
        <div class="batton" onclick="filemanager.drop()"></div>
    </div>
    <div class="toolbar">
        <a id="add_image">Добавить выбраные</a>
    </div>
    <div class="box">
    <?php
    if(isset($back)):
    ?>
        <div onclick="filemanager.getFolder('<?=$back?>')" class="folder">
            <div class="image">
                <img src="<?=$dirName?>/img/icon_folder_back_32x32.gif" />
            </div>
            <div class="name">Вернутся</div>
        </div>
    <?php
    endif;
    ?>
    
    <?php
    if(!empty($folders)):
    foreach($folders as $folder):
    ?>
        <div onclick="filemanager.getFolder('<?=$dir.$folder?>')" class="folder">
            <div class="image">
                <img src="<?=$dirName?>/img/icon_folder_32x32.gif" />
            </div>
            <div class="name"><?=$folder?></div>
        </div>
    <?php
    endforeach;
    endif;
    ?>
    <?php
    if(!empty($files)):
    foreach($files as $file):
    ?>
        <div class="images" onclick="filemanager.getFile('<?=$cononical.$file?>','<?=$file?>','<?=($size = @filesize($cononical.$file))? $size: 0?>')">
            <div class="image">
                <img class="get" src="<?=$cononical.$file?>" height="100%" />
            </div>
            <div class="name"><?=$file?></div>
            <input class="input_image" type="hidden" value="<?=$file?>" />
            <input class="input_cononical" type="hidden" value="<?=$cononical?>" />
            <input class="input_dir" type="hidden" value="<?=$dir?>" />
        </div>
    <?php
    endforeach;
    endif;
    ?>
    </div>
</div>
<div class="submit_image">Добавить</div>
<script type="text/javascript">

</script>