<?php
    function GetFolders($dir) {
        $folders = array();
        $dir = iconv('utf-8','cp1251',$dir);
        if ($dh = @opendir($dir)) {
            while($file = readdir($dh)) {
                if (!preg_match("/^\.+$/", $file)) {
                    if (is_dir($dir.$file)) {
                        $folders[] = iconv('cp1251','utf-8',$file); 
                    }
                }
            }
            closedir($dh);
        }

        @sort($folders, SORT_STRING);

        // Server-Cache lцschen
        clearstatcache();

        // Ordner-Array zurьckgeben
        return $folders;
    }
    
    
    function GetFiles($dir, $orderby) {
        $files = array();
        $dir = iconv('utf-8','cp1251',$dir);
        if ($dh = @opendir($dir)) {
            while($file = readdir($dh)) {
                if (!preg_match("/^\.+$/", $file)) {
                    if (is_file($dir.$file)) {
                        $files[0][] = iconv('cp1251','utf-8',$file); ;
                        $files[1][] = filemtime($dir.$file);
                        $files[2][] = filesize($dir.$file);
                    }
                }
            }
            closedir($dh);
        }
        return $files[0];
    }
    
    
    
    /*вспомогательные*/
    // Datei-Erweiterung zurьckgeben
    function GetFileExt($file) {
        $pfad_info = @pathinfo($file);
        return $pfad_info['extension'];
    }

    // Datei-Erweiterung prьfen
    function IsFileExt($file, $ext) {
        if (GetFileExt(strtolower($file)) == strtolower($ext)) { return true; }
        else { return false; }
}
?>