<?php
$to = $_POST['to'];
$subject = $_POST['subject'];
$txt = $_POST['message'];
$from = $_POST['from'];
$headers = "From: $from" . "\r\n" ;
$files = [];

foreach($_FILES as $file){
    $tmpFilePath = $file['tmp_name'];
    $newFilePath = "temp/" .$file['name'];
    if(move_uploaded_file($tmpFilePath, $newFilePath)) {
        array_push($files, $newFilePath);
    }
}




if(multi_attach_mail($to,$subject,$txt,$from, $files)){
    echo 'OK';
} else {
    echo 'Sorry, something went wrong';
}


/* 
 * Custom PHP function to send an email with multiple attachments 
 * $to Recipient email address 
 * $subject Subject of the email 
 * $message Mail body content 
 * $senderEmail Sender email address 
 * $senderName Sender name 
 * $files Files to attach with the email 
 */
function multi_attach_mail($to, $subject, $message, $senderEmail, $files = array()){ 
    // Sender info  
    $from =" <".$senderEmail.">";  
    $headers = "From: $from"; 
 
    // Boundary  
    $semi_rand = md5(time());  
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
 
    // Headers for attachment  
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";  
 
    // Multipart boundary  
    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
    "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";  
 
    // Preparing attachment 
    if(!empty($files)){ 
        for($i=0;$i<count($files);$i++){ 
            if(is_file($files[$i])){ 
                $file_name = basename($files[$i]); 
                $file_size = filesize($files[$i]); 
                 
                $message .= "--{$mime_boundary}\n"; 
                $fp =    @fopen($files[$i], "rb"); 
                $data =  @fread($fp, $file_size); 
                @fclose($fp); 
                $data = chunk_split(base64_encode($data)); 
                $message .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\n" .  
                "Content-Description: ".$file_name."\n" . 
                "Content-Disposition: attachment;\n" . " filename=\"".$file_name."\"; size=".$file_size.";\n" .  
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
            } 
        } 
    } 
     
    $message .= "--{$mime_boundary}--"; 
    $returnpath = "-f" . $senderEmail; 
     
    // Send email 
    $mail = mail($to, $subject, $message, $headers, $returnpath);  
    foreach($files as $file){
        unlink($file);
    }
    if($mail){ 
        return true; 
    }else{ 
        return false; 
    }
}
?>