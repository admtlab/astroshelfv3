<?php
	session_start();
        $path = '/afs/cs.pitt.edu/projects/vis/visweb/webtest/astroshelf/';
        $format = 'png';
        
        if(isset($_POST['img_data'])){
            $data = $_POST['img_data'];
            $data = str_replace('data:image/png;base64,', '', $data);
            $data = base64_decode($data);
    
            $date = date('Y_m_d_-_H_i_s');
            $file_name = "save_img_".$date.".".$format;
            
            file_put_contents($path.$file_name, $data);
            $_SESSION['save_to_file'] = $file_name;
    
            header('Content-type: image/png');
            $data = file_get_contents($path.$_SESSION['save_to_file']);
               
            echo $data;
            
         }
?>